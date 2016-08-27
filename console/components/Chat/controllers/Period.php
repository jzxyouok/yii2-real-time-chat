<?php

namespace console\components\Chat\controllers;

class Period
{
	private $start;
    private $end;

    function __construct ($period)
    {
    	$this->init($period);
    }

    private function init ($period)
    {
    	if (empty($period)) {
    		$this->initDefaultValues();
    	}

    	if (empty($period['start'])) {
    		$this->initDefaultStart();
    	} else {
    		$this->setStart($period['start']);
    	}

    	if (empty($period['end'])) {
    		$this->initDefaultEnd();
    	} else {
    		$this->setEnd($period['start']);	
    	}
    }

    public function setStart ($val)
    {
    	$this->start = $this->intval($val);
    }
    public function setEnd ($val)
    {
    	$this->end = $this->intval($val);
    }
    
    public function getStart ()
    {
    	return $this->start;
    }
    public function getEnd ()
    {
    	return $this->end;
    }

    private function intval ($val)
    {
    	if (gettype($val) === 'iteger') {
    		return $val;
    	}
    	return intval($val);
    }

    private function initDefaultValues ()
    {
    	$this->initDefaultStart();	
    	$this->initDefaultEnd();	
    }

    private function initDefaultStart ()
    {
    	$this->start = time();
    }
    
    private function initDefaultEnd ()
    {
    	$this->end = $this->start - (60 * 60 * 24 * 30 * 2);
    }
}