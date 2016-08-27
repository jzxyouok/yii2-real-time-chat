<?php

namespace console\components\Chat\controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use yii\helpers\Json;
use yii\web\Request;
use console\components\Chat\collections\UserSecure;

abstract class AbstractOpcodes extends Params
{
    const DEFAULT_EVENT_NAME = 'default';
    const DATA_VARIABLE_NAME = 'data';
    const EVENT_PREFIX = 'on';

    abstract public function doEvent (ConnectionInterface $from, $msg);    
    abstract public function onDefault ($data);

    protected function getData ($msg)
    {
        if (empty($msg)) {
            return null;
        }
        $object = Json::decode($msg);

        if (empty($object[self::DATA_VARIABLE_NAME])) {
            return null;
        }
        return $object[self::DATA_VARIABLE_NAME];
    }

    protected function isEvent ($eventName)
    {
        $isFunction = method_exists($this, self::EVENT_PREFIX.$eventName);
        if (!$isFunction) {
            return false;
        }
        return true;
    }

    protected function getEventNameFromMessage ($msg)
    {
        if (empty($msg)) {
            return self::DEFAULT_EVENT_NAME;
        }
        $object = Json::decode($msg);
        if (empty($object['eventName']) || !is_string($object['eventName'])) {
            return self::DEFAULT_EVENT_NAME;
        }

        return $object['eventName'];
    }
}