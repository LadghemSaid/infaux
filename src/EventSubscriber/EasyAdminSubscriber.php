<?php
namespace App\EventSubscriber;

use App\Entity\Article;
use Cocur\Slugify\Slugify;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{


    public function __construct()
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_persist' => [
                ["setArticleSlug",20]
            ],
        );
    }



    public function setArticleSlug(GenericEvent $event)
    {

        $entity = $event->getSubject();

        if (!($entity instanceof Article)) {
            return;
        }

        $slugifyTitle = new Slugify();
        $entity->setSlug($slugifyTitle->slugify($entity->getTitle()));

        $event['entity'] = $entity;

    }
}
