<?php 
/**
 * PromotionController.php
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
 * @version CVS: Id: PromotionController.php,v 1.0 2013-12-25 下午9:36:56 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
class PromotionController extends AbstractActionController
{
	public function indexAction()
	{
		$routeParams = array('controller' => 'promotion' , 'action' => 'index');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$cid = $this->params()->fromQuery('cid' , '');
		$page = $this->params()->fromQuery('page' , 1);
		$pageSize = $this->params()->fromQuery('pageSize');
		$paginaction = $this->_getPromotionPaginator($params);
		$items = $paginaction->getCurrentItems()->toArray();
		$startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
		
		$removePageParams = $params;
		
		$params['page'] = $this->params()->fromQuery('page' , 1);
		
		$orderPageParams = $params;
		
		$order = $this->_getOrder($prefixUrl, array('title', 'show_icon', 'order', 'url','user_name', 'email', 'mobile', 'updateTime'), $removePageParams);
		$assign = array(
				'paginaction' => $paginaction,
				'lists' => $items,
				'startNumber' => $startNumber,
				'cid' => $cid,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'order' => $order,
		);
	}
	private function _getPromotionPaginator($params, $all = false)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if (isset($params['orderField'])) {
			$order = array("{$params['orderField']}" => $params['orderType']);
		}
		$table = $this->_getTable('PromotionTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage($all ? 10000 : (isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT));
		return $paginator;
	}
}