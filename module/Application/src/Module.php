<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Listener\AuthenticationListener;
use Application\Service\Pusher;
use Application\Service\PushServerListener;
use React\ZMQ\Context;
use Zend\Mvc\MvcEvent;
use React\EventLoop\Factory;
use React\Socket\Server;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Ratchet\Server\IoServer;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

//    public function onBootstrap(MvcEvent $e)
//    {
//        $loop = Factory::create();
//        $pusher = new Pusher();
//
//        $context = new Context($loop);
//        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
//        $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
//        $pull->on('message', array($pusher, 'onLikeComment'));
//
//        // Set up our WebSocket server for clients wanting real-time updates
//        $webSock = new Server('0.0.0.0:8080', $loop); // Binding to 0.0.0.0 means remotes can connect
//        $webServer = new IoServer(
//            new HttpServer(
//                new WsServer(
//                    new WampServer(
//                        $pusher
//                    )
//                )
//            ),
//            $webSock
//        );
//        $loop->run();
////        $pushServer = new PushServerListener();
////        $pushServer->attach($e->getApplication()->getEventManager());
//    }
}
