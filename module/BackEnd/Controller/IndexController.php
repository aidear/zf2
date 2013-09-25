<?php
namespace BackEnd\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	protected $adminTable;
	public function indexAction()
	{
		$rs = array('rs' => 'Welcome!');
// 		return $this->redirect()->toUrl('/login');
		return new ViewModel($rs);
	}
	public function getAdminTable()
	{
		if (!$this->adminTable) {
			$sm = $this->getServiceLocator();
			$this->adminTable = $sm->get('BackEnd\Model\AdminTable');
		}
		return $this->adminTable;
	}
}