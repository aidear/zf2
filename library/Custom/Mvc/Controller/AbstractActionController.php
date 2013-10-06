<?php

/**
* AbstractActionController.php
*-------------------------
*
* 控制器自定义抽象类
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
* @author Yaron Jiang<yjiang@corp.valueclick.com>
* @copyright (C) 2004-2013 Mezimedia.com
* @license http://www.mezimedia.com PHP License 5.3
* @version CVS: $Id: AbstractActionController.php,v 1.4 2013/07/15 09:21:38 yjiang Exp $
* @link http://www.dahongbao.com
* @deprecated File deprecated in Release 3.0.0
*/
namespace Custom\Mvc\Controller;

use Zend\EventManager\EventManager;

use Custom\Mvc\ActionEvent;


use Zend\Mvc\Controller\AbstractActionController as Father;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class AbstractActionController extends Father
{
    const LIMIT = 15;
    const CN_SITE_ID = 1;
    const us_SITE_ID = 2;
    const MSG_SUCCESS = 'success';    //message
    const MSG_ERROR = 'error';    //message
    
    protected $actionEvent;
    protected $actionEvents;
    protected $action;
    
    protected $sites = array('1' => '中国' , '2' => '美国');
    
    /**
     * 后台配置文件
     * @var array
     */
    protected $config = array();
    
    /**
     * 根据tableName获取table 实列
     * @param string $tableName
     */
    protected function _getTable($tableName)
    {
        if (empty($tableName)) {
            throw new \Exception('tableName is Empty');
        }
        if (!isset($this->$tableName)) {
            $this->$tableName = $this->getServiceLocator()->get($tableName);
        }
        return $this->$tableName;
    }
    
    /**
     * 获取Session
     * @param string $nameSpace
     * @return \Zend\Session\Container
     */
    protected function _getSession($nameSpace = 'user'){
        return new Container($nameSpace);
    }
    
    /**
     * 发送信息
     * @param string $message
     * @param string $type
     * @return \Custom\Mvc\Controller\AbstractActionController
     */
    protected function _message($message , $type = self::MSG_SUCCESS){
        $this->flashMessenger()->setNamespace($type);
        $this->flashMessenger()->addMessage($message);
        return $this;
    }
    
    /**
     * 读取配置文件
     * @param string $configName
     * @throws \Exception
     * @return Ambigous <multitype:, \Zend\Config\Config, unknown>
     */
    protected function _getConfig($configName){
        if(empty($configName)){
            throw new \Exception('configName is Empty');
        }
        
        $filename = APPLICATION_PATH . '/module/BackEnd/Config/' . $configName . '.config.php';
        if(!file_exists($filename)){
            throw new \Exception('not found ' . $filename);
        }
        
        return \Zend\Config\Factory::fromFile($filename);
    }
    
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        $events = $this->getServiceLocator()->get('Application')->getEventManager();
        $events->attach(MvcEvent::EVENT_RENDER, array($this, 'setVar') , 100);
    }

    public function setVar($event)
    {
        $viewModel = $event->getViewModel();
        $flashMessenger = $this->flashMessenger();
        
        $flashMessenger->setNamespace('error');
        if($flashMessenger->hasMessages()){
            $messages['error'] = $flashMessenger->getMessages();
        }
        
        $flashMessenger->setNamespace('success');
        if($flashMessenger->hasMessages()){
            $messages['success'] = $flashMessenger->getMessages();
        }
        if(!empty($messages)){
            $this->layout()->flashMessage = $messages;
        }
        
        if ($viewModel->hasChildren()) {
            foreach ($viewModel->getChildren() as $child) {
                $child->setVariables(array(
                    'sites' => $this->sites,
                ));
            }
        }
    }
    
    /**
     * 得到后台配置
     * @param string $name
     * @return string $value
     */
    protected function getBackEndOptions($name) {
        if (empty($this->config)) {
            $file = APPLICATION_MODULE_PATH . '/Config/dps.config.php';
            if (!file_exists($file)) {
                throw new \Exception('can not found the file' . $file);
            }
            $this->config = \Zend\Config\Factory::fromFile($file);
        }
        return $this->config[$name];
    }
    
    /**
     * 创建日志
     * @id int 文件后缀名称
     * @type string 日志类型
     * return string 文件路径
     */
    protected function getStdoutFile($id, $merType, $typeName)
    {
        if (empty($id) || empty($typeName)) {
            throw new \Exception('id or typeName can not empty');
        }
        $logPath = $this->getBackEndOptions($typeName);
        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, true);
        }
        $logFile = $id . '_'. $merType . '.log';
        return $logPath . $logFile;
    }
    
    function setActionEvent(ActionEvent $event)
    {
        $this->actionEvent = $event;
        $route = $this->getEvent()->getRouteMatch();
        
        $this->actionEvent->setController($route->getParam('controller'));
        
        $request = $this->getRequest();
        if($request->isPost()){
            $params = $request->getPost();
        }else{
            $params = $request->getQuery();
        }
        
        $this->actionEvent->setParams($params);
        $this->actionEvent->setSession(new Container('user'));
    }
    function getActionEvent()
    {
        if(!$this->actionEvent){
            $this->setActionEvent(new ActionEvent());
        }
        
        return $this->actionEvent;
    }
    
    function setActionEvents(EventManager $events)
    {
        $this->actionEvents = $events;
        $this->actionEvents->setIdentifiers('action');
    }
    
    function getActionEvents()
    {
        if(!$this->actionEvents){
            $this->setActionEvents(new EventManager());
        }
        
        return $this->actionEvents;
    }
    
    function trigger($action)
    {
        $e = $this->getActionEvent();
        $e->setAction($action);
        $this->getActionEvents()->trigger(ActionEvent::EVENT_ACTION , $e);
    }

}