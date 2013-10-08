<?php
namespace FrontEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FrontEnd\Model\Nav;

class IndexController extends AbstractActionController
{
	protected $adminTable;
	public function indexAction()
	{
		$navCategoryTable = $this->_getTable('NavCategoryTable');
		$nav = $navCategoryTable->getlist(array('isShow' => 1));
		$linkTable = $this->_getTable('LinkTable');
		
		$navLists = array();
		foreach ($nav as $k=>$v) {
			$links = $linkTable->getlist(array('isShow' => 1, 'category' => $v['id']));
			$navLists[] = array (
				'name' => $v['name'],
				'img' => $v['imgUrl'],
				'links' => $links,
			);
		}
		
		$rs = array('rs' => 'Welcome!', 'nav' => $navLists);
		return new ViewModel($rs);
	}
	
	public function shortcutAction()
	{
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$url = $_SERVER['HTTP_REFERER'];
		$fileName = '导航.url';
		
		if (false !== strpos($userAgent, 'MSIE')) {
			$fileName = urlencode($fileName);
		}
		$Shortcut = "[InternetShortcut]
		URL={$url}
		IconFile{$url}favicon.ico
		IconIndex=0
		HotKey=1613
		IDList=
		[{000214A0-0000-0000-C000-000000000046}]
		Prop3=19,2";
		header("Content-Type: application/octet-stream;");
		header("Content-Disposition: attachment; filename=".$fileName);
		echo $Shortcut;die;
	}
}