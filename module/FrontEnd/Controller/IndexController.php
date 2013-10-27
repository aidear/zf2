<?php
namespace FrontEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FrontEnd\Model\Nav;
use FrontEnd\Model\Users\RegionTable;
use Custom\Util\Utilities;

class IndexController extends AbstractActionController
{
	protected $adminTable;
	public function indexAction()
	{
		$region = $this->_getTable('RegionTable');
		$prov = $region->getRegionByType(2);
		$navCategoryTable = $this->_getTable('NavCategoryTable');
		$nav = $navCategoryTable->getlist(array('isShow' => 1));
		$linkTable = $this->_getTable('LinkTable');
		$defCity = '';
		if (!(isset($_COOKIE['z_loc_c']) && $_COOKIE['z_loc_c'])) {
			$cityArr = Utilities::getCityByIP();
			$city = $cityArr['region_id'];
			$defCity = $cityArr['city'];
		} else {
			$name = Utilities::unescape($_COOKIE['z_loc_c']);
			$city = $region->getRidByName($name);
		}
		$navLists = array();
		foreach ($nav as $k=>$v) {
			$where = "isShow=1 AND category='{$v['id']}'";
			if (isset($city) && $city) {
				$where .= " AND (city IS NULL OR city='0' OR city='{$city}' OR province='0' OR province='{$city}')";
			}
			$links = $linkTable->getlist($where);
// 			$links = $linkTable->getlist(array('isShow' => 1, 'category' => $v['id']));
			$navLists[] = array (
				'name' => $v['name'],
				'img' => $v['imgUrl'],
				'line' => $v['line'],
				'links' => $links,
			);
		}
		$rs = array('rs' => 'Welcome!', 'nav' => $navLists, 'provList' => $prov, 'defCity' => $defCity);
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