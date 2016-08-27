<?php

namespace console\components\Chat\controllers;

class AnswerSkeleton
{
	public $success;
    public $data;
    public $error;
    public $eventName;

    function __construct ($data, $eventName = 'default', $success = true, $error = null)
    {
    	$this->data = $data;
    	$this->eventName = $eventName;
    	$this->success = $success;
    	$this->error = $error;
    }
}