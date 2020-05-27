<?php


namespace App\EventListener;

use App\Controller\PosterController;
use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class DatabaseSubscriber implements EventSubscriber
{
    public function getSubscribedEvents() {
        return [Events::postUpdate, Events::postPersist, Events::postRemove];
    }
    private $_poster;
    public function __construct(PosterController $poster)   { $this->_poster = $poster; }
    public function postPersist(LifecycleEventArgs $args)   { $this->onEvent('persist', $args); }
    public function postRemove(LifecycleEventArgs $args)    { $this->onEvent('remove', $args); }
    public function postUpdate(LifecycleEventArgs $args)    { $this->onEvent('update', $args); }

    private function onEvent(string $act, LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $manager = $args->getEntityManager();
        if (!$entity instanceof Product) return;
        $entity_str = $entity->json_serialize();
        switch ($act) {
            case 'persist': $this->_poster->sendEmail($manager, $entity, 'persist'); break;
            case 'update':  $this->_poster->sendEmail($manager, $entity, 'update');  break;
            case 'remove':  $this->_poster->sendEmail($manager, $entity, 'remove');  break;
        }
    }
}