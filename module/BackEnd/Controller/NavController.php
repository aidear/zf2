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
	protected  $op = array();
	protected  $category = array();
	function indexAction()
	{
		
	}
	function categoryAction()
	{
		$routeParams = array('controller' => 'nav' , 'action' => 'category');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
        $table = $this->_getTable('NavCategoryTable');
        $name = $this->params()->fromQuery('name' , '');
        
        if($name){
        	$params['name'] = $name;
        }
        $params['orderField'] = $this->params()->fromQuery('orderField', 'order');
        $params['orderType'] = $this->params()->fromQuery('orderType', __LIST_ORDER);
        
        $removePageParams = $params;
        
        $params['page'] = $this->params()->fromQuery('page' , 1);
        
        $orderPageParams = $params;
        
        
//         $paginaction = new Paginator($re);
        $paginaction = $this->_getNavPaginator($params);
        
        $navList = $paginaction->getCurrentItems()->toArray();
        
        foreach ($navList as $k=>$v) {
        	$navList[$k]['parentName'] = $table->getCateNameById($v['parentID']);
        }
        
        $startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
        $order = $this->_getOrder($prefixUrl, array('name', 'isShow', 'order', 'addTime'), $removePageParams);
        
        $assign = array(
        		'paginaction' => $paginaction, 
        		'navList' => $navList, 
        		'startNumber' => $startNumber,
        		'orderQuery' => http_build_query($orderPageParams),
        		'query' => http_build_query($removePageParams),
        		'order' => $order,
        );
        
        return new ViewModel($assign);
	}
	private function _getNavPaginator($params)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('NavCategoryTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(self::LIMIT);
		return $paginator;
	}
	public function saveAction()
	{
		$req = $this->getRequest();
		$form = new NavCategoryForm();
		
		$navCategoryTable = $this->_getTable('NavCategoryTable');
// 		print_r($navCategoryTable->getCateTree());die;
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		
		$fCategory = $this->_formatCategory($navCate);
		$navOption = array();
		$this->op[0] = '顶级分类';
		$this->_returnOptionValue($fCategory);
		if ($catid = $this->params()->fromQuery('id')) {
			unset($this->op[$catid]);
		}
		$form->get('parentID')->setValueOptions($this->op);
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
		$routeParams = array('controller' => 'nav' , 'action' => 'items');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
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
			$params['title'] = $title;
			$where .= " AND title LIKE '%{$title}%'";
		}
		
		//params
		if ($cid) {
			$params['cid'] = $cid;
		}
		if ($title) {
			$params['title'] = $title;
		}
		$navCategoryTable = $this->_getTable('NavCategoryTable');
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		
		$params['orderField'] = $this->params()->fromQuery('orderField', 'order');
		$params['orderType'] = $this->params()->fromQuery('orderType', __LIST_ORDER);
		
		$removePageParams = $params;
		
		$params['page'] = $this->params()->fromQuery('page' , 1);
		
		$orderPageParams = $params;
		
		$fCategory = $this->_formatCategory($navCate);
		$navOption = array();
		$this->op[0] = '所有分类';
		$this->category[0] = '所有分类';
		$this->_returnOptionValue($fCategory);
		
		
		$paginaction = $this->_getLinkPaginator($params);
		$items = $paginaction->getCurrentItems()->toArray();
		foreach ($items as $k=>$v) {
			$items[$k]['categoryName'] = isset($this->category[$v['category']]) ? $this->category[$v['category']] : '';
		}
		$startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
		
		$order = $this->_getOrder($prefixUrl, array('title', 'isShow', 'order', 'addTime'), $removePageParams);
		
		$assign = array(
				'category' => $this->op,
				'paginaction' => $paginaction,
				'lists' => $items,
				'cateInfo' => $cateInfo,
				'startNumber' => $startNumber,
				'cid' => $cid,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'order' => $order,
		);
		return $assign;
	}
	private function _getLinkPaginator($params)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('LinkTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(self::LIMIT);
		return $paginator;
	}
	public function addItemAction()
	{
		$cid = $this->params()->fromQuery('cid' , '');
		$cid = $cid ? $cid : $this->params()->fromPost('category', '');
		
		$id = $this->params()->fromQuery('id');
		if ($id) {
			$linkTable = $this->_getTable('LinkTable');
			$liankItem = $linkTable->getOneById($id);
			$cid = $liankItem->category;
		}
// 		if (!$cid) {
// 			throw new \Exception('incomplete cid');
// 		}
		$form = new LinkForm();
		$categoryTable = $this->_getTable('NavCategoryTable');
		$cOjb = $categoryTable->getlist(array('isShow' => 1));
		
		$fCategory = $this->_formatCategory($cOjb);
		$this->_returnOptionValue($fCategory);
		
		$form->get('category')->setValueOptions($this->op)->setValue($cid);
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
            $form->setData($liankItem->toArray());
		} else {
			$form->get('isShow')->setValue(1);
			$form->get('order')->setValue(1);
			$form->get('category')->setValue($cid);
		}
		
		return array('form' => $form, 'cid' => $cid);
	}
	public function deleteAction()
	{
		$id = $this->params()->fromQuery('id', '');
		if (!$id) {
			throw new \Exception('incomplete category id');
		}
		$navTable = $this->_getTable('NavCategoryTable');
		if ($navTable->checkNavCanDel($id)) {
			$this->_message('抱歉，此分类不能删除，因为当前分类或者子分类下有导航数据', 'error');
			return $this->redirect()->toUrl('/nav/category');
		}
		$rs = $navTable->deleteCateTree($id);
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
	private function _formatCategory($rows)
	{
		$tr = array();
		foreach ($rows as $k=>$v) {
			if (0 == $v['parentID'] && empty($v['catPath'])) {
				$tr[$v['id']]['id'] = $v['id'];
				$tr[$v['id']]['name'] = $v['name'];
				$tr[$v['id']]['depth'] = 0;
// 				array('id' => $v['id'], 'name' => $v['name'], 'depth' => 0);
			} else {
				$path = explode(',', trim($v['catPath'], ','));
				$count = count($path);
				$tmp = '$tr';
				for($i=0; $i < $count; $i ++) {
					$tmp .= "[$path[$i]]['sub']";
				}
				$tmp .= "[".$v['id']."]";//echo $tmp,'<br />';
				$val = array('id' => $v['id'], 'name' => $v['name'], 'depth' => $count);
				eval("$tmp=\$val;");
			}
		}
// 		echo '<pre>';
// 		print_r($tr);
// 		echo '</pre>';die;
		
		return $tr;
	}
	private function _returnOptionValue($fCategory)
	{
		foreach ($fCategory as $k=>$v) {
			$prefix = str_repeat(' -- ', $v['depth']);
			$this->op[$k] = '| '.$prefix.$v['name'];
			$this->category[$k] = $v['name'];
			if (isset($v['sub'])) {
				$this->_returnOptionValue($v['sub']);
			}
		}
	}
}