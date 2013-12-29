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
use BackEnd\Form\PromotionForm;
use BackEnd\Model\Promotion\Promotion;
use Custom\Util\Utilities;
use Zend\View\Model\ViewModel;
class PromotionController extends AbstractActionController
{
	public function indexAction()
	{
		$cid = $this->params()->fromQuery('cid' , '');
		$title = $this->params()->fromQuery('title' , '');
		$routeParams = array('controller' => 'promotion' , 'action' => 'index');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$cid = $this->params()->fromQuery('cid' , '');
		$page = $this->params()->fromQuery('page' , 1);
		$pageSize = $this->params()->fromQuery('pageSize');
		
		if($title){
			$params['title'] = $title;
		}
		
		//params
		if ($cid) {
			$params['cid'] = $cid;
		}
		if ($title) {
			$params['title'] = $title;
		}
		if ($pageSize) {
			$params['pageSize'] = $pageSize;
		}
		$params['orderField'] = $this->params()->fromQuery('orderField', 'last_update');
		$params['orderType'] = $this->params()->fromQuery('orderType', 'DESC');
		$removePageParams = $params;
		
		$params['page'] = $this->params()->fromQuery('page' , 1);
		
		$orderPageParams = $params;
		
		$paginaction = $this->_getPromotionPaginator($params);
		$items = $paginaction->getCurrentItems()->toArray();
		$startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
		
		$proTypeTable = $this->_getTable('ProTypeTable');
		$ruleType = $proTypeTable->getlist(array('enable' => 1));
		$selectTypeValue = array('0' => '活动类型');
		foreach ($ruleType as $v) {
			$selectTypeValue[$v['type_code']] = $v['type_name'];
		}
		$order = $this->_getOrder($prefixUrl, array('type_name', 'points', 'start_time', 'end_time','is_active', 'last_update', 'updateUser'), $removePageParams);
		$assign = array(
				'rule_type' => $selectTypeValue,
				'paginaction' => $paginaction,
				'lists' => $items,
				'startNumber' => $startNumber,
				'cid' => $cid,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'order' => $order,
		);
		return new ViewModel($assign);
	}
	public function saveAction()
	{
		$id = $this->params()->fromQuery('id');
		$promotionTable = $this->_getTable('PromotionTable');
		if ($id) {
			$proItem = $promotionTable->getOneById($id);
		}
		$form = new PromotionForm();
		
		$proTypeTable = $this->_getTable('ProTypeTable');
		$ruleType = $proTypeTable->getlist(array('enable' => 1));
		$selectTypeValue = array();
		foreach ($ruleType as $v) {
			$selectTypeValue[$v['type_code']] = $v['type_name'];
		}
		$defRuleCode = isset($proItem->rule_code) ? $proItem->rule_code : null;
		$form->get('rule_code')->setValueOptions($selectTypeValue)->setValue($defRuleCode);
		
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			$promotion = new Promotion();
			$form->bind($promotion);
			$form->setData($params);
			if ($form->isValid()) {
				$container = $this->_getSession();
				$promotion->id = $params->id;
				$promotion->rule_code = $params->rule_code;
				$promotion->points = $params->points;
				$promotion->start_time = $params->start_time;
				$promotion->end_time = $params->end_time;
				$promotion->is_active = $params->is_active;
				$promotion->add_time = Utilities::getDateTime();
				$promotion->last_update = Utilities::getDateTime();
				$promotion->updateUser = $container->Name;
				$id = $promotionTable->save($promotion);
				
				if (empty( $promotion->id)) {
					$this->_message('添加成功！', 'success');
				} elseif ($promotion->id) {
					$this->_message('修改成功！', 'success');
				} else {
					$this->_message('编辑失败!', 'error');
				}
				
				return $this->redirect()->toUrl('/promotion');
			}
		} elseif ($id = $this->params()->fromQuery('id')) {
            $form->setData($proItem->toArray());
		}
		
		return array('form' => $form);
	}
	public function deleteAction()
	{
		$id = $this->params()->fromQuery('id', '');
		if (!$id) {
			throw new \Exception('incomplete item id');
		}
		$promotionTable = $this->_getTable('PromotionTable');
		$idStr = 'id='.str_replace(',', ' OR id=', $id);
		$rs = $promotionTable->deleteMuti($idStr);
		if ($rs) {
			$this->_message('已删除', 'success');
		} else {
			$this->_message('删除失败!', 'error');
		}
		return $this->redirect()->toUrl('/promotion');
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