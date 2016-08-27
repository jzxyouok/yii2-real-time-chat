<?php
namespace console\controllers;

class Daemon
{
    private $PID = null;

    private $baseDir = './';

    public $STDIN = null;
    public $STOUT = null;
    public $STDERR = null;

    public $fileWithPID = 'wschat.pid';

    public function __construct ($fileWithPID = null) 
    {
        $this->init($fileWithPID);   
    }

    private function init ($fileWithPID) 
    {
        $this->setFileWithPID($fileWithPID);
        $this->baseDir = dirname(__FILE__);
    }

    private function setFileWithPID ($fileWithPID) 
    {
        if (empty($fileWithPID)) {
            return 0;
        }

        $this->fileWithPID = $fileWithPID;
        return 1;
    }

    public function run () 
    {
		$this->switchProcess();
        $this->switchOutput();		
    }

    private function switchProcess () {
        $this->PID = pcntl_fork();

        if ($this->PID) {
            $this->savePID();
            exit();
        }

        posix_setsid();
    }

    private function switchOutput () 
    {
        ini_set('error_log',$this->baseDir.'/error.log');
        $this->closeConsoleOutputs();
        $this->changeOutputWay();
    }

    private function closeConsoleOutputs () 
    {
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
    }

    private function changeOutputWay () 
    {
        $this->STDIN = fopen('/dev/null', 'r');
        $this->STDOUT = fopen($this->baseDir.'/chat.log', 'ab');
        $this->STDERR = fopen($this->baseDir.'/daemon-chat.log', 'ab');
    }

    private function readPID () 
    {
        $fileWithPID = $this->getPIDFullFilename();
        
        if (!file_exists($fileWithPID)) {
            return 0;
        }
        
        $PID = file_get_contents($fileWithPID);
        $this->PID = $PID;

        return $PID;
    }

    private function savePID () 
    {
        $this->deleteFileWithPID();
        $fileWithPID = $this->getPIDFullFilename();

        print("PID data saved to : ".$fileWithPID."\n");
        
        file_put_contents($fileWithPID, $this->PID);
    }

    private function deleteFileWithPID() 
    {
        $isFileExists = file_exists($this->getPIDFullFilename());
        if ($isFileExists) {
            unlink($this->getPIDFullFilename());
        }
    } 

    private function getPIDFullFilename () 
    {
        return $this->baseDir.'/'.$this->fileWithPID;
    }

    public function destroy() 
    {
        $this->readPID();
        
        if (!$this->pidExsists()) {
            return 0;
        }
        
        $this->deleteFileWithPID();

        posix_kill($this->PID, 15);
        
        return 1;
    }

    public function restart () 
    {
        $this->destroy();
        $this->run();
    }

    public function runned () 
    {
        if ($this->readPID()) {
            return true;
        }
        return false;
    }

    public function pidExsists () 
    {
        if (empty($this->PID)) {
            return false;
        }
        return true;
    }

    public function getPID () 
    {
        $pidExsists = $this->pidExsists();
        $this->readPID(); 

        $isValidPID = !pidExsists && !$this->PID;
        
        if ($isValidPID) {
            return 0;
        }

        return $this->PID;
    }
}