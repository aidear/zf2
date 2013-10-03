<?php
/**
 * NavController.php
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
 * @version CVS: Id: NavController.php,v 1.0 2013-10-3 下午9:25:54 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Zend\Paginator\Paginator;
use Zend\Form\Form;
use Zend\Form\Element;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Custom\Mvc\ActionEvent;
use Custom\File\Uploader;

use Zend\Validator\File\Size;

use Zend\File\Transfer\Adapter\Http;
use Zend\View\Model\ViewModel;
use BackEnd\Form\NavCategoryForm;
use BackEnd\Model\Nav\NavCategory;

class NavController extends AbstractActionController
{
	function indexAction()
	{
		
	}
	function categoryAction()
	{
		$page = $this->params()->fromQuery('page' , 1);
        $table = $this->_getTable('NavCategoryTable');
        $name = $this->params()->fromQuery('name' , '');
        
        if($name){
            $re = $table->getByName($name);
        }else{
            $re = $table->getAllToPage();
        }
        
        $paginaction = new Paginator($re);
        $paginaction->setCurrentPageNumber($page);
        $paginaction->setItemCountPerPage(self::LIMIT);
//         $list = $paginaction->getCurrentItems()->toArray();
//         $parents = array();
//         foreach ($paginaction as $k=>$v) {
//         	if (isset($parents[$v->parentID])) {
//         		$paginaction[$k]['parentName']  = $parents[$v->parentID];
//         	} else {
//         		$tmp = $table->getOneById($v->parentID);
//         		$paginaction[$k]['parentName'] = $parents[$v->parentID] = $tmp->name;
//         	}
//         }
        return array('paginaction' => $paginaction);
	}
	public function saveAction()
	{
		$req = $this->getRequest();
		$form = new NavCategoryForm();
		
		$navCategoryTable = $this->_getTable('NavCategoryTable');
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		$navOption = array();
		foreach ($navCate as $k=>$v) {
			$navOption[$v['id']] = $v['name'];
		}
		array_unshift($navOption, '顶级分类');
		$form->get('parentID')->setValueOptions($navOption);
		if($req->isPost()){
			$params = $req->getPost();
			$form->bind(new NavCategory());
			$form->setData($params);
			if ($form->isValid()) {
				$params = $form->getData();
				
				$navCategory = new NavCategory();
				$navCategory->name = $params->name;
				$navCategory->desc = $params->desc;
				$navCatetory->keyword = $params->keyword;
				$navCatetory->parentID = $params->parentID;
				$navCatetory->isShow = $params->isShow;
				$navCatetory->order = $params->order;
				$navCategory->addTime = Utilities::getDateTime();
				$navCategoryTable->save($navCategory);
				
				//插入图片
				$navCatetory->imgUrl = $this->_insertImg($navCatetory->id);
				//更新表
				if($navCatetory->imgUrl){
					$this->_updateNavImage($navCatetory->id , $navCatetory->imgUrl );
				}
				
				return $this->redirect()->toRoute('backend' , array('controller' => 'nav' , 'action' => 'category'));
			}
			
		} elseif ($id = $this->params()->fromQuery('id')) {
			$navCategory = new NavCategory();
			$navCategoryTable = $this->_getTable('NavCategoryTable');
			
			$nav = $navCategoryTable->getID($id);
            $form->setData($nav);
		} else {
			
		}
		$form->get('isShow')->setValue(1);
		$form->get('order')->setValue(1);
		
		return array('navCategory' => new NavCategory() , 'form' => $form);
	}
	private function _insertImg($name)
	{
		$config = $this->_getConfig('upload');
		$config = $config['nav'];
		$files = $this->params()->fromFiles('imgUrl');
		if(! empty($files['name'])){
			return $config['showPath'] . Uploader::upload($files , $name , $config['uploadPath'] , $config);
		}else{
			return $this->params()->fromPost('imgUrl');
		}
	
		return null;
	}
	private function _updateNavImage($id , $imageFile){
		$table = $this->_getTable('NavCategoryTable');
		return $table->updateImage($id , $imageFile);
	}
}