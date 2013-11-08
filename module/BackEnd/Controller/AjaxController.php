<?php
/**
 * AjaxController.php
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
 * @version CVS: Id: AjaxController.php,v 1.0 2013-9-20 上午11:07:06 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use BackEnd\Model\Users\RegionTable;

class AjaxController extends AbstractActionController
{
	public function regionAction()
	{
		$pid = $this->params()->fromQuery('p');
		$regionTable = $this->_getTable('RegionTable');
		$assign = $regionTable->getRegionByPid($pid);
		print_r(json_encode($assign));die;
	}
	public function memberListAction()
	{
		$member = $this->_getTable('MemberTable');
		$rs = $member->getAll();
		$data = array();
		if ($rs) {
			foreach ($rs as $v) {
				$data[] = array(
					'UserID' => $v->UserID,
					'UserName' => $v->UserName,
					'Email' => $v->Email,
					'Mobile' => $v->Mobile,
				);
			}
		}
		return new JsonModel($data);
	}
	public function approvedAction()
	{
		$table = $this->_getTable('IdentityTable');
		$rs = array();
		$ids = $this->params()->fromPost('ids');
		$approved = $this->params()->fromPost('key');
		$approved = $approved == 1 ? 1 : 0;
		$ids = explode(',', trim($ids, ','));
		
		if($table->updateStatus($approved, $ids)) {
			$rs = array('code' => 0, 'msg' => '设置成功', 'data' => $ids);
		} else {
			$rs = array('code' => -1, 'msg' => '数据错误');
		}
		return new JsonModel($rs);
	}
}