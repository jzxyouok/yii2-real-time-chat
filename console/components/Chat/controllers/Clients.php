<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;

class Clients {

    private $REQUIRE_CONNECTION_PARAMS = ['user_id', 'access_token'];

    private $clients;

    private $identifityKey = 'user_id';
    private $accessTokenKey = 'access_token';

    public function __construct() {
        $this->clients = [];
    }

    public function addConnection (ConnectionInterface $conn)
    {
        if (!$this->isValidConnection($conn)) {
            $conn->close();            
            return false;
        }


        $identifityValue = $this->getParam($conn, $this->identifityKey);
        $clientExist = $this->existClient($conn);

        if ($clientExist) {
            array_push($this->clients[$identifityValue], $conn);
        } else {
            $this->clients[$identifityValue] = [$conn]; 
        }

        return true;
    }

    public function isValidConnection ($conn)
    {
        $isValidParams = $this->existsRequireParams($conn);     
        $isSecureUser = $this->isUserSecureConnection($conn);

        if (!$isValidParams || !$isSecureUser) {
            return false;
        }

        return true;
    }
    
    private function existsRequireParams ($conn)
    {
        $params = $this->getQueryParams($conn);

        foreach ($params as $key => $value) {
            $exist = false;
            foreach ($this->REQUIRE_CONNECTION_PARAMS as $paramName) {
                $equal = $paramName === $key;
                if ($equal) {
                    $exist = true;
                }
            }
            if (!$exist) {
                return false;
            }
        }
        return true;
    }

    private function isUserSecureConnection (ConnectionInterface $conn)
    {
        $userId = $this->getParam($conn, $this->identifityKey);
        $authAccessToken = $this->getParam($conn, $this->accessTokenKey);

        $accessToken = UserSecure::getAccessTokenByUserId($userId);

        $allowedReconnectTime = UserSecure::allowedToReconnect($userId);
        
        if (empty($accessToken)) {
            return false;
        }

        if ($accessToken !== $authAccessToken || !$allowedReconnectTime) {
            return false;
        }

        return true;

    }

    public function getIdentifityValue ($conn)
    {
        $identifityValue = $this->getParam($conn, $this->identifityKey);
        
        return $identifityValue;
    }

    private function existClient (ConnectionInterface $conn)
    {
        $identifityValue = $this->getParam($conn, $this->identifityKey);

        if (empty($identifityValue) || !$this->existClientByIdentifityValue($identifityValue)) {
            return false;
        }

        return true;
    }

    public function getClientsCount()
    {
        return count($this->clients);
    }

    public function getConnectionsCount()
    {
        $count = 0;

        foreach ($this->clients as $client) {
            $count += count($client);
        }

        return $count;
    }

    public function disconnectClient ($identifityValue)
    {
        $clientExist = $this->existClientByIdentifityValue($identifityValue);

        if (!$clientExist) {
            return false;
        }

        $client = $this->clients[$identifityValue];

        foreach ($client as $connection) {
            $this->deleteConnection($connection);
        }

        return true;
    }

    public function disconnectAllClients ()
    {
        foreach ($this->clients as $client) {
            foreach ($client as $connection) {
                $this->deleteConnection($connection);
            }
        }

        return true;
    }

    public function deleteConnection (ConnectionInterface $conn)
    {
       $clientExist = $this->existClient($conn);

        if ($clientExist) {
            $identifityValue = $this->getParam($conn, $this->identifityKey);
            $client = $this->clients[$identifityValue];
            foreach ($client as $key => $connection) {
                if ($connection === $conn) {
                    $conn->close();
                    unset($this->clients[$identifityValue][$key]);
                    if (count($this->clients[$identifityValue]) == 0) {
                        unset($this->clients[$identifityValue]);
                        UserSecure::unsetAccessTokenByUserId($identifityValue);
                    }
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    public function sendMessageToClient ($identifityValue, $message)
    {
        $clientExist = $this->existClientByIdentifityValue($identifityValue);

        if (!$clientExist) {
            return false;
        }

        $client = $this->clients[$identifityValue];

        foreach ($client as $connection) {
            $connection->send($message);
        }

        return true;
    }

    public function sendMessageToAllConnections ($message)
    {
        foreach ($this->clients as $client) {
            foreach ($client as $connection) {
                $connection->send($message);
            }
        }

        return true;
    }


    private function existClientByIdentifityValue ($identifityValue)
    {
        if (!array_key_exists($identifityValue, $this->clients)) {
            return false;
        }
        return true;
    }

    public function getQueryParams (ConnectionInterface $conn, $querystring = null) 
    {
        $params = $conn->WebSocket->request->getQuery();

        if (empty($querystring)) {
            $params = $params->toArray();
        }

        return $params;
    }

    public function getParam ($conn, $key)
    {
        $param = $this->getQueryParams($conn, true)->get($key);

        return $param;
    }
}