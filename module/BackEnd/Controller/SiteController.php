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
class SiteController extends AbstractActionController
{
	public function indexAction()
	{
		$memberTable = $this->_getTable('MemberTable');
		$memberS = $memberTable->getStatistics();
		
		$rs = array('memberStatist' => $memberS);
		return new ViewModel($rs);
	}
}