<?php
namespace console\controllers;

use jones\wschat\components\Chat;
use jones\wschat\components\ChatManager;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use common\models\User;

use Yii;
use yii\console\Controller;

use console\controllers\Daemon;

class ChatController extends Controller
{
	private $daemon = null;

	private $port = 8080;
	/**
     * Running chat server, when you rerun server, will be restart server
     */
    public function actionRun()
    {
    	$this->createDaemon();

        $runnedStr = 'runned';
        
        if ($this->daemon->runned()) {
            $runnedStr = 're'.$runnedStr;
            $this->daemon->destroy();
        }
        
        echo "Daemon chat server $runnedStr Port: ".$this->port."\n";
        $this->daemon->run();

        $this->run();
    }
    
    private function run () 
    {
        $manager = Yii::configure(new ChatManager(), [
            'userClassName' => User::className()
        ]);

        $server = IoServer::factory(new HttpServer(new WsServer(new Chat($manager))), $this->port);

        $server->run();
    }

    private function createDaemon () {
    	if (empty($this->daemon)) {
    		$this->daemon = new Daemon();	
    	}
    }
    /**
     * Destroying chat server
     */
    public function actionStop() 
    {
        $this->createDaemon();

        if (!$this->daemon->runned()) {
            echo "Daemon not runned\n";
        }
        if ($this->daemon->destroy()) {
            echo "Daemon destroyed\n";
        } else {
            echo "Destroying failed\n";
        }
    }
	/**
     * Chat server status
     */
    public function actionStatus () 
    {
    	$this->createDaemon();
    	if (!$this->daemon->runned()) {
    		echo "Daemon not runned\n";
    		return;
    	}
    	echo "Daemon runned\n";
    }
}