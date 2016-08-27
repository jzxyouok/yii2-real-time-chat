<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;

class Params
{
    protected function getQueryParams (ConnectionInterface $conn, $querystring = null) 
    {
        $params = $conn->WebSocket->request->getQuery();

        if (!empty($querystring)) {
            $params = $params->toArray();
        }

        return $params;
    }

    protected function getParam ($conn, $key)
    {
        $param = $this->getQueryParams($conn, true)->get($key);

        return $param;
    }

    protected function getAnswerFormatedData ($data, $eventName, $success = true, $error = null) 
    {
        $answerSkeleton = new AnswerSkeleton($data, $eventName, $success, $error);
        return Json::encode($answerSkeleton);
    }

    protected function log ($outline)
    {
        print("\033[32mLog: \033[0m".$outline."\n");
    }

    protected function logError ($outline)
    {
        print("\033[31mError: \033[0m".$outline."\n");
    }

    protected function newline()
    {
        print("\n");
    }
}