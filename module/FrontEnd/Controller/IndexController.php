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
	    $customLinks = array();
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
		if (isset($_COOKIE['custom_links']) && !empty($_COOKIE['custom_links'])) {
		  $custom = explode('||', $_COOKIE['custom_links']);
		  if (!empty($custom)) {
		      foreach ($custom as $c) {
		          $explodeStr = explode('*', $c);
		          if (isset($explodeStr[0]) && !empty($explodeStr[0]) && isset($explodeStr[1]) && !empty($explodeStr[1])) {
		              $customLinks[] = array('title' => Utilities::unescape($explodeStr[0]), 'url' => $explodeStr[1]);
		          }
		      }
		  }
		}
		$navLists = array();
		$structNavLists = array();
		foreach ($nav as $k=>$v) {
			$where = "link.isShow=1";
			if (isset($city) && $city) {
				$where .= " AND (city IS NULL OR city='0' OR city='{$city}' OR province='0' OR province='{$city}')";
			}
// 			$links = $linkTable->getlist($where);
			$links = $linkTable->getAllLinksByNid($v['id'], $where);
			$tmp = array (
				'id' => $v['id'],
				'catPath' => $v['catPath'],
				'pid' => $v['parentID'],
				'name' => $v['name'],
				'img' => $v['imgUrl'],
				'line' => $v['line'],
				'links' => $links,
			);
			$navLists[] = $tmp;
			if (0 == $v['parentID']) {
				$structNavLists['rootNav'][$v['id']]['info'] = $v;
				$structNavLists['rootNav'][$v['id']]['links'] = $links;
				$structNavLists['rootNav'][$v['id']]['line'] = $v['line'];
			}
		}
		if ($navLists) {
			foreach ($navLists as $k=>$v) {
				$ids = trim($v['catPath'], ',');
				if (is_numeric($ids) && in_array($ids, array_keys($structNavLists['rootNav']))) {
					$structNavLists['rootNav'][$ids]['subNav'][] = $v;
				}
			}
		}
		$commonLinks = array();
		$commonLinksCate = array();
		if (file_exists(APPLICATION_PATH.'/data/commonLinks/category.php')) {
		    $commonLinksCate = include APPLICATION_PATH.'/data/commonLinks/category.php';
		    foreach ($commonLinksCate as $k=>$v) {
		        if (file_exists(APPLICATION_PATH.'/data/commonLinks/'.$k.'.php')) {
		            $commonLinks[$k] = include APPLICATION_PATH.'/data/commonLinks/'.$k.'.php';
		        } else {
		            $commonLinks[$k] = array();
		        }
		    }
		}
// 		if (file_exists('./data/commonLinks/1.php')) {
// 		    $commonLinks = include'./data/commonLinks/1.php';
// 		    $config = new \Zend\Config\Config($commonLinks);
// 		}
// 		print_r($commonLinks);die;
		$rs = array('commonLinksCate' => $commonLinksCate, 'commonLinks' => $commonLinks, 'customLinks' => $customLinks, 'struct' => $structNavLists, 'nav' => $navLists, 'provList' => $prov, 'defCity' => $defCity);
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