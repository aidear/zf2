<?php
/**
 * Config.php
 *------------------------------------------------------
 *
 * 
 *
 * PHP versions 5
 *
 *
 *
 * @author Willing Peng<pcq2006@gmail.com>
 * @copyright (C) 2013-2018 
 * @version CVS: Id: Config.php,v 1.0 2013-9-21 下午9:48:07 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\System;

class Config
{
    public $ID;
    public $PID = 0;
    public $cName;
    public $cKey;
    public $cValue;
    public $cRange;
    public $cRangeName;
    public $summary;
    public $cType = 'text';
    public $cShow = 1;
    public $sortOrder = 1;
    
    function exchangeArray(Array $data){
        $this->ID = isset($data['ID']) ? $data['ID'] : '';
        $this->PID = isset($data['PID']) ? $data['PID'] : 0;
        $this->cName = isset($data['cName']) ? $data['cName'] : '';
        $this->cKey = isset($data['cKey']) ? $data['cKey'] : '';
        $this->cValue = isset($data['cValue']) ? $data['cValue'] : '';
        $this->cRange = isset($data['cRange']) ? $data['cRange'] : '';
        $this->cRangeName = isset($data['cRangeName']) ? $data['cRangeName'] : '';
        $this->summary = isset($data['summary']) ? $data['summary'] : '';
        $this->cType = isset($data['cType']) ? $data['cType'] : '';
        $this->cShow = isset($data['cShow']) ? $data['cShow'] : 1;
        $this->sortOrder = isset($data['sortOrder']) ? $data['sortOrder'] : '';
    }
    
    function getArrayCopy(){
        return get_object_vars($this);
    }
    function toArray(){
    	return get_object_vars($this);
    }
}