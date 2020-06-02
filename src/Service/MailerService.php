<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $token
     * @param $username
     * @param $template
     * @param $to
     */
    public function sendToken($token, $user, $template)
    {

        $message = (new \Swift_Message('Mail de confirmation'))
            ->setFrom('pop-web.contact@gmail.com')
            ->setTo($user->getEmail());

        $img = $message->embed(\Swift_Image::fromPath('img/logo_infaux.png')); // j'ajoute l'image que je souhaite afficher
        $message->setBody(
            $this->renderView(
                'emails/' . $template,
                [
                    'token' => $token,
                    'user' => $user,
                    'img'=>$img
                ]
            ),
            'text/html'
        );
        $this->mailer->send($message);
    }
}
