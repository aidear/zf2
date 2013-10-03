<?php
namespace BackEnd\Controller;

use Custom\Mvc\ActionEvent;
// use Zend\EventManager\Event as ActionEvent;
// use Custom\Db\Adapter\SmcninternalAdapter;

use BackEnd\Model\Users\MyAcl;

use BackEnd\Model\Users\Role;

use BackEnd\Form\RoleForm;

use Zend\Paginator\Paginator;

use Custom\Mvc\Controller\AbstractActionController;

use Custom\Util\Utilities;

class RoleController extends AbstractActionController
{
    function indexAction(){
        $page = $this->params()->fromQuery('page' , 1);
        $table = $this->_getRoleTable();
        $list = $table->getAll();//print_r($list->toArray());die;
        return array('list' => $list);
    }
    
    function saveAction(){
        $request = $this->getRequest();
        $form = new RoleForm();
               
        if($request->isPost()){
            $table = $this->_getRoleTable();
            $params = $request->getPost();
            if (trim($params->Name) == '') {
            	$this->flashMessenger()->addMessage('角色名不允许为空！');
            	return $this->redirect()->toRoute('backend', array('controller' => 'role' , 'action' => 'save'));
            }
            $role = new Role();
            $role->RoleID = $params->RoleID;
            $role->Name = $params->Name;
            $role->CnName = $params->CnName;
            $role->ParentName = $params->ParentName;
            $role->AddTime = Utilities::getDateTime('Y-m-d h:i:s');
            if ($table->checkIsExist($role->Name)) {
            	$this->flashMessenger()->addMessage('角色名称重复，请更换其他名称！');
            	return $this->redirect()->toRoute('backend', array('controller' => 'role' , 'action' => 'save'));
            }
            $table->save($role);
            
            if($role->RoleID){
                $this->trigger(ActionEvent::ACTION_UPDATE);
            }else{
                $this->trigger(ActionEvent::ACTION_INSERT);
            }
            
            
            $acl = $this->_getAcl();
            $acl->acl->addRole($role);
            $acl->saveAcl();
            
            return $this->redirect()->toRoute('backend' , array('controller' => 'role' , 'action' => 'index'));
        }
        
        $table = $this->_getRoleTable();
        $roles = $table->getAll();
        $form->setParents($roles);
        $return = array('form' => $form);
        if($this->flashMessenger()->hasMessages()){
        	$return['msg'] = $this->flashMessenger()->getMessages();
        	return $return;
        }
        return $return;
    }
    
    function deleteAction(){
        $name = $this->params()->fromQuery('name');
        if($name){
            $table = $this->_getRoleTable();
            $table->deleteForName($name);
            
            $this->trigger(ActionEvent::ACTION_DELETE);
            $acl = $this->_getAcl();
            $acl->acl->removeRole($name);
        }
        return $this->redirect()->toUrl('/role/index');
//         return $this->redirect()->toRoute('backend' , array('controllter' => 'role' , 'action' => 'index'));
    }
    
    private function _getRoleTable(){
        return $this->getServiceLocator()->get('RoleTable');
    }
    
    private function _getCache(){
        return $this->getServiceLocator()->get('cache');
    }
    
    private function _getAcl(){
        return $this->getServiceLocator()->get('acl');
    }
}