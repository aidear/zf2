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
use BackEnd\Form\LinkForm;
use BackEnd\Model\Nav\Link;

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
        
        return array('paginaction' => $paginaction);
	}
	public function saveAction()
	{
		$req = $this->getRequest();
		$form = new NavCategoryForm();
		
		$navCategoryTable = $this->_getTable('NavCategoryTable');
// 		print_r($navCategoryTable->getCateTree());die;
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		$navOption = array();
		$navOption[0] = '顶级分类';
		foreach ($navCate as $k=>$v) {
			if ($v['catPath']) {
				$indent = count(explode(',', trim($v['catPath'], ',')));
			} else {
				$indent = 0;
			}
// 			$blank = '|--';
// 			for($i = $indent; $i > 0; $i --) {
// 				$blank .= '--';
// 			}
			
			$navOption[$v['id']] = $v['name'];
		}
// 		array_unshift($navOption, '顶级分类');
		$form->get('parentID')->setValueOptions($navOption);
		if($req->isPost()){
			$params = $req->getPost();
			$navCategory = new NavCategory();
			$form->bind($navCategory);
			$form->setData($params);
			if ($form->isValid()) {
				$params = $form->getData();
				
				$navCategory->id = $params->id;
				$navCategory->name = $params->name;
				$navCategory->desc = $params->desc;
				$navCategory->keyword = $params->keyword;
				$navCategory->parentID = $params->parentID;
				$navCategory->isShow = $params->isShow;
				$navCategory->order = $params->order;
				$navCategory->addTime = Utilities::getDateTime();
				
				$navCategory->catPath = $navCategoryTable->getPathByParent($navCategory->parentID);
				$id = $navCategoryTable->save($navCategory);
				
				//插入图片
				$navCategory->imgUrl = $this->_insertImg($navCategory->id ? $navCategory->id : $id);
				//更新表
				if($navCategory->imgUrl){
					$this->_updateNavImage($navCategory->id ? $navCategory->id : $id , $navCategory->imgUrl );
				}
				$this->_message('保存成功！');
				
				return $this->redirect()->toRoute('backend' , array('controller' => 'nav' , 'action' => 'category'));
			}
			
		} elseif ($id = $this->params()->fromQuery('id')) {
			$navCategory = new NavCategory();
			$navCategoryTable = $this->_getTable('NavCategoryTable');
			
			$nav = $navCategoryTable->getOneById($id);
            $form->setData($nav->toArray());
		} else {
			$form->get('isShow')->setValue(1);
			$form->get('order')->setValue(1);
		}
		
		
		return array('navCategory' => new NavCategory() , 'form' => $form);
	}
	public function itemsAction()
	{
		$cid = $this->params()->fromQuery('cid' , '');
		$page = $this->params()->fromQuery('page' , 1);
		$table = $this->_getTable('LinkTable');
		$navTable = $this->_getTable('NavCategoryTable');
		$title = $this->params()->fromQuery('title' , '');
		
		/*category info*/
		$cateInfo = $navTable->getOneById($cid);
		
		$where = '1=1';
		if ($cid) {
// 			$where['category'] = $cid;
			$where .= " AND category={$cid}";
		}
		if($title){
			$where .= " AND title LIKE '%{$title}%'";
		}
		$re = $table->getAllToPage($where);
		
		$paginaction = new Paginator($re);
		$paginaction->setCurrentPageNumber($page);
		$paginaction->setItemCountPerPage(self::LIMIT);
		$items = $paginaction->getCurrentItems()->toArray();
		foreach ($items as $k=>$v) {
			$items[$k]['categoryName'] = $cateInfo->name;
		}
		return array('paginaction' => $paginaction, 'lists' => $items, 'cateInfo' => $cateInfo);
	}
	public function addItemAction()
	{
		$cid = $this->params()->fromQuery('cid' , '');
		$cid = $cid ? $cid : $this->params()->fromPost('category', '');
		if (!$cid) {
			throw new \Exception('incomplete cid');
		}
		$form = new LinkForm();
		$categoryTable = $this->_getTable('NavCategoryTable');
		$cOjb = $categoryTable->getlist(array('isShow' => 1));
		$cateLists = array();
		foreach ($cOjb as $k=>$v) {
			$cateLists[$v['id']] = $v['name'];
		}
		
		$form->get('category')->setValueOptions($cateLists)->setValue($cid);
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			$link = new Link();
			$form->bind($link);
			$form->setData($params);
			if ($form->isValid()) {
				$params = $form->getData();
				$linkTable = $this->_getTable('LinkTable');
			
				$link->id = $params->id;
				$link->title = $params->title;
				$link->url = $params->url;
				$link->target = $params->target;
				$link->category = $params->category;
				$link->isShow = $params->isShow;
				$link->order = $params->order;
				$link->addTime = Utilities::getDateTime();
				if (false === strpos($link->url, 'http://')) {
					$link->url = 'http://'.$link->url;
				}
				$id = $linkTable->save($link);
				if (empty( $link->id)) {
					$this->_message('添加成功！', 'success');
				} elseif ($link->id) {
					$this->_message('修改成功！', 'success');
				} else {
					$this->_message('编辑失败!', 'error');
				}
			
				return $this->redirect()->toUrl('/nav/items?cid='.$link->category);
			}
		} elseif ($id = $this->params()->fromQuery('id')) {
			$linkTable = $this->_getTable('LinkTable');
			
			$liankItem = $linkTable->getOneById($id);
            $form->setData($liankItem->toArray());
		} else {
			$form->get('isShow')->setValue(1);
			$form->get('order')->setValue(1);
		}
		
		return array('form' => $form);
	}
	public function deleteAction()
	{
		$id = $this->params()->fromQuery('id', '');
		if (!$id) {
			throw new \Exception('incomplete category id');
		}
		$navTable = $this->_getTable('NavCategoryTable');
		$rs = $navTable->delete($id);
		if ($rs) {
			$this->_message('已删除', 'success');
		} else {
			$this->_message('删除失败!', 'error');
		}
		return $this->redirect()->toUrl('/nav/category');
	}
	public function deleteItemAction()
	{
		$id = $this->params()->fromQuery('id', '');
		$cid = $this->params()->fromQuery('cid', '');
		if (!$id) {
			throw new \Exception('incomplete item id');
		}
		$linkTable = $this->_getTable('LinkTable');
		$rs = $linkTable->delete($id);
		if ($rs) {
			$this->_message('已删除', 'success');
		} else {
			$this->_message('删除失败!', 'error');
		}
		return $this->redirect()->toUrl('/nav/items?cid='.$cid);
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