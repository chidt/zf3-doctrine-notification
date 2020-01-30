<?php
require 'vendor/autoload.php';
use Application\Service\Pusher;
use React\ZMQ\Context;
use React\EventLoop\Factory;
use React\Socket\Server;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Ratchet\Server\IoServer;
$loop = Factory::create();
$pusher = new Pusher();

$context = new Context($loop);
$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
$pull->on('message', array($pusher, 'onLikeComment'));

// Set up our WebSocket server for clients wanting real-time updates
$webSock = new Server('0.0.0.0:8089', $loop); // Binding to 0.0.0.0 means remotes can connect
$webServer = new IoServer(
    new HttpServer(
        new WsServer(
            new WampServer(
                $pusher
            )
        )
    ),
    $webSock
);
$loop->run();