<?php


namespace AppBundle\Service\Monetico;


use Symfony\Component\HttpFoundation\Request;

class BackRequest
{
    /**
     * All request data
     * @var array
     */
    private $params;

    /**
     * Authorization demande date
     * @var \DateTime
     */
    private $date;

    /**
     * TPE number
     * @var integer
     */
    private $ept;

    /**
     * MAC hash
     * @var string
     */
    private $mac;

    /**
     * Payment total
     * @var float
     */
    private $amount;

    /**
     * Order reference
     * @var string
     */
    private $reference;

    /**
     * is authorization given
     * @var boolean
     */
    private $accepted;

    /**
     * annulation information
     * @var string
     */
    private $error;

    /**
     * Payment method (CB, Paypal, leuro, audiotel...)
     * @var string
     */
    private $method;


    /**
     * BackRequest constructor.
     */
    public function __construct(Request $request)
    {
        $this->params = $request->request->all();

        $this->amount = substr($request->get('montant'),0,-3);
        $this->date = \DateTime::createFromFormat('d/m/Y_\a_H:i:s', $request->get('date'));
        $this->accepted = ($request->get('code-retour') == 'paiement' OR $request->get('code-retour') == 'payetest');
        $this->error = $request->get('motifrefus');
        $this->reference = $request->get('reference');
        $this->ept = $request->get('TPE');
        $this->mac = strtolower($request->get('MAC'));
        $this->method = $request->get('modepaiement');
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getEtp()
    {
        return $this->etp;
    }

    /**
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return boolean
     */
    public function isAccepted()
    {
        return $this->accepted;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }


}