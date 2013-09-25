<?php
/**
* ActionEvent.php
*-------------------------
*
* 
*
* PHP versions 5
*
* LICENSE: This source file is from Smarter Ver2.0, which is a comprehensive shopping engine 
* that helps consumers to make smarter buying decisions online. We empower consumers to compare 
* the attributes of over one million products in the common channels and common categories
* and to read user product reviews in order to make informed purchase decisions. Consumers can then 
* research the latest promotional and pricing information on products listed at a wide selection of 
* online merchants, and read user reviews on those merchants.
* The copyrights is reserved by http://www.mezimedia.com. 
* Copyright (c) 2006, Mezimedia. All rights reserved.
*
* @author Yaron Jiang <yjiang@corp.valueclick.com.cn>
* @copyright (C) 2004-2013 Mezimedia.com
* @license http://www.mezimedia.com PHP License 5.0
* @version CVS: $Id: ActionEvent.php,v 1.1 2013/07/15 09:21:39 yjiang Exp $
* @link http://www.dahongbao.com/
* @deprecated File deprecated in Release 3.0.0
*/

namespace Custom\Mvc;

use Zend\EventManager\Event;

class ActionEvent extends Event
{
    const EVENT_ACTION = 'action';
    
    const ACTION_SELECT = 'SELECT';
    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';
    const ACTION_DEFAULT = 'DEFAULT';
    
    protected $controller;
    protected $session;
    protected $action;
    
    function setController($controller)
    {
        $this->controller = (string)$controller;
    }
    
    function getController()
    {
        if($this->controller){
            return $this->controller;
        }
        return '';
    }
    
    function setSession($session)
    {
        $this->session = $session;
    }
    
    function getSession()
    {
        if(!$this->session){
            $this->setSession('');
        }
        return $this->session;
    }
    
    function setAction($action)
    {
        $this->action = $action;
    }
    
    function getAction()
    {
        if(!$this->action){
            $this->setAction(ActionEvent::ACTION_DEFAULT);
        }
    
        return $this->action;
    }
    
}