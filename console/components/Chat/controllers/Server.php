<?php

namespace console\components\Chat\controllers;

use console\controllers\Daemon;

abstract class Server
{
	protected $server;
	protected $daemon = null;

	function __construct ($isDeamon = false) {
		$this->run($isDeamon);
	}

    protected function run ($isDeamon = false) {
    	if (!$isDeamon) {
			return $this->runServer();
    	}

    	$this->runServer();
    	return $this->runDeamon();
    }

    protected function runServer ()
    {
    	$this->createServerInstance();
		$this->server->run();
    }

    abstract public function createServerInstance ();

    protected function runDeamon ()
    {
    	$this->createDaemon();
    	$this->daemon->run();
    }

    protected function createDaemon () {
    	if (empty($this->daemon)) {
    		$this->daemon = new Daemon();	
    	}
    }
}