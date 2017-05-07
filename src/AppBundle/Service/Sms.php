<?php
namespace AppBundle\Service;

use AppBundle\Entity\User;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use GuzzleHttp\Exception\ClientException;
use Ovh\Api;
use Sortable\Fixture\Transport\Engine;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Templating\EngineInterface;

class Sms
{

    use ContainerAwareTrait;

    private $consumerKey;

    private $applicationKey;

    private $applicationSecret;

    /**
     * @var Api
     */
    private $api;

    /**
     * Sms constructor.
     * @param $consumerKey
     * @param $applicationKey
     * @param $applicationSecret
     */
    public function __construct($consumerKey, $applicationKey, $applicationSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->applicationKey = $applicationKey;
        $this->applicationSecret = $applicationSecret;

        $endpoint = 'ovh-eu';

        $this->api = new Api(
            $applicationKey,
            $applicationSecret,
            $endpoint,
            $consumerKey
        );
    }

    public function send(User $user, $message)
    {
        $tel = '+33'.substr($user->getPhoneNumber(),1);


        $services = $this->api->get('/sms/');

        if(count($services) < 1) return;

        $senders = $this->api->get('/sms/'. $services[0].'/senders');

        if(count($senders) < 1) return;

        $content = (object) array(
            "charset"=> "UTF-8",
            "class"=> "phoneDisplay",
            "coding"=> "7bit",
            "message"=> $message,
            "noStopClause"=> true,
            "priority"=> "high",
            "receivers" => [ $tel ],
            "sender" => $senders[0],
            "senderForResponse"=> false,
            "validityPeriod"=> 2880
        );



        try {
            $newJob = $this->api->post('/sms/'. $services[0] . '/jobs/', $content);
        } catch (ClientException $e) {

        }

    }


}
