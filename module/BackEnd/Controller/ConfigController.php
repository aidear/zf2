<?php
/**
 * ConfigController.php
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
 * @version CVS: Id: ConfigController.php,v 1.0 2013-9-21 下午9:51:20 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace BackEnd\Controller;

use Custom\Mvc\ActionEvent;

use Zend\Paginator\Paginator;

use BackEnd\Model\Users\RoleTable;

use BackEnd\Model\Users\Role;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Users\UserTable;
use BackEnd\Model\Users\User;
use BackEnd\Form\UserForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Custom\Util\Utilities;
use BackEnd\Form\ConfigForm;
use BackEnd\Model\System\Config;
use Custom\File\Uploader;

use Zend\Validator\File\Size;
use Zend\Config\Config as Conf;
use Zend\Config\Writer\PhpArray;
use Zend\View\Model\ViewModel;

class ConfigController extends AbstractActionController
{
    function indexAction(){
    	$configTable = $this->_getTable('ConfigTable');
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$set = $request->getPost()->toArray();
    		
    		$file = $this->params()->fromFiles();
    		
    		if ($file) {
    			foreach ($file as $k=>$v) {
    				if (!empty($v['name'])) {
    					$path = $this->_insertImg($k, $v);
    					$set[$k] = $path;
    				}
    			}
    		}
    		foreach ($set as $k=>$v) {
    			if (!in_array($k, array('submit', 'button'))) {
    				$configTable->setConfigValue($k, $v);
    			}
    		}
    		
    		$sys = $configTable->getAll();
    		$sysArr = $sys->toArray();
    		$cache = array();
    		foreach ($sysArr as $v) {
    			if ($v['PID']) {
    				$cache[$v['cKey']] = $v['cValue'];
    			}
    		}
    		$conf = new Conf($cache);
    		
    		$writer = new PhpArray();
    		
    		//echo $writer->toString($conf);die;
    		//@file_put_contents(APPLICATION_PATH.'/data/sys_config.php', $writer->toString($conf));
    		$writer->toFile(APPLICATION_PATH.'/data/sys_config.php', $conf);
    	}
    	
    	
    	$configInfo = $configTable->select("PID IN (SELECT ID FROM sys_config WHERE cKey='basic') AND cShow=1 AND PID<>0");//print_r($configInfo->toArray());
    	
    	return array('config' => $configInfo, 'cateTitle' => '基本配置');
    }
    
    function mailAction(){
    	$configTable = $this->_getTable('ConfigTable');
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$set = $request->getPost()->toArray();
    
    		$file = $this->params()->fromFiles();
    
    		if ($file) {
    			foreach ($file as $k=>$v) {
    				if (!empty($v['name'])) {
    					$path = $this->_insertImg($k, $v);
    					$set[$k] = $path;
    				}
    			}
    		}
    		foreach ($set as $k=>$v) {
    			if (!in_array($k, array('submit', 'button'))) {
    				$configTable->setConfigValue($k, $v);
    			}
    		}
    
    		$sys = $configTable->getAll();
    		$sysArr = $sys->toArray();
    		$cache = array();
    		foreach ($sysArr as $v) {
    			if ($v['PID']) {
    				$cache[$v['cKey']] = $v['cValue'];
    			}
    		}
    		$conf = new Conf($cache);
    
    		$writer = new PhpArray();
    
    		//echo $writer->toString($conf);die;
    		//@file_put_contents(APPLICATION_PATH.'/data/sys_config.php', $writer->toString($conf));
    		$writer->toFile(APPLICATION_PATH.'/data/sys_config.php', $conf);
    	}
    	 
    	 
    	$configInfo = $configTable->select("PID IN (SELECT ID FROM sys_config WHERE cKey='mail_service') AND cShow=1 AND PID<>0");//print_r($configInfo->toArray());
    	 
    	$v = new ViewModel(array('config' => $configInfo, 'cateTitle' => '邮件服务器'));
    	$v->setTemplate('back-end/config/index');
    	return $v;
    }
    
    function addAction()
    {
    	$form = new ConfigForm();
    	$configTable = $this->_getTable('ConfigTable');
    	$cate = $configTable->getCate();
    	//array_unshift($cate, '根目录');
    	$form->get('PID')->setValueOptions($cate);
    	$config = new Config();
    	$form->bind($config);
    	
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setData($request->getPost());//print_r($request->getPost());die;
    		if ($form->isValid()) {
    			$configTable = $this->_getTable('ConfigTable');
    			$flg = $configTable->insert($config->getArrayCopy());
//     			$this->flashMessenger()->addMessage('角色名称重复，请更换其他名称！');
    			return $this->redirect()->toRoute('backend', array('controller' => 'config' , 'action' => 'index'));
    		} else {
    			print_r($this->flashMessenger()->getMessages());die;
    		}
    		
    		
    	}
    	
    	return array('form' => $form);
    }
    private function _insertImg($name, $files)
    {
    	$config = $this->_getConfig('upload');
    	$config = $config['sys_config'];
    	if(! empty($files['name'])){
    		return $config['showPath'] . Uploader::upload($files , $name , $config['uploadPath'] , $config);
    	}else{
    		return $this->params()->fromPost($name);
    	}
    
    	return null;
    }
}
