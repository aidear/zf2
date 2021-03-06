<?php
/**
 * MemberController.php
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
 * @version CVS: Id: MemberController.php,v 1.0 2013-9-18 下午11:17:43 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Controller;

use Zend\Paginator\Paginator;
use Custom\Mvc\Controller\AbstractActionController;
use Custom\Util\Utilities;
use BackEnd\Model\Users\Member;
use BackEnd\Form\MemberForm;
use Custom\Mvc\ActionEvent;
use Custom\File\Uploader;
use Zend\View\Model\ViewModel;

class MemberController extends AbstractActionController
{
	public function indexAction()
	{
		$routeParams = array('controller' => 'member' , 'action' => 'index');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		
        $table = $this->_getTable('MemberTable');
        $k = $this->params()->fromQuery('k' , '');
        
        if($k){
        	$params['k']  = $k;
        }
        $params['orderField'] = $this->params()->fromQuery('orderField', '');
        $params['orderType'] = $this->params()->fromQuery('orderType', '');
        if ($this->params()->fromQuery('pageSize')) {
        	$params['pageSize'] = $this->params()->fromQuery('pageSize');
        }
        $noFilterParams = $params;
        
        //filter
        $s_fields = $this->params()->fromQuery('s_fields', '');
        $s_opts = $this->params()->fromQuery('s_opts', '');
        $s_vals = $this->params()->fromQuery('s_vals', '');
        $s_rels = $this->params()->fromQuery('s_rels', '');
        if ($s_fields) {
        	$params['s_fields'] = $s_fields;
        }
        if ($s_opts) {
        	$params['s_opts'] = $s_opts;
        }
        if ($s_vals) {
        	$params['s_vals'] = $s_vals;
        }
        if ($s_rels) {
        	$params['s_rels'] = $s_rels;
        }
        $removePageParams = $params;
        
        $params = $this->_dealFilter($params);
        
        $params['page'] = $this->params()->fromQuery('page' , 1);
        
        $orderPageParams = $params;
        
        $paginaction = $this->_getNavPaginator($params);
        
        $startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
        $order = $this->_getOrder($prefixUrl, array('UserName', 'Points', 'Email', 'Mobile', 'Nick', 'LastLogin', 'LoginCount', 'LastUpdate'), $removePageParams);
        
        $assign = array(
        		'paginaction' => $paginaction, 
        		'startNumber' => $startNumber,
        		'orderQuery' => http_build_query($orderPageParams),
        		'query' => http_build_query($removePageParams),
        		'filterQuery' => http_build_query($noFilterParams),
        		'order' => $order,
        		'k' => $k,
        );
        return new ViewModel($assign);
	}
	public function contactAction()
	{
		$routeParams = array('controller' => 'member' , 'action' => 'contact');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
	
		$table = $this->_getTable('MemberTable');
		$k = $this->params()->fromQuery('k' , '');
	
		if($k){
			$params['k']  = $k;
		}
		$params['orderField'] = $this->params()->fromQuery('orderField', '');
		$params['orderType'] = $this->params()->fromQuery('orderType', '');
		if ($this->params()->fromQuery('pageSize')) {
			$params['pageSize'] = $this->params()->fromQuery('pageSize');
		}
		$noFilterParams = $params;
		//filter
        $s_fields = $this->params()->fromQuery('s_fields', '');
        $s_opts = $this->params()->fromQuery('s_opts', '');
        $s_vals = $this->params()->fromQuery('s_vals', '');
        $s_rels = $this->params()->fromQuery('s_rels', '');
        if ($s_fields) {
        	$params['s_fields'] = $s_fields;
        }
        if ($s_opts) {
        	$params['s_opts'] = $s_opts;
        }
        if ($s_vals) {
        	$params['s_vals'] = $s_vals;
        }
        if ($s_rels) {
        	$params['s_rels'] = $s_rels;
        }
        $removePageParams = $params;
        
        $params = $this->_dealFilter($params);
	
		$params['page'] = $this->params()->fromQuery('page' , 1);
	
		$orderPageParams = $params;
	
		$paginaction = $this->_getNavPaginator($params);
	
		$startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
		$order = $this->_getOrder($prefixUrl, array('UserName', 'Points', 'Email', 'Tel', 'QQ', 'MSN', 'Mobile', 'Nick', 'LastLogin', 'LoginCount', 'LastUpdate'), $removePageParams);
	
		$assign = array(
				'paginaction' => $paginaction,
				'startNumber' => $startNumber,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'filterQuery' => http_build_query($noFilterParams),
				'order' => $order,
				'k' => $k,
		);
		return new ViewModel($assign);
	}
	public function allAction()
	{
		$routeParams = array('controller' => 'member' , 'action' => 'all');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$cityList = array();
		$districtList = array();
	
		$table = $this->_getTable('MemberTable');
		$k = $this->params()->fromQuery('k' , '');
		
		$province = $this->params()->fromQuery('Province' , '');
		$city = $this->params()->fromQuery('City' , '');
		$district = $this->params()->fromQuery('District' , '');
	
		if($k){
			$params['k']  = $k;
		}
		
		$region = $this->_getTable('RegionTable');
		$prov = $region->getSelectRegion(2);
		$provK = array_keys($prov);
// 		$dC = isset($userInfo['Province']) ? $userInfo['Province'] : array_shift($provK);
// 		$city = $region->getSelectRegion(3, $dC);
// 		$cityK = array_keys($city);
// 		$dDi = isset($userInfo['City']) ? $userInfo['City'] : array_shift($cityK);
// 		$district = $region->getSelectRegion(4, $dDi);

		$regionList['Province'] = $prov;
		if ($province) {
			$cityList = $region->getSelectRegion(3, $province);
			$params['Province'] = $province;
		}
		if ($city) {
			$districtList = $region->getSelectRegion(4, $city);
			$params['City'] = $city;
		}
		if ($district) {
			$params['District'] = $district;
		}
		$regionList['City'] = $cityList;
		$regionList['District'] = $districtList;
		$params['orderField'] = $this->params()->fromQuery('orderField', '');
		$params['orderType'] = $this->params()->fromQuery('orderType', '');
		if ($this->params()->fromQuery('pageSize')) {
			$params['pageSize'] = $this->params()->fromQuery('pageSize');
		}
		$noFilterParams = $params;
		//filter
        $s_fields = $this->params()->fromQuery('s_fields', '');
        $s_opts = $this->params()->fromQuery('s_opts', '');
        $s_vals = $this->params()->fromQuery('s_vals', '');
        $s_rels = $this->params()->fromQuery('s_rels', '');
        if ($s_fields) {
        	$params['s_fields'] = $s_fields;
        }
        if ($s_opts) {
        	$params['s_opts'] = $s_opts;
        }
        if ($s_vals) {
        	$params['s_vals'] = $s_vals;
        }
        if ($s_rels) {
        	$params['s_rels'] = $s_rels;
        }
        $removePageParams = $params;
        
        $params = $this->_dealFilter($params);
	
		$params['page'] = $this->params()->fromQuery('page' , 1);
	
		$orderPageParams = $params;
	
		$paginaction = $this->_getNavPaginator($params);
	
		$startNumber = 1+($params['page']-1)*$paginaction->getItemCountPerPage();
		$order = $this->_getOrder($prefixUrl, array('UserName', 'Points', 'Email', 'Mobile', 'Nick',
		        'Tel', 'TrueName', 'Gender', 'Address', 'QQ', 'MSN' , 'LastLogin', 'LoginCount', 'LastUpdate'), $removePageParams);
	
		$region = $this->_getTable('RegionTable');
		
		$assign = array(
				'paginaction' => $paginaction,
				'startNumber' => $startNumber,
				'orderQuery' => http_build_query($orderPageParams),
				'query' => http_build_query($removePageParams),
				'filterQuery' => http_build_query($noFilterParams),
				'order' => $order,
				'region' => $region->getRegionList(),
				'k' => $k,
				'regionList' => $regionList,
				'Province' => $province,
				'City' => $city,
				'District' => $district,
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
		$table = $this->_getTable('MemberTable');
		$paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
		$paginator->setCurrentPageNumber($page)->setItemCountPerPage(isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT);
		return $paginator;
	}
	private function _getIdentityPaginator($params)
	{
	    $page = isset($params['page']) ? $params['page'] : 1;
	    $order = array();
	    if ($params['orderField']) {
	        $order = array($params['orderField'] => $params['orderType']);
	    }
	    $table = $this->_getTable('IdentityTable');
	    $paginator = new Paginator($table->formatWhere($params)->getListToPaginator($order));
	    $paginator->setCurrentPageNumber($page)->setItemCountPerPage(isset($params['pageSize']) ? $params['pageSize'] : self::LIMIT);
	    return $paginator;
	}
	public function saveAction()
	{
		$requery = $this->getRequest();
		$form = new MemberForm($this->_getTable('Zend\Db\Adapter\Adapter'));
		if($requery->isPost()){
			$this->_memberSave($form, $requery);
		}
		return $this->_memberEdit($form);
	}
	public function contactSaveAction()
	{
		$requery = $this->getRequest();
		$form = new MemberForm($this->_getTable('Zend\Db\Adapter\Adapter'));
		if($requery->isPost()){
			$this->_memberSave($form, $requery, 'contact');
		}
		return $this->_memberEdit($form);
	}
	public function allSaveAction()
	{
		$requery = $this->getRequest();
		$form = new MemberForm($this->_getTable('Zend\Db\Adapter\Adapter'));
		if($requery->isPost()){
			$this->_memberSave($form, $requery, 'all');
		}
		return $this->_memberEdit($form);
	}
	private function _memberEdit($form)
	{
		$UserID = $this->params()->fromQuery('id');
		if ($UserID) {
			$userInfo = $this->_getMemberByID($UserID);
			$form->setData($userInfo);
		}
		$region = $this->_getTable('RegionTable');
		$prov = $region->getSelectRegion(2);
		$provK = array_keys($prov);
		$dC = isset($userInfo['Province']) ? $userInfo['Province'] : array_shift($provK);
		$city = $region->getSelectRegion(3, $dC);
		$cityK = array_keys($city);
		$dDi = isset($userInfo['City']) ? $userInfo['City'] : array_shift($cityK);
		$district = $region->getSelectRegion(4, $dDi);
		$form->get('Province')->setValueOptions($prov);
		$form->get('City')->setValueOptions($city);
		$form->get('District')->setValueOptions($district);
		$form->get('Source')->setValue(2);
		
		return array('member' => new Member() , 'form' => $form);
	}
	private function _memberSave($form, $requery, $action = 'index')
	{
		$params = $requery->getPost();
		$member = new Member($this->_getTable('Zend\Db\Adapter\Adapter'));
			
		$member->UserID = $params->UserID;
		$member->UserName = $params->UserName;
		// 			$member->Password = md5($params->Password);
		$member->Nick = $params->Nick;
		// 			$member->ImgUrl = $params->ImgUrl;
		$member->Email = $params->Email;
		$member->Mobile = $params->Mobile;
		$member->Points = $params->Points;
		$member->TrueName = $params->TrueName;
		$member->Gender = $params->Gender;
		$member->Province = $params->Province;
		$member->City = $params->City;
		$member->District = $params->District;
		$member->Address = $params->Address;
		$member->Tel = $params->Tel;
		$member->Birthday = $params->Birthday;
		$member->QQ = $params->QQ;
		$member->MSN = $params->MSN;
		$member->Status = $params->Status;
		$member->Source = $params->Source;
		$member->LastUpdate = $member->AddTime = Utilities::getDateTime();
		$File = $this->params()->fromFiles('ImgUrl');
		$params->ImgUrl = $File['name'];
		$form->setData($params);
			
		$form->setInputFilter($member->getInputFilter());
		
		if ($form->isValid()) {
			$size = new \Zend\Validator\File\Size(array('min' => 0,'max' => '255kB'));
			$adapter = new \Zend\File\Transfer\Adapter\Http();
			$adapter->setValidators(array($size), $File['name']);
			if (!$adapter->isValid() && !empty($params->ImgUrl)){
				$dataError = $adapter->getMessages();
				$error = array();
				foreach($dataError as $key=>$row)
				{
					$error[] = $row;
				}
				$form->setMessages(array('ImgUrl'=>$error ));
			} else {
				$table = $this->_getTable('MemberTable');
				$chkExist = 1;
		
				$id = $table->save($member);
					
				//插入图片
				$member->ImgUrl = $this->_insertImg($member->UserID ? $member->UserID : $id);
				//更新表
				if($member->ImgUrl){
					$this->_updateMemberImage($member->UserID ? $member->UserID : $id, $member->ImgUrl );
				}
		
				if($member->UserID){
					$this->trigger(ActionEvent::ACTION_UPDATE);
					$this->_message('用户名为'.$member->UserName.'的会员资料更新成功');
				}else{
					$this->_message('用户名为'.$member->UserName.'的会员添加成功');
					$this->trigger(ActionEvent::ACTION_INSERT);
				}
				return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => $action));
			}
		}
	}
	function identityAction()
	{
		$page = $this->params()->fromQuery('page' ,1);
		$pageSize = $this->params()->fromQuery('pageSize');
		$routeParams = array('controller' => 'member' , 'action' => 'identity');
		$prefixUrl = $this->url()->fromRoute(null, $routeParams);
		$params = array();
		$k = $this->params()->fromQuery('k' , '');
		if (($this->params()->fromQuery('status' , '') && $this->params()->fromQuery('status' , '') != 'no') || $this->params()->fromQuery('status' , '') == 0) {
		  $params['status'] = $this->params()->fromQuery('status' , '');
		}
		if ($this->params()->fromQuery('type' , '')) {
		    $params['type'] = $this->params()->fromQuery('type' , '');
		}
		if(!empty($k)){
		    $params['k']  = $k;
		}
		
		$params['orderField'] = $this->params()->fromQuery('orderField', '');
		$params['orderType'] = $this->params()->fromQuery('orderType', '');
		if ($this->params()->fromQuery('pageSize')) {
		    $params['pageSize'] = $this->params()->fromQuery('pageSize');
		}
		$noFilterParams = $params;
		
		$removePageParams = $params;
		
		$params['page'] = $this->params()->fromQuery('page' , 1);
		
		$orderPageParams = $params;
		
		$table = $this->_getTable('IdentityTable');
		$member = $this->_getTable('MemberTable');
// 		$paginaction = new Paginator($table->getPage());
		$paginaction = $this->_getIdentityPaginator($params);
		$paginaction->setCurrentPageNumber($page);
		$paginaction->setItemCountPerPage($pageSize ? $pageSize : self::LIMIT);
		$idRecords = $paginaction->getCurrentItems()->toArray();
		$startNumber = 1+($page-1)*$paginaction->getItemCountPerPage();
		foreach ($idRecords as $key=>$v) {
// 			$idRecords[$k]['UserName'] = $member->getUserNameByID($v['user_id']);
			$idRecords[$key]['type_name'] = $v['type'] == 1 ? '个人' : '企业';
			$check_desc = '<span class="red">未审核</span>';
			switch($v['status']) {
				case 1:
					$check_desc = "已审核";
					break;
				case 2:
					$check_desc = '未通过';
					break;
				default:
					break;
			}
			$idRecords[$key]['check_desc'] = $check_desc;
		}
		$url = '/member/identity';
		if ($pageSize) {
			$url .= "?pageSize=".$pageSize;
		}
		
		$order = $this->_getOrder($prefixUrl, array('UserName', 'type', 'name', 'code', 'status', 'addTime', 'lastApproved'), $removePageParams);
		
		$assign = array(
		    'paginaction' => $paginaction,
		    'records' => $idRecords,
		    'startNumber' => $startNumber,
		    'orderQuery' => http_build_query($orderPageParams),
		    'query' => http_build_query($removePageParams),
		    'filterQuery' => http_build_query($noFilterParams),
		    'order' => $order,
		    'k' => $k,
		    'params' => $params
		);
		return $assign;
	}
	function pwdAction()
	{
		$form = new MemberForm();
		$post = $this->getRequest();
		$UserID = $this->params()->fromQuery('id');
		$userInfo = $this->_getMemberByID($UserID);
		if($post->isPost()){
			$this->_memberPwdSave($post);
		}
		return $this->_memberPwdEdit($form, $userInfo);
	}
	function allPwdAction()
	{
		$form = new MemberForm();
		$post = $this->getRequest();
		$UserID = $this->params()->fromQuery('id');
		$userInfo = $this->_getMemberByID($UserID);
		if($post->isPost()){
			$this->_memberPwdSave($post, 'all');
		}
		return $this->_memberPwdEdit($form, $userInfo);
	}
	private function _memberPwdSave($post, $action = 'index')
	{
		$password = trim($post->getPost()->Password);
		$rePassword = trim($post->getPost()->rePassword);
		$UserID = $post->getPost()->UserID;
		if (empty($password) || empty($rePassword)) {
			$this->flashMessenger()->addErrorMessage('请输入密码后提交');
		
		} elseif ($password == $rePassword) {
			$memberTable = $this->_getTable('MemberTable');
			$memberTable->updateFieldsByID(array('Password' => md5($password)), $UserID);
			$this->flashMessenger()->addSuccessMessage('会员'.$userInfo['UserName'].'密码已重置成功');
			return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => $action));
		} else {
			$this->flashMessenger()->addErrorMessage('抱歉，两次输入的密码不一致！请重新输入');
		}
		if ($action == 'all') {
			return $this->redirect()->toUrl("/member/allPwd?id={$UserID}");
		}
		return $this->redirect()->toUrl("/member/pwd?id={$UserID}");
	}
	private function _memberPwdEdit($form, $userInfo)
	{
		unset($userInfo['Password']);
		$form->setData($userInfo);
		$form->add ( array (
				'name' => 'rePassword',
				'options' => array (
						'label' => '确认密码'
				)
		) );
		
		return array('form' => $form, 'user' => $userInfo);
	}
	function sendMailAction()
	{
		$table = $this->_getTable('MemberTable');
		$userid = $id = $this->params()->fromQuery('id');
		if (!$userid) {
			throw new \Exception('id is required!');
		}
		$req = $this->getRequest();
		if ($req->isPost()) {
			$this->_memberSendMailSave($req);
		}
		return $this->_memberSendMailEdit();
	}
	function contactSendMailAction()
	{
		$table = $this->_getTable('MemberTable');
		$userid = $id = $this->params()->fromQuery('id');
		if (!$userid) {
			throw new \Exception('id is required!');
		}
		$req = $this->getRequest();
		if ($req->isPost()) {
			$this->_memberSendMailSave($req, 'contact');
		}
		return $this->_memberSendMailEdit();
	}
	function allSendMailAction()
	{
		$table = $this->_getTable('MemberTable');
		$userid = $id = $this->params()->fromQuery('id');
		if (!$userid) {
			throw new \Exception('id is required!');
		}
		$req = $this->getRequest();
		if ($req->isPost()) {
			$this->_memberSendMailSave($req, 'all');
		}
		return $this->_memberSendMailEdit();
	}
	private function _memberSendMailSave($req, $action = 'index')
	{
		set_time_limit(0);
		$subject = $req->getPost('subject');
		$email = $req->getPost('email');
		$content = $req->getPost('content');
		$mail = new \BackEnd\Model\System\Mail();
		$email = explode(',', $email);
		$email = array_filter($email);
			
		$file = $this->params()->fromFiles('attach');
		$mail->sendHtml($email, $subject, $content, $file);
		$this->_message('信息已投递至'.implode(',', $email).'地址');
		return $this->redirect()->toRoute('backend' , array('controller' => 'member' , 'action' => $action));
	}
	private function _memberSendMailEdit()
	{
		$table = $this->_getTable('MemberTable');
		$userid = $id = $this->params()->fromQuery('id');
		if (strpos($userid, ',') !== false) {
			$userid = explode(',', $userid);
		} else {
			$userid = array($userid);
		}
		$user = $table->getUserListByID($userid)->toArray();
		$assign = array();
		foreach ($user as $u) {
			$assign['email'][] = $u['Email'];
			$assign['name'][] = $u['UserName'];
		}
		return array('user' => $assign, 'id' => $id);
	}
	function deleteAction()
	{
		$requery = $this->getRequest();
		if($userId = $requery->getQuery('id')){
			$table = $this->_getTable('MemberTable');
			$table->delete($userId);
	
			$this->trigger(ActionEvent::ACTION_DELETE);
	
			$this->_message('删除成功');
			return $this->redirect()->toUrl('/member');
	
		}
		throw new \Exception('没有ID参数');
	}
	function allDeleteAction()
	{
		$requery = $this->getRequest();
		if($userId = $requery->getQuery('id')){
			$table = $this->_getTable('MemberTable');
			$table->delete($userId);
	
			$this->trigger(ActionEvent::ACTION_DELETE);
	
			$this->_message('删除成功');
			return $this->redirect()->toUrl('/member/all');
	
		}
		throw new \Exception('没有ID参数');
	}
	function deleteIdentityAction()
	{
		$requery = $this->getRequest();
		if($id = $requery->getQuery('id')){
			$table = $this->_getTable('IdentityTable');
			$idStr = 'id='.str_replace(',', ' OR id=', $id);
			$table->deleteMuti($idStr);
	
			$this->trigger(ActionEvent::ACTION_DELETE);
	
			$this->_message('删除成功');
			return $this->redirect()->toUrl('/member/identity');
	
		}
		throw new \Exception('没有ID参数');
	}
	private function _getMemberByID($UserID)
	{
		$table = $this->_getTable('MemberTable');
		$rs = $table->getOneForId($UserID);
		
		return $rs->toArray();
	}
	private function _insertImg($name)
	{
		$config = $this->_getConfig('upload');
		$config = $config['member'];
		$files = $this->params()->fromFiles('ImgUrl');
		if(! empty($files['name'])){
			return $config['showPath'] . Uploader::upload($files , $name , $config['uploadPath'] , $config);
		}else{
			return $this->params()->fromPost('ImgUrl');
		}
	
		return null;
	}
	private function _updateMemberImage($UserId , $imageFile)
	{
		$memberTable = $this->_getTable('memberTable');
		return $memberTable->updateImage($UserId , $imageFile);
	}
	private function _dealFilter($params)
	{
		//filter
		$s_fields = $this->params()->fromQuery('s_fields', '');
		$s_opts = $this->params()->fromQuery('s_opts', '');
		$s_vals = $this->params()->fromQuery('s_vals', '');
		$s_rels = $this->params()->fromQuery('s_rels', '');
		if ($s_fields) {
			$params['filter'] = array(
					'fields' => explode('|', $s_fields),
					'opts' => explode('|', $s_opts),
					'vals' => explode('|', $s_vals),
					'rel' => explode('|', $s_rels)
			);
		}
		return $params;
	}
}