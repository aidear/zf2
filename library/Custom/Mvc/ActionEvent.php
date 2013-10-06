<?php
/**
 * ActionEvent.php
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
 * @version CVS: Id: ActionEvent.php,v 1.0 2013-10-6 下午10:12:55 Willing Exp
 * @link http://localhost
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