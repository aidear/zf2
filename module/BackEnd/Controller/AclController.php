<?php
namespace BackEnd\Controller;

use Custom\Mvc\ActionEvent;

use BackEnd\Form\AclForm;

use BackEnd\Model\Users\Acl;

use BackEnd\Model\Users\AclTable;

use BackEnd\Model\Users\ResourceTable;

use BackEnd\Model\Users\MyAcl;

use BackEnd\Model\Users\RoleTable;

use Zend\Db\ResultSet\ResultSet;
use Custom\Db\TableGateway\TableGateway;

use BackEnd\Model\Users\Resource;
use BackEnd\Model\Users\Role;
use Custom\Mvc\Controller\AbstractActionController;
use Zend\Cache\StorageFactory;
use Custom\Util\Utilities;


class AclController extends AbstractActionController
{
    function indexAction(){
        return array();
    }

    function allowAction(){
        $table = $this->_getAclTable();
        $form = new AclForm();
        $request = $this->getRequest();
        $acl = $this->_getAcl();
        
        if($request->isPost()){
            $postParams = $request->getPost();
            if($postParams->resources){
                $acl->acl->allow($postParams->role , $postParams->resources);
                foreach($postParams->resources as $resource){
                    $myacl = new Acl();
                    $myacl->ResourceName = $resource;
                    $myacl->RoleName = $postParams->role;
                    $myacl->AddTime = Utilities::getDateTime('Y-m-d h:i:s');
                    $table->save($myacl);
                }
            }
        
            if($postParams->deresources){
                $acl->acl->removeAllow($postParams->role , $postParams->deresources);
                $table->remove($postParams->role , $postParams->deresources);
            }
            $acl->saveAcl();
            
            $this->trigger(ActionEvent::ACTION_INSERT);
            $this->trigger(ActionEvent::ACTION_DELETE);
            return $this->redirect()->toUrl('/acl/allow?role=' . $postParams->role);
        }
        
        $role = $this->params()->fromQuery('role');
        if(!$role){
            throw new \Exception('没有Role参数');
        }
        
        $resources = $acl->acl->getResources();
        
        $hasResources = array();
        $noneResources = array();
        foreach($resources as $v){
            if($acl->acl->isAllowed($role , $v)){
                $hasResources[] = $v;
            }else{
                $noneResources[] = $v;
            }
        }
        
        $form->get('role')->setAttribute('value', $role);
        
        $form->setResource($noneResources)
        ->setDeResource($hasResources);
        return array('role' => $role ,'form' => $form);
    }

    function removeAllowAction(){
        $params = $this->getRequest()->getQuery();
        if($params->role && $params->deresource){
            $this->acl->acl->removeAllow($params->role , $params->deresource);
        }
        return $this->redirect()->toUrl('/backend/acl/addallow?role=' . $params->role);
    }

    function clearCacheAction(){
        $cache = $this->getServiceLocator()->get('cache');
        $cache->removeItem('backend_acl');
        echo '清理完成';
        exit;
    }
    
    private function _getAclTable(){
        return $this->getServiceLocator()->get('AclTable');
    }
    
    private function _getAcl(){
        return $this->getServiceLocator()->get('acl');
    }
}