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
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;
use BackEnd\Model\Nav\Link;

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
	public function approvedUrlAction()
	{
	    $linkTable = $this->_getTable('LinkTable');
	    $recommendLinkTable = $this->_getTable('RecommendLinkTable');
	    $rs = array();
	    $id = $this->params()->fromPost('id');
	    $cid = $this->params()->fromPost('cid');
	    
	    $item = $recommendLinkTable->getOneById($id);
	    $exist = $linkTable->checkExistUrl($item->url);
	    if ($exist) {
	        $recommendLinkTable->updateFieldsByID(array('status' => 2), $id);
	        $rs = array('code' => -1, 'msg' => '网址已存在网址库中！');
	    } else {
	        $link = new Link();
	        $item->id = NULL;
	        $item->category = $cid;
	        $link->exchangeArray($item->toArray());
	        $link->recommend_id = $id;
	    	$linkTable->save($link);
	    	$recommendLinkTable->updateFieldsByID(array('status' => 1), $id);
	    	$rs = array('code' => 0, 'msg' => '已录入网址库，审核通过！', 'data' => $id);
	    }
	
	    return new JsonModel($rs);
	}
	public function saveCommonLinksAction()
	{
	    $category = $this->params()->fromPost('cate');
	    $html = $this->params()->fromPost('html');
	    $cache = array(
	    	'category' => $category,
	        'html' => $html,
	    );
	    $config = new Config($cache);
	    
	    
	    $writer = new PhpArray();
	    
	    //echo $writer->toString($conf);die;
	    //@file_put_contents(APPLICATION_PATH.'/data/sys_config.php', $writer->toString($conf));
	    $writer->toFile(APPLICATION_PATH.'/data/commonLinks/'.$category.'.php', $config);
	    return new JsonModel(array('code' => 0, 'msg' => 'ok'));
	}
}