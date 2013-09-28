<?php
namespace FrontEnd\Controller;

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
	
	public function shortcutAction()
	{
		$url = $_SERVER['HTTP_REFERER'];
		$Shortcut = "[InternetShortcut]
		URL={$url}
		IconFile{$url}favicon.ico
		IconIndex=0
		HotKey=1613
		IDList=
		[{000214A0-0000-0000-C000-000000000046}]
		Prop3=19,2";
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=导航.url");
		echo $Shortcut;die;
	}
}