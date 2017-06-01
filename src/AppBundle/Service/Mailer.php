<?php
namespace AppBundle\Service;

use AppBundle\Entity\User;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use Html2Text\Html2Text;
use Sortable\Fixture\Transport\Engine;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $twig;


    private $from;

    private $fromAlias;



    /**
     * Mailer constructor.
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $twig, $from, $fromAlias)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
        $this->fromAlias = $fromAlias;
    }

    public function sendToUser(User $dest, $subject, $template, $vars)
    {
        $html =  $this->twig->render('@App/Mail/'.$template.'.html.twig', $vars);
        $txt = Html2Text::convert($html);
        $to = $dest->getEmail();

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from, $this->fromAlias)
            ->setTo($to)
            ->addPart($txt, 'text/plain')
            ->setBody($html, 'text/html');

        $this->mailer->send($message);
    }

    public function sendToEmail(string $dest, $subject, $template, $vars)
    {
        $html =  $this->twig->render('@App/Mail/'.$template.'.html.twig', $vars);
        $txt = Html2Text::convert($html);
        $to = $dest;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from, $this->fromAlias)
            ->setTo($to)
            ->addPart($txt, 'text/plain')
            ->setBody($html, 'text/html');

        $this->mailer->send($message);
    }
}
