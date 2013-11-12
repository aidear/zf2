<?php
/**
 * AdminLog.php
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
 * @version CVS: Id: AdminLog.php,v 1.0 2013-11-12 下午11:00:15 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Model\System;

class AdminLog
{
    public $user_id;
    public $user_name;
    public $opt_type;
    public $info;
    public $ip;
    public $add_time;
    
    function exchangeArray(Array $data){
        $this->user_id = isset($data['user_id']) ? $data['user_id'] : '';
        $this->user_name = isset($data['user_name']) ? $data['user_name'] : '';
        $this->opt_type = isset($data['opt_type']) ? $data['opt_type'] : 0;
        $this->info = isset($data['info']) ? $data['info'] : '';
        $this->ip = isset($data['ip']) ? $data['ip'] : '';
        $this->add_time = isset($data['add_time']) ? $data['add_time'] : date('Y-m-d H:i:s');
    }
    
    function getArrayCopy(){
        return get_object_vars($this);
    }
    function toArray(){
    	return get_object_vars($this);
    }
}