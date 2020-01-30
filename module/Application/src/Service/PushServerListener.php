<?php


namespace Application\Service;


use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

class PushServerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;


    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'initNotification']);

    }

    public function initNotification(){
        var_dump("AAA");
        exit();
    }

}