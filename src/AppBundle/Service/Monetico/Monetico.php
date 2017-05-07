<?php

namespace AppBundle\Service\Monetico;


use AppBundle\Entity\Bill;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Professional;
use AppBundle\Entity\Subscription;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class Monetico
{

    use ContainerAwareTrait;


    const BACK_RECEIPT = "version=2\ncdr=%s";
    const BACK_MACOK = "0\n";
    const BACK_MACKO = "1\n";
    const GO_MAC_PATTERN = "%s*%s*%s*%s*%s*%s*%s*%s*%s**********";
    const BACK_MAC_PATTERN = "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*";
    const CAPTURE_MAC_PATTERN = "%s*%s*%s%s%s*%s*%s*%s*%s*%s*";
    const URL_PAYMENT = "paiement.cgi";
    const URL_CAPTURE = "capture_paiement.cgi";


    private $key;
    private $ept_number;
    private $version;
    private $url_server;
    private $company_code;


    /**
     * Monetico constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->key = $this->formatKey($config['key']);
        $this->ept_number = $config['ept_number'];
        $this->version = $config['version'];
        $this->url_server = $config['url_server'];
        $this->company_code = $config['company_code'];

    }

    private function formatKey($key)
    {
        $hexStrKey  = substr($key, 0, 38);
        $hexFinal   = "" . substr($key, 38, 2) . "00";

        $cca0=ord($hexFinal);

        if ($cca0>70 && $cca0<97)
            $hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
        else {
            if (substr($hexFinal, 1, 1)=="M")
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            else
                $hexStrKey .= substr($hexFinal, 0, 2);
        }

        return pack("H*", $hexStrKey);
    }

    public function computeMac($pattern, array $fields)
    {
        //dump(vsprintf($pattern, array_values($fields)));
        return strtolower(hash_hmac("sha1", vsprintf($pattern, array_values($fields)), $this->key));
    }

    public function isValidBackMac(BackRequest $back)
    {
        $params = $back->getParams();
        $fields = array(
            $this->ept_number,
            $params["date"],
            $params['montant'],
            $params['reference'],
            $params['texte-libre'],
            $this->version,
            $params['code-retour'],
            $params['cvx'],
            $params['vld'],
            $params['brand'],
            $params['status3ds'],
            $params['numauto'],
            $params['motifrefus'],
            $params['originecb'],
            $params['bincb'],
            $params['hpancb'],
            $params['ipclient'],
            $params['originetr'],
            $params['veres'],
            $params['pares']
        );

        return $back->getMac() == $this->computeMac(Monetico::BACK_MAC_PATTERN, $fields);
    }

    public function subToRef(Subscription $subscription)
    {
        return 'SUB'.$subscription->getId();
    }

    /**
     * @param $ref
     * @return Subscription
     */
    public function refToSub($ref)
    {
        return $this->container->get('doctrine')->getRepository('AppBundle:Subscription')->find(substr($ref,3));
    }

    public function renderPaymentButton(Professional $professional, Subscription $subscription)
    {

        $this->url_ok = 'http://ok'; // TODO
        $this->url_ko = 'http://ko'; // TODO
        $this->url_error = 'http://error'; // TODO

        /**
         * @var $router Router
         */
        $router = $this->container->get('router');
        $url = $router->generate('subscriptions', array(), Router::ABSOLUTE_URL);

        $fields = array(
            'TPE' => $this->ept_number,
            'date' => date("d/m/Y:H:i:s"),
            'montant' => number_format($subscription->getOffer()->getTotalPrice(), 2, '.', '').'EUR',
            'reference' => $this->subToRef($subscription),
            'texte-libre' => 'bonsoir',
            'version' => $this->version,
            'lgue' => 'FR',
            'societe' => $this->company_code,
            'mail' => $professional->getEmail(),
        );

        $mac = $this->computeMac(Monetico::GO_MAC_PATTERN, $fields);

        //dump($mac);

        $html = '
            Redirection vers Monetico...
            <form action="'.$this->url_server.Monetico::URL_PAYMENT.'" method="post" style="display:none;" id="PaymentRequest">
            <input type="hidden" name="version"             id="version"        value="'.$fields['version'].'" />
            <input type="hidden" name="TPE"                 id="TPE"            value="'.$fields['TPE'].'" />
            <input type="hidden" name="date"                id="date"           value="'.$fields['date'].'" />
            <input type="hidden" name="montant"             id="montant"        value="'.$fields['montant'].'" />
            <input type="hidden" name="reference"           id="reference"      value="'.$fields['reference'].'" />
            <input type="hidden" name="MAC"                 id="MAC"            value="'.$mac.'" />
            <input type="hidden" name="url_retour"          id="url_retour"     value="'.$url.'" />
            <input type="hidden" name="url_retour_ok"       id="url_retour_ok"  value="'.$url.'" />
            <input type="hidden" name="url_retour_err"      id="url_retour_err" value="'.$url.'" />
            <input type="hidden" name="lgue"                id="lgue"           value="'.$fields['lgue'].'" />
            <input type="hidden" name="societe"             id="societe"        value="'.$fields['societe'].'" />
            <input type="hidden" name="texte-libre"         id="texte-libre"    value="'.$fields['texte-libre'].'" />
            <input type="hidden" name="mail"                id="mail"           value="'.$fields['mail'].'" />
            <input type="submit" name="bouton"              id="bouton"         value="Payer via le site de la banque"  class="monetico-button"/>
            </form>
        ';

        $autosubmit = '<script type="text/javascript">document.getElementById("PaymentRequest").submit();</script>';

        //return $html;
        return $html.$autosubmit;
    }


    public function createBackReceiptResponse()
    {
        return new Response(sprintf(Monetico::BACK_RECEIPT, Monetico::BACK_MACOK));
    }

    public function createBackReceiptNotOkResponse()
    {
        return new Response(sprintf(Monetico::BACK_RECEIPT, Monetico::BACK_MACKO));
    }

    public function charge(Bill $bill)
    {
        $subscription = $bill->getSubscription();
        $doctrine = $this->container->get('doctrine');

        // SUBSCRIPTION PAYMENTS FLOW

        $total = $subscription->getOffer()->getTotalPrice();
        $chargedAmount = $this->container->get('doctrine')->getRepository('AppBundle:Payment')->getChargedAmount($subscription);
        $amount = $subscription->getOffer()->getPrice();
        $remaining = $total - $chargedAmount - $amount;

        // PAYMENT INIT

        $payment = new Payment();
        $payment
            ->setProvider('MONETICO')
            ->setAmount($amount)
            ->setBill($bill);

        $doctrine->getManager()->persist($payment);

        // CHARGING REQUEST


        $mac_fields = array(
            'TPE' => $this->ept_number,
            'date' => date("d/m/Y:H:i:s"),
            'montant_a_capturer' => number_format($amount, 2, '.', '').'EUR',
            'montant_deja_capture' => number_format($chargedAmount, 2, '.', '').'EUR',
            'montant_restant' => number_format($remaining, 2, '.', '').'EUR',
            'reference' => $this->subToRef($subscription),
            'texte-libre' => 'bonsoir',
            'version' => $this->version,
            'lgue' => 'FR',
            'societe' => $this->company_code,
        );

        $fields = array_merge($mac_fields, array(
            'date_commande' => $subscription->getSubscriptionDate()->format('d/m/Y'),
            'montant' => number_format($total, 2, '.', '').'EUR',
            'MAC' => $this->computeMac(Monetico::CAPTURE_MAC_PATTERN, $mac_fields),
        ));

        $client = new Client();

        $response = $client->request('POST', $this->url_server.Monetico::URL_CAPTURE, [
            'form_params' => $fields
        ]);

        if($response->getStatusCode() != 200) return;

        // RESPONSE ANALYSIS

        $fields = array();
        $rawFields = explode(chr(10), $response->getBody());
        foreach ($rawFields as $rawField)
        {
            $eRawField = explode('=', $rawField);
            if(count($eRawField) != 2) continue;
            $fields[$eRawField[0]] = $eRawField[1];
        }


        $cdr = $fields['cdr']; // Return code 1 = allowed, 0 = deny, -1 = error
        $lib = $fields['lib']; // code label

        if($cdr == 1)
        {
            // PAYMENT DONE

            $payment->setStatus('DONE');
            $bill->setStatus('PAYED');

            $doctrine->getManager()->persist($payment);
            $doctrine->getManager()->persist($bill);

        }
        else
        {
            // PAYMENT FAILED

            $payment->setStatus('FAILED');
            $payment->setError($lib);
            $doctrine->getManager()->persist($payment);
        }

        $doctrine->getManager()->flush();


    }






}



