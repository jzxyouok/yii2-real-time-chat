<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;
use console\components\Chat\collections\Conversation;

class Opcodes extends AbstractOpcodes
{
    private $currentConnection;
    private $chatManager;

    private function setCurrentConnection (ConnectionInterface $conn)
    {
        $this->currentConnection = $conn;
    }

    private function setChatManager (ChatManager $chatManager)
    {
        $this->chatManager = $chatManager;
    }

    private function existsChatManager () {
        if (empty($this->chatManager)) {
            return false;
        }
        return true;
    }

    public function doEvent (ConnectionInterface $from, $msg, ChatManager $chatManager = null)
    {
        if (!empty($chatManager)) {
            $this->setChatManager($chatManager);
        }

        $this->setCurrentConnection($from);

        $eventName = $this->getEventNameFromMessage($msg);
        $data = $this->getData($msg);

        if (!$this->isEvent($eventName)) {
            return $this->onDefault($data);
        }

        return $this->{self::EVENT_PREFIX.$eventName}($data);
        
    }
    
    public function onDefault ($data)
    {
        
    }

    private function onCreateConversation ($data)
    {
        $this->log("Create Conversation doc.");
        if (!$this->existsChatManager()) {
            $this->logError('Need Chat instance for adding Conversation');
            return false;
        }
        $this->log('Input data: '.var_export($data, true));
        
        $participantId = intval($data['participantId']);

        $conversation = $this->chatManager->chat->addConversation($participantId);

        $this->chatManager->sendDataToClient($participantId, $conversation, 'statusCreateConversation');
        $this->chatManager->sendDataToClient($this->chatManager->chat->user->id, $conversation, 'statusCreateConversation');

        $this->log("Create conversation status sended to recepients");
    }
    private function onSendMessage($data)
    {
        $this->log('Send Message to conversation');

        $recepientConversation = null;

        if (empty($data)) {
            $this->log('Input data empty.');
            return false;
        }
        if (empty($data['conversationId'])) {
            $this->log('Input variable "Conversation not" found.');
            return false;
        }
        if (empty($recepientConversation = Conversation::getModel($data['conversationId']))) {
            $this->log("Conversation not found.");   
            return false;
        }
        if (empty($data['text'])) {
            $this->log("Message text cannot was empty");   
            return false;
        }

        $chat = $this->chatManager->chat;

        $message = $recepientConversation->addMessage($chat->user->id, $data['text']);

        foreach ($recepientConversation->participants as $participantId) {
            $this->chatManager->sendDataToClient($participantId, $message, 'newMessage');
        }
    }
}