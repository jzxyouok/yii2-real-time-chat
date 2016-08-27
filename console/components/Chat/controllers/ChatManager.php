<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;

class ChatManager extends Opcodes implements MessageComponentInterface
{
    private $clients;
    private $opcodes;
    public $chat;

    public function __construct ()
    {
        $this->init();
    }

    private function init ()
    {
        $this->clients = new Clients();
        $this->opcodes = new Opcodes();
    }

    public function onOpen(ConnectionInterface $conn) 
    {
        $message = "\n"."Client connected, started doing authorizing client.\n";

        if (!$this->clients->addConnection($conn)) {
            return "\n".'Authorize failed. Not all require data for authorizing'."\n";
        }

        $this->initChat($this->clients->getIdentifityValue($conn));

        $this->printConnectionStatus();

        echo $message;

        $this->successfulAuthorized($conn);
    }

    private function initChat ($userId)
    {
        $this->chat = new Chat($userId);
    }

    private function successfulAuthorized (ConnectionInterface $conn)
    {
        $message = "\n".'Successful authorized connection'."\n";

        $userId = $this->chat->getUserId();

        $eventName = 'successfulConnection';
        $responseData = [
            'message' => 'Successful connection'
        ];

        $this->sendDataToClient($userId, $responseData, $eventName);

        echo $message;
        $responseData = [
            'conversations' => $this->chat->getAllConversations()
        ];

        $this->sendDataToClient($userId, $responseData, 'waitConversations');

        UserSecure::updateLastConnection($userId);
    }

    private function printConnectionStatus ()
    {
        $message = "\n".'--- --- ---  Connections status  --- --- ---'."\n";
        $message .= "\n".'Connection count: '.$this->clients->getConnectionsCount()."\n";
        $message .= 'Clients count: '.$this->clients->getClientsCount()."\n";
        $message .= "\n".'--- --- ---  Connections status  --- --- ---'."\n";
        echo $message;
    }

    public function onMessage (ConnectionInterface $from, $msg)
    {   
        $this->doEvent($from, $msg, $this);
    }
    
    public function sendDataToClient ($userId, $data, $eventName, $success = true, $error = null)
    {
        $formatedSendingData = $this->getAnswerFormatedData($data,$eventName, $success, $error);

        $this->clients->sendMessageToClient($userId, $formatedSendingData);
    }

    public function onClose(ConnectionInterface $conn) 
    {
        $this->clients->deleteConnection($conn);

        $this->printConnectionStatus();

        echo "\n".'Client has disconnected'."\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function error (ConnectionInterface $conn, $error)
    {
        
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
}