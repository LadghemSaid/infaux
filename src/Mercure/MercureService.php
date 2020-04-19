<?php

namespace App\Mercure;


use App\Form\ContactType;
use App\Repository\PostRepository;
use App\Repository\MaillingListRepository;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MercureService extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function addNotification($user=null,$message='Message par defaut')
    {
        if($user){
            $target =[
                "/user/{$user->getId()}"
            ];
        }else{
            $target =[
                "/user/{$this->getUser()->getId()}"
            ];
        }

        $update = new Update('/message', json_encode([
            'message' => $message,
        ]),$target);
        $this->bus->dispatch($update);

        return true;
    }
}
