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
}