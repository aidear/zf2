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
namespace FrontEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use FrontEnd\Model\Users\MemberTable;

class AjaxController extends AbstractActionController
{
	public function checkAction()
	{
		$rs = array();
		$s = $this->params()->fromQuery('s');
		switch ($s) {
			case 'user':
				$userName = $this->params()->fromPost('name');
				$table = $this->_getTable('MemberTable');
				$count = $table->checkExist(array('UserName' => $userName));
				$rs['code'] = $count ? 1 : 0;
				$rs['msg'] = $count;
				break;
			default:
				break;
		}
		
		print_r(json_encode($rs));die;
	}
}