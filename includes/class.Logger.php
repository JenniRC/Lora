<?php
/*
 *
 *  Copyright (C) 2016 Libelium Comunicaciones Distribuidas S.L.
 *  http://www.libelium.com
 *
 *  This program is distributed WITHOUT ANY WARRANTY; without
 *  even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 *  PARTICULAR PURPOSE.
 *
 *  By using it you accept the Libelium Terms & Conditions.
 *  You can find them at: http://libelium.com/legal
 *
 *
 *  Version:           2.3
 *  Design:            David Gascon
 *  Implementation:    Diego Becerrica
 */

class LogLevel{
    const OFF = 'off';
    const ERROR = 'error';
    const INFO = 'info';
    const DEBUG = 'debug';
    const ALL = 'all';
}

class Logger{
    private $file;
    private $level_set;
    private $levels = array(
        LogLevel::OFF => 0,
        LogLevel::ERROR => 1,
        LogLevel::INFO => 2,
        LogLevel::DEBUG => 3,
        LogLevel::ALL => 4
    );
    private $fileHandle;
    private $type;

    public function __construct($_file, $_type, $_level){
        if(is_file($_file))
            $this->file = $_file;
        else
            throw new Exception("Log file " . $_file . " does not exist.");

        $this->type = $_type;

        $this->level_set = $this->levels[LogLevel::DEBUG];
        if(!isset($_level) || $_level=="")
            $this->level_set = $this->levels[LogLevel::DEBUG];
        else{
            $_level = strtolower($_level);
            if(array_key_exists($_level, $this->levels))
                $this->level_set = $this->levels[$_level];
            else
                $this->level_set = $this->levels[LogLevel::DEBUG];
        }
    }

    public function __destruct(){

    }

    private function log($_level, $_message){
        try {
            if ($this->level_set < $this->levels[$_level]) {
                return;
            } else{
                $this->fileHandle = fopen($this->file, 'a+');
                $message = "[" . $this->getTimestamp() . "] [" . strtoupper($_level) . "] " . $this->type . " - " . $_message . PHP_EOL;
                if (fwrite($this->fileHandle, $message) === false) {
                    throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
                }
            }
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        finally{
            if ($this->fileHandle) {
                fclose($this->fileHandle);
            }
        }
    }

    public function addError($_message)
    {
        $this->log(LogLevel::ERROR, $_message);
    }

    public function addInfo($_message)
    {
        $this->log(LogLevel::INFO, $_message);
    }

    public function addDebug($_message)
    {
        $this->log(LogLevel::DEBUG, $_message);
    }

    private function getTimestamp()
    {
        $t = microtime(true);
        $micro = sprintf("%03d", ($t - floor($t)) * 1000);
        $date = new DateTime(date('Y-m-d H:i:s', $t));
        return $date->format("Y-m-d H:i:s."). $micro;
    }

}