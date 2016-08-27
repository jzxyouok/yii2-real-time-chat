<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;

class ChatManager implements MessageComponentInterface {

    private $REQUIRE_CONNECTION_PARAMS = ['user_id', 'access_token'];

    protected $clients;

    public function __construct() {
        $this->clients = [];
    }

    public function onOpen(ConnectionInterface $conn) 
    {
        $message = "Client connected\n";

        if (!$this->attachClient($conn)) {
            $message = 'Connection failed. Not all require connection data';
        }

        echo $message;

        echo 'Connection count: '.count($this->clients);
    }

    private function attachClient (ConnectionInterface $conn)
    {
        if (!$this->isValidConnection($conn)) {
            $conn->close();
            return false;
        }
        

        /*array_push($this->clients, $conn);*/

        return true;
    }

    private exists

    private function isValidConnection ($conn)
    {
        $params = $this->getQueryParams($conn);

        if (!$this->existsRequireParams($params)) {
            return false;
        }
        
        $userSecure = UserSecure::findDoc(['user_id' => $params['user_id']]);

        if (empty($userSecure->access_token) || $userSecure->access_token != $params['access_token']) {
            return false;
        }

        return true;
    }

    private function existsRequireParams ($params)
    {      
        foreach ($params as $key => $value) {
            $exist = false;
            foreach ($this->REQUIRE_CONNECTION_PARAMS as $paramName) {
                if ($paramName === $key) {
                    $exist = true;
                }
            }
            if (!$exist) {
                return false;
            }
        }
        return true;
    }

    private function getQueryParams (ConnectionInterface $conn, $querystring = null) 
    {
        $params = $conn->WebSocket->request->getQuery();

        if (!empty($querystring)) {
            $params = $params->toArray();
        }

        return $params;
    }

    private function getParam ($conn, $key)
    {
        $param = $this->getQueryParams($conn, true)->get($key);

        return $param;
    }

    public function onMessage(ConnectionInterface $from, $msg) 
    {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function sendMessageToUser ($user_id, $msg) 
    {

    }

    public function onClose(ConnectionInterface $conn) 
    {
        $this->clients->detach($conn);
        echo "Client has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function getAnswerFormatedData ($data, $success = true, $error = null) 
    {
        $answerSkeleton = new AnswerSkeleton($data, $success, $error);
        return Json::encode($answerSkeleton);
    }

    private function sendAccessToken ($user_id, $connection) {
        $accessToken = UserSecure::findDoc(['user_id' => $user_id]);
        
        $data = [
            'access_token' => $accessToken
        ];

        $userSecure = new UserSecure();


        $connection->send($this->getAnswerFormatedData($data));
    }
}