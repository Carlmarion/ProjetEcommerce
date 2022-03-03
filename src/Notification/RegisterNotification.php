<?php

namespace App\Notification;

use App\Entity\User;
use Twig\Environment;

class RegisterNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment 
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer; 
    }

    public function notify(User $user)
    {
        $msg = (new \Swift_Message('Bienvenue sur BuyAll le marché en ligne ou vous pouvez trouver tout ce que vous ne désirez pas encore'))
            ->setFrom("marion.carl498@gmail.com")
            ->setTo($user->getEmail())
            ->setReplyTo("noreply")
            ->setBody($this->renderer->render("emails/register.html.twig", [
                'user' => $user,
            ]), 'text/html');

            $this->mailer->send($msg);

    }
}