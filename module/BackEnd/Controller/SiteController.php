<?php 
/**
 * SiteController.php
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
 * @version CVS: Id: SiteController.php,v 1.0 2013-12-16 下午7:57:34 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
class SiteController extends AbstractActionController
{
	public function indexAction()
	{
		$memberTable = $this->_getTable('MemberTable');
		$memberS = $memberTable->getStatistics();
		
		$rs = array('memberStatist' => $memberS);
		return new ViewModel($rs);
	}
	public function advApplyAction()
	{
	    $routeParams = array('controller' => 'site' , 'action' => 'advApply');
	    $prefixUrl = $this->url()->fromRoute(null, $routeParams);
	    $params = array();
	    $page = $this->params()->fromQuery('page' , 1);
	    $pageSize = $this->params()->fromQuery('pageSize');
	    $k = $this->params()->fromQuery('k' , '');
	    
	    
	    //params
	    if ($k) {
	        $params['k'] = $k;
	    }
	    if ($pageSize) {
	        $params['pageSize'] = $pageSize;
	    }
	    
	    $params['orderField'] = $this->params()->fromQuery('orderField', '');
	    $params['orderType'] = $this->params()->fromQuery('orderType', '');
	    
	    $removePageParams = $params;
	    
	    $params['page'] = $this->params()->fromQuery('page' , 1);
	    
	    $orderPageParams = $params;
	    
	    $act = $this->params()->fromQuery('act');
	    $paginaction = $this->_getAdvApplyPaginator($params);
	    
	    $items = $paginaction->getCurrentItems()->toArray();
// 	    foreach ($items as $k=>$v) {
	        // 	        $items[$k]['categoryName'] = isset($this->category[$v['category']]) ? $this->category[$v['category']] : '';
	        // 	        $items[$k]['area'] = $this->_getAreaLink($v['province'], $v['city']);
// 	    }
	    $startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
	    
	    $order = $this->_getOrder($prefixUrl, array('title', 'url', 'QQ', 'position','dailyView',
	        'email', 'tel', 'summary', 'build_time', 'add_time', 'ip'), $removePageParams);
	    
	    $assign = array(
	        'paginaction' => $paginaction,
	        'lists' => $items,
	        'startNumber' => $startNumber,
	        'orderQuery' => http_build_query($orderPageParams),
	        'query' => http_build_query($removePageParams),
	        'order' => $order,
            'k' => $k,
	    );
	    return $assign;
	}
	public function deleteAdvApplyAction()
	{
	    $id = $this->params()->fromQuery('id', '');
	    if (!$id) {
	        throw new \Exception('incomplete item id');
	    }
	    $table = $this->_getTable('AdvApplyTable');
	    $idStr = 'id='.str_replace(',', ' OR id=', $id);
	    $rs = $table->deleteMuti($idStr);
	    if ($rs) {
	        $this->_message('已删除', 'success');
	    } else {
	        $this->_message('删除失败!', 'error');
	    }
	    return $this->redirect()->toUrl('/site/advApply');
	}
	public function feedbackAction()
	{
	    $routeParams = array('controller' => 'site' , 'action' => 'feedback');
	    $prefixUrl = $this->url()->fromRoute(null, $routeParams);
	    $params = array();
	    $page = $this->params()->fromQuery('page' , 1);
	    $pageSize = $this->params()->fromQuery('pageSize');
	    $k = $this->params()->fromQuery('k' , '');
	     
	     
	    //params
	    if ($k) {
	        $params['k'] = $k;
	    }
	    if ($pageSize) {
	        $params['pageSize'] = $pageSize;
	    }
	     
	    $params['orderField'] = $this->params()->fromQuery('orderField', '');
	    $params['orderType'] = $this->params()->fromQuery('orderType', '');
	     
	    $removePageParams = $params;
	     
	    $params['page'] = $this->params()->fromQuery('page' , 1);
	     
	    $orderPageParams = $params;
	     
	    $act = $this->params()->fromQuery('act');
	    $paginaction = $this->_getFeedbackPaginator($params);
	     
	    $items = $paginaction->getCurrentItems()->toArray();
	    // 	    foreach ($items as $k=>$v) {
	    // 	        $items[$k]['categoryName'] = isset($this->category[$v['category']]) ? $this->category[$v['category']] : '';
	    // 	        $items[$k]['area'] = $this->_getAreaLink($v['province'], $v['city']);
	    // 	    }
	    $startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
	     
	    $order = $this->_getOrder($prefixUrl, array('content', 'contact','add_time', 'ip'), $removePageParams);
	     
	    $assign = array(
	        'paginaction' => $paginaction,
	        'lists' => $items,
	        'startNumber' => $startNumber,
	        'orderQuery' => http_build_query($orderPageParams),
	        'query' => http_build_query($removePageParams),
	        'order' => $order,
	        'k' => $k,
	    );
	    return $assign;
	}
	public function deleteFeedbackAction()
	{
	    $id = $this->params()->fromQuery('id', '');
	    if (!$id) {
	        throw new \Exception('incomplete item id');
	    }
	    $table = $this->_getTable('FeedbackTable');
	    $idStr = 'id='.str_replace(',', ' OR id=', $id);
	    $rs = $table->deleteMuti($idStr);
	    if ($rs) {
	        $this->_message('已删除', 'success');
	    } else {
	        $this->_message('删除失败!', 'error');
	    }
	    return $this->redirect()->toUrl('/site/feedback');
	}
	private function _getAdvApplyPaginator($params, $all = false)
	{
	    $page = isset($params['page']) ? $params['page'] : 1;
	    $order = array();
	    if ($params['orderField']) {
	        $order = array("{$params['orderField']}" => $params['orderType']);
	    }
	    $table = $this->_getTable('AdvApplyTable');
	    $paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
	    $paginator->setCurrentPageNumber($page)->setItemCountPerPage($all ? 10000 : (isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT));
	    return $paginator;
	}
	private function _getFeedbackPaginator($params, $all = false)
	{
	    $page = isset($params['page']) ? $params['page'] : 1;
	    $order = array();
	    if ($params['orderField']) {
	        $order = array("{$params['orderField']}" => $params['orderType']);
	    }
	    $table = $this->_getTable('FeedbackTable');
	    $paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
	    $paginator->setCurrentPageNumber($page)->setItemCountPerPage($all ? 10000 : (isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT));
	    return $paginator;
	}
}