<?php
/**
 * UserController.php
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
 * @version CVS: Id: UserController.php,v 1.0 2013-9-23 下午10:10:10 Willing Exp
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
use Custom\Form\Form;

class UserController extends AbstractActionController
{
    function indexAction(){
        $page = $this->params()->fromQuery('page' , 1);
        $pageSize = $this->params()->fromQuery('pageSize');
        $table = $this->_getUserTable();
        $username = $this->params()->fromQuery('username' , '');
        
        if($username){
            $re = $table->getUserForName($username);
        }else{
            $re = $table->getAllToPage();
        }
        
        $paginaction = new Paginator($re);
        $paginaction->setCurrentPageNumber($page);
        $paginaction->setItemCountPerPage($pageSize ? $pageSize : self::LIMIT);
        $url = "/user";
        if ($pageSize) {
        	$url .= "?pageSize=".$pageSize;
        }
        return array('paginaction' => $paginaction, 'url' => $url);
    }
    
    function saveAction(){
        $requery = $this->getRequest();
        $form = new UserForm();
        if($requery->isPost()){
            $params = $requery->getPost();
            $user = new User();
            $user->ID = $params->ID;
            $user->UserName = $params->UserName;
            $user->LastUpdate = $user->AddTime = Utilities::getDateTime();
            $user->Role = $params->Role;
            unset($user->Password);
            $user->Status = 1;
            $table = $this->_getUserTable();
            $table->save($user);
            
            if($user->ID){
                $this->trigger(ActionEvent::ACTION_UPDATE);
                $this->_message('更新成功');
            }else{
                $this->_message('添加成功');
                $this->trigger(ActionEvent::ACTION_INSERT);
            }
            return $this->redirect()->toRoute('backend' , array('controller' => 'user' , 'action' => 'index'));
        }
        $table = $this->_getRoleTable();
        $roles = $table->getAll();
        
        if($userId = $requery->getQuery('id')){
            $table = $this->_getUserTable();
            $re = $table->getOneForId($userId);
            $form->bind($re);
            $form->setRole($roles , $re->Role);
            $form->get('ID')->setAttribute('value', $userId);
            return array('user' => $re , 'roles' => $roles , 'form' => $form , 'ID' => $userId);
        }
        
        $form->setRole($roles);
        return array('user' => new User() , 'form' => $form);
    }
    public function pwdAction()
    {
    	$form = new Form();
    	$form->add( array (
            'name' => 'oldPassword',
    		'filters' => array(
    				array('name' => 'Zend\Filter\StringTrim'),
    		),
    		'attributes' => array(
    				'required' => 'required',
    		),
            'options' => array (
                'label' => '旧密码'
            ) 
        ) );
    	$form->add( array (
    			'name' => 'newPassword',
    			'filters' => array(
    					array('name' => 'Zend\Filter\StringTrim'),
    			),
    			'attributes' => array(
    					'required' => 'required',
    					'minlength' => 6,
    					'maxlength' => 16,
    					'notemsg' => '请输入6-16位字符',
    			),
    			'options' => array (
    					'label' => '新密码'
    			)
    	) );
    	$form->add( array (
    			'name' => 'reNewPassword',
    			'filters' => array(
    					array('name' => 'Zend\Filter\StringTrim'),
    			),
    			'attributes' => array(
    					'required' => 'required',
    					'minlength' => 6,
    					'maxlength' => 16,
    					'notemsg' => '请输入6-16位字符',
    			),
    			'options' => array (
    					'label' => '确认密码'
    			)
    	) );
    	$form->add ( array (
    			'name' => 'submit',
    			'attributes' => array (
    					'value' => '提交',
    					'type' => 'submit',
    			)
    	) );
    	$request = $this->getRequest();
    	if($request->isPost()){
    		$form->setData($request->getPost());
    		if ($form->isValid()) {
    			$userTable = $this->_getUserTable();
    			$container = $this->_getSession();
    			$userInfo = $userTable->getUserByName($container->Name);
    			if ($userInfo && $userInfo->Password == md5($form->get('oldPassword')->getValue())) {
    				if ($form->get('newPassword')->getValue() ==  $form->get('reNewPassword')->getValue()) {
    					$userTable->update(array('Password' => md5($form->get('newPassword')->getValue())), array('ID' => $userInfo->ID));
    					$this->flashMessenger()->addSuccessMessage("恭喜您，密码已经修改成功！");
    				} else {
    					$this->flashMessenger()->addErrorMessage("输入的两次新密码不一致！", 'error');
    				}
    			} else {
    				$this->flashMessenger()->addErrorMessage("抱歉，旧密码验证失败！", 'error');
    			}
    		} else {
    			$this->flashMessenger()->addMessage($form->getMessages(), 'error');
    		}
    		return $this->redirect()->toRoute('backend' , array('controller' => 'user' , 'action' => 'pwd'));
    	}
    	
//     	if($this->flashMessenger()->hasMessages()){
// 			return array('form' => $form, 'msg' => $this->flashMessenger()->getMessages());
// 		} else {
			return array('form' => $form);
// 		}
    }
    function deleteAction()
    {
        $requery = $this->getRequest();
        if($userId = $requery->getQuery('id')){
            $table = $this->_getUserTable();
            $table->delete($userId);
            
            $this->trigger(ActionEvent::ACTION_DELETE);
            
            $this->_message('删除成功');
            return $this->redirect()->toUrl('/user');
            
        }
        throw new \Exception('没有ID参数');
    }
    
    private function _getUserTable(){
        return $this->getServiceLocator()->get('UserTable');
    }
    
    
    private function _getRoleTable(){
        return $this->getServiceLocator()->get('RoleTable');
    }
    
}
