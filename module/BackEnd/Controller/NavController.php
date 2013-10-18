<?php
/**
 * NavController.php
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
 * @version CVS: Id: NavController.php,v 1.0 2013-10-3 下午9:25:54 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Zend\Paginator\Paginator;
use Zend\Form\Form;
use Zend\Form\Element;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use Custom\Mvc\ActionEvent;
use Custom\File\Uploader;

use Zend\Validator\File\Size;

use Zend\File\Transfer\Adapter\Http;
use Zend\View\Model\ViewModel;
use BackEnd\Form\NavCategoryForm;
use BackEnd\Model\Nav\NavCategory;
use BackEnd\Form\LinkForm;
use BackEnd\Model\Nav\Link;

class NavController extends AbstractActionController
{
	protected  $op = array();
	protected  $category = array();
	function indexAction()
	{
		
	}
	function categoryAction()
	{
		$routeParams = array('controller' => 'nav' , 'action' => 'category');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
        $table = $this->_getTable('NavCategoryTable');
        $name = $this->params()->fromQuery('name' , '');
        
        if($name){
        	$params['name'] = $name;
        }
        $params['orderField'] = $this->params()->fromQuery('orderField', 'order');
        $params['orderType'] = $this->params()->fromQuery('orderType', __LIST_ORDER);
        
        $removePageParams = $params;
        
        $params['page'] = $this->params()->fromQuery('page' , 1);
        
        $orderPageParams = $params;
        
        
//         $paginaction = new Paginator($re);
        $paginaction = $this->_getNavPaginator($params);
        
        $navList = $paginaction->getCurrentItems()->toArray();
        
        foreach ($navList as $k=>$v) {
        	$navList[$k]['parentName'] = $table->getCateNameById($v['parentID']);
        }
        
        $startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
        $order = $this->_getOrder($prefixUrl, array('name', 'isShow', 'order', 'addTime'), $removePageParams);
        
        $assign = array(
        		'paginaction' => $paginaction, 
        		'navList' => $navList, 
        		'startNumber' => $startNumber,
        		'orderQuery' => http_build_query($orderPageParams),
        		'query' => http_build_query($removePageParams),
        		'order' => $order,
        );
        
        return new ViewModel($assign);
	}
	private function _getNavPaginator($params)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('NavCategoryTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(self::LIMIT);
		return $paginator;
	}
	public function saveAction()
	{
		$req = $this->getRequest();
		$form = new NavCategoryForm();
		
		$navCategoryTable = $this->_getTable('NavCategoryTable');
// 		print_r($navCategoryTable->getCateTree());die;
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		
		$fCategory = $this->_formatCategory($navCate);
		$navOption = array();
		$this->op[0] = '顶级分类';
		$this->_returnOptionValue($fCategory);
		if ($catid = $this->params()->fromQuery('id')) {
			unset($this->op[$catid]);
		}
		$form->get('parentID')->setValueOptions($this->op);
		if($req->isPost()){
			$params = $req->getPost();
			$navCategory = new NavCategory();
			$form->bind($navCategory);
			$form->setData($params);
			if ($form->isValid()) {
				$params = $form->getData();
				
				$navCategory->id = $params->id;
				$navCategory->name = $params->name;
				$navCategory->desc = $params->desc;
				$navCategory->keyword = $params->keyword;
				$navCategory->parentID = $params->parentID;
				$navCategory->isShow = $params->isShow;
				$navCategory->order = $params->order;
				$navCategory->addTime = Utilities::getDateTime();
				
				$navCategory->catPath = $navCategoryTable->getPathByParent($navCategory->parentID);
				$id = $navCategoryTable->save($navCategory);
				
				//插入图片
				$navCategory->imgUrl = $this->_insertImg($navCategory->id ? $navCategory->id : $id);
				//更新表
				if($navCategory->imgUrl){
					$this->_updateNavImage($navCategory->id ? $navCategory->id : $id , $navCategory->imgUrl );
				}
				$this->_message('保存成功！');
				
				return $this->redirect()->toRoute('backend' , array('controller' => 'nav' , 'action' => 'category'));
			}
			
		} elseif ($id = $this->params()->fromQuery('id')) {
			$navCategory = new NavCategory();
			$navCategoryTable = $this->_getTable('NavCategoryTable');
			
			$nav = $navCategoryTable->getOneById($id);
            $form->setData($nav->toArray());
		} else {
			$form->get('isShow')->setValue(1);
			$form->get('order')->setValue(1);
		}
		
		
		return array('navCategory' => new NavCategory() , 'form' => $form);
	}
	public function itemsAction()
	{
		$routeParams = array('controller' => 'nav' , 'action' => 'items');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$cid = $this->params()->fromQuery('cid' , '');
		$page = $this->params()->fromQuery('page' , 1);
		$table = $this->_getTable('LinkTable');
		$navTable = $this->_getTable('NavCategoryTable');
		$title = $this->params()->fromQuery('title' , '');
		
		/*category info*/
		$cateInfo = $navTable->getOneById($cid);
		
		$where = '1=1';
		if ($cid) {
// 			$where['category'] = $cid;
			$where .= " AND category={$cid}";
		}
		if($title){
			$params['title'] = $title;
			$where .= " AND title LIKE '%{$title}%'";
		}
		
		//params
		if ($cid) {
			$params['cid'] = $cid;
		}
		if ($title) {
			$params['title'] = $title;
		}
		$navCategoryTable = $this->_getTable('NavCategoryTable');
		$navCate = $navCategoryTable->getlist(array('isShow' => 1));
		
		$params['orderField'] = $this->params()->fromQuery('orderField', 'order');
		$params['orderType'] = $this->params()->fromQuery('orderType', __LIST_ORDER);
		
		$removePageParams = $params;
		
		$params['page'] = $this->params()->fromQuery('page' , 1);
		
		$orderPageParams = $params;
		
		$fCategory = $this->_formatCategory($navCate);
		$navOption = array();
		$this->op[0] = '所有分类';
		$this->category[0] = '所有分类';
		$this->_returnOptionValue($fCategory);
		
		$act = $this->params()->fromQuery('act');
		if ($act == 'down') {
			$paginaction = $this->_getLinkPaginator($params, true);
		}
		$paginaction = $this->_getLinkPaginator($params);
		$items = $paginaction->getCurrentItems()->toArray();
		foreach ($items as $k=>$v) {
			$items[$k]['categoryName'] = isset($this->category[$v['category']]) ? $this->category[$v['category']] : '';
		}
		
		if ($act == 'down') {
			//require_once APPLICATION_MODULE_PATH.'/Model/PHPExcel.php';
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("Maarten Balliauw")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");
			$sheet = $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '标题')
								->setCellValue('B1', '网址')
								->setCellValue('C1', '分类')
								->setCellValue('D1', '是否显示')
								->setCellValue('E1', '排序')
								->setCellValue('F1', '添加时间');
			$sheet->getStyle('A1')->getFont()->setBold(true);
			$sheet->getStyle('B1')->getFont()->setBold(true);
			$sheet->getStyle('C1')->getFont()->setBold(true);
			$sheet->getStyle('D1')->getFont()->setBold(true);
			$sheet->getStyle('E1')->getFont()->setBold(true);
			$sheet->getStyle('F1')->getFont()->setBold(true);
			foreach ($items as $k =>$v) {
				$sheet->setCellValue('A'.($k+2), $v['title'])
							->setCellValue('B'.($k+2), $v['url'])
							->setCellValue('C'.($k+2), $v['categoryName'])
				->setCellValue('D'.($k+2), $v['isShow'] ? '显示' : '不显示')
				->setCellValue('E'.($k+2), $v['order'])
				->setCellValue('F'.($k+2), $v['addTime']);
			}
			$sheet->getColumnDimension('A')->setWidth(20);
			$sheet->getColumnDimension('B')->setWidth(40);
			$sheet->getColumnDimension('E')->setWidth(20);
			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('导航list');
			
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="导航list.xlsx"');
			header('Cache-Control: max-age=0');
			
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;
				
		}
		$startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
		
		$order = $this->_getOrder($prefixUrl, array('title', 'isShow', 'order', 'addTime'), $removePageParams);
		
		$assign = array(
				'category' => $this->op,
				'paginaction' => $paginaction,
				'lists' => $items,
				'cateInfo' => $cateInfo,
				'startNumber' => $startNumber,
				'cid' => $cid,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'order' => $order,
		);
		return $assign;
	}
	public function uploadAction()
	{	
		$file = $this->params()->fromFiles('srcFile');
		$data = array();
		if ($file['name']) {
			$PHPExcel = \PHPExcel_IOFactory::load($file['tmp_name']);
			/**读取excel文件中的第一个工作表*/
			$currentSheet = $PHPExcel->getSheet(0);
			/**取得最大的列号*/
			$allColumn = $currentSheet->getHighestColumn();
			/**取得一共有多少行*/
			$allRow = $currentSheet->getHighestRow();
			/**从第二行开始输出，因为excel表中第一行为列名*/
			for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
				/**从第A列开始输出*/
				$v = array();
				for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
					$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
					$v[] = $val;
// 					if($currentColumn == 'A')
// 					{
// 						echo GetData($val)."\t";
// 					}else{
						//echo $val;
						/**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
// 						echo iconv('utf-8','gb2312', $val)."\t";
// 					}
				}
// 				echo "</br>";
				$data[] = $v;
			}
// 			echo "\n";
		}
		if ($data) {
			$affect = 0;
			$navTable = $this->_getTable('NavCategoryTable');
			$navCate = $navTable->getlist(array('isShow' => 1));
			
			$fCategory = $this->_formatCategory($navCate);
			$navOption = array();
			$this->_returnOptionValue($fCategory);
			$insert = array();
			$link = $this->_getTable('LinkTable');
			$now = date('Y-m-d H:i:s');
			foreach ($data as $k=>$v) {
				$insert['title'] = $v[0];
				$insert['url'] = $v[1];
				if (in_array($v[2], $this->category)) {
					$tmp = array_flip($this->category);
					$insert['category'] = $tmp[$v[2]];
				} else {
					$insert['category'] = 0;
				}
				$insert['isShow'] = $v[3] == '显示' ? 1 : 0;
				$insert['order'] = $v[4];
				$insert['title'] = $v[0];
				$insert['addTime'] = $now;
				$flg = $link->insert($insert);
				if ($flg) {
					$affect ++;
				}
			}
			
		}
		print_r(json_encode(array('code' => 0, 'msg' => $affect)));die;
	}
	private function _getLinkPaginator($params, $all = false)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$order = array();
		if ($params['orderField']) {
			$order = array($params['orderField'] => $params['orderType']);
		}
		$table = $this->_getTable('LinkTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage($all ? 10000 : self::LIMIT);
		return $paginator;
	}
	public function addItemAction()
	{
		$cid = $this->params()->fromQuery('cid' , '');
		$cid = $cid ? $cid : $this->params()->fromPost('category', '');
		
		$id = $this->params()->fromQuery('id');
		if ($id) {
			$linkTable = $this->_getTable('LinkTable');
			$liankItem = $linkTable->getOneById($id);
			$cid = $liankItem->category;
		}
// 		if (!$cid) {
// 			throw new \Exception('incomplete cid');
// 		}
		$form = new LinkForm();
		$categoryTable = $this->_getTable('NavCategoryTable');
		$cOjb = $categoryTable->getlist(array('isShow' => 1));
		
		$fCategory = $this->_formatCategory($cOjb);
		$this->_returnOptionValue($fCategory);
		
		$form->get('category')->setValueOptions($this->op)->setValue($cid);
		$req = $this->getRequest();
		if ($req->isPost()) {
			$params = $req->getPost();
			$link = new Link();
			$form->bind($link);
			$form->setData($params);
			if ($form->isValid()) {
				$params = $form->getData();
				$linkTable = $this->_getTable('LinkTable');
			
				$link->id = $params->id;
				$link->title = $params->title;
				$link->url = $params->url;
				$link->target = $params->target;
				$link->category = $params->category;
				$link->isShow = $params->isShow;
				$link->order = $params->order;
				$link->addTime = Utilities::getDateTime();
				if (false === strpos($link->url, 'http://')) {
					$link->url = 'http://'.$link->url;
				}
				$id = $linkTable->save($link);
				if (empty( $link->id)) {
					$this->_message('添加成功！', 'success');
				} elseif ($link->id) {
					$this->_message('修改成功！', 'success');
				} else {
					$this->_message('编辑失败!', 'error');
				}
			
				return $this->redirect()->toUrl('/nav/items?cid='.$link->category);
			}
		} elseif ($id = $this->params()->fromQuery('id')) {
            $form->setData($liankItem->toArray());
		} else {
			$form->get('isShow')->setValue(1);
			$form->get('order')->setValue(1);
			$form->get('category')->setValue($cid);
		}
		
		return array('form' => $form, 'cid' => $cid);
	}
	public function deleteAction()
	{
		$id = $this->params()->fromQuery('id', '');
		if (!$id) {
			throw new \Exception('incomplete category id');
		}
		$navTable = $this->_getTable('NavCategoryTable');
		if ($navTable->checkNavCanDel($id)) {
			$this->_message('抱歉，此分类不能删除，因为当前分类或者子分类下有导航数据', 'error');
			return $this->redirect()->toUrl('/nav/category');
		}
		$rs = $navTable->deleteCateTree($id);
		if ($rs) {
			$this->_message('已删除', 'success');
		} else {
			$this->_message('删除失败!', 'error');
		}
		return $this->redirect()->toUrl('/nav/category');
	}
	public function deleteItemAction()
	{
		$id = $this->params()->fromQuery('id', '');
		$cid = $this->params()->fromQuery('cid', '');
		if (!$id) {
			throw new \Exception('incomplete item id');
		}
		$linkTable = $this->_getTable('LinkTable');
		$rs = $linkTable->delete($id);
		if ($rs) {
			$this->_message('已删除', 'success');
		} else {
			$this->_message('删除失败!', 'error');
		}
		return $this->redirect()->toUrl('/nav/items?cid='.$cid);
	}
	private function _insertImg($name)
	{
		$config = $this->_getConfig('upload');
		$config = $config['nav'];
		$files = $this->params()->fromFiles('imgUrl');
		if(! empty($files['name'])){
			return $config['showPath'] . Uploader::upload($files , $name , $config['uploadPath'] , $config);
		}else{
			return $this->params()->fromPost('imgUrl');
		}
	
		return null;
	}
	private function _updateNavImage($id , $imageFile){
		$table = $this->_getTable('NavCategoryTable');
		return $table->updateImage($id , $imageFile);
	}
	private function _formatCategory($rows)
	{
		$tr = array();
		foreach ($rows as $k=>$v) {
			if (0 == $v['parentID'] && empty($v['catPath'])) {
				$tr[$v['id']]['id'] = $v['id'];
				$tr[$v['id']]['name'] = $v['name'];
				$tr[$v['id']]['depth'] = 0;
// 				array('id' => $v['id'], 'name' => $v['name'], 'depth' => 0);
			} else {
				$path = explode(',', trim($v['catPath'], ','));
				$count = count($path);
				$tmp = '$tr';
				for($i=0; $i < $count; $i ++) {
					$tmp .= "[$path[$i]]['sub']";
				}
				$tmp .= "[".$v['id']."]";//echo $tmp,'<br />';
				$val = array('id' => $v['id'], 'name' => $v['name'], 'depth' => $count);
				eval("$tmp=\$val;");
			}
		}
// 		echo '<pre>';
// 		print_r($tr);
// 		echo '</pre>';die;
		
		return $tr;
	}
	private function _returnOptionValue($fCategory)
	{
		foreach ($fCategory as $k=>$v) {
			$prefix = str_repeat(' -- ', $v['depth']);
			$this->op[$k] = '| '.$prefix.$v['name'];
			$this->category[$k] = $v['name'];
			if (isset($v['sub'])) {
				$this->_returnOptionValue($v['sub']);
			}
		}
	}
}