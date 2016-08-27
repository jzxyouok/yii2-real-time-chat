<?php

namespace console\components\Chat\controllers;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatServer extends Server
{
	const PORT = 8080;

	public static function factory ($PORT = null) {
		$defaultPort = self::PORT;
		
		if (!empty($PORT)) {
			$defaultPort = $PORT;
		}

		$server = IoServer::factory(
		    new HttpServer(
		        new WsServer(
		            new ChatManager()
		        )
		    ),
		    $defaultPort
		);
		return $server;
	}

	public function createServerInstance ()
    {
        if (!empty($this->server)) {
            throw new \Exception('Server instance not empty');
        }

        $this->server = self::factory();
    }
}