<?php
/**
 * page.config.php
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
 * @version CVS: Id: page.config.php,v 1.0 2013-9-20 下午9:05:49 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

return array (
	array(
		'label' => '管理中心',
		'controller' => 'acl',
		'resource' => 'acl_index',
		'pages' => array(
	        array(
        		'label' => '帐号权限',
        		'controller' => 'acl',
	        	'order' => 15,
        		'pages' => array(
        				array (
        						'label' => '角色管理',
        						'controller' => 'role',
        				        'action' => 'index',
        						'resource' => 'role_index'
        				),
        				array (
        						'label' => '资源管理',
        						'controller' => 'resource',
        				        'action' => 'index',
        						'resource' => 'resource_index'
        				),
        				array (
        						'label' => '帐号管理',
        						'controller' => 'user',
        						'action' => 'index',
        						'resource' => 'user_index'
        				),
        		)
			),
			array(
					'label' => '网站管理',
					'controller' => 'site',
					'action' => 'index',
					'resource' => 'site_index',
					'order' => 10,
					'pages' => array(
							array(
									'label' => '网站统计',
									'controller' => 'site',
									'action' => 'index',
									'resource' => 'site_index',
							),
							array(
							    'label' => '广告申请',
							    'controller' => 'site',
							    'action' => 'advApply',
							    'resource' => 'site_advApply',
							),
							array(
							    'label' => '意见建议',
							    'controller' => 'site',
							    'action' => 'feedback',
							    'resource' => 'site_feedback',
							),
					)
			),
			array(
					'label' => '会员管理',
					'controller' => 'user',
					'resource' => 'member_index',
					'order' => 10,
					'pages' => array(
							array(
									'label' => '会员列表',
									'controller' => 'member',
									'action' => 'index',
									'resource' => 'member_index',
							),
							array(
									'label' => '通讯录',
									'controller' => 'member',
									'action' => 'contact',
									'resource' => 'member_contact',
							),
							array(
									'label' => '全息表',
									'controller' => 'member',
									'action' => 'all',
									'resource' => 'member_all',
							),
							array(
									'label' => '身份认证',
									'controller' => 'member',
									'action' => 'identity',
									'resource' => 'member_identity',
							),
							array(
									'label' => '新增会员',
									'controller' => 'member',
									'action' => 'save',
									'resource' => 'member_save',
									'visible' => false,
							),
							array(
									'label' => '编辑会员资料',
									'controller' => 'member',
									'params' => array('id' => '[0-9]+'),
									'visible' => false,
							)
					)
			),
			array(
					'label' => '系统设置',
					'controller' => 'config',
					'order' => 30,
					'pages' => array(
							array(
									'label' => '基本设置',
									'controller' => 'config',
									'action' => 'index',
									'resource' => 'config_index',
							),
							array(
									'label' => '促销活动',
									'controller' => 'promotion',
									'action' => 'index',
									'resource' => 'promotion_index',
							),
							array(
									'label' => '邮件服务器',
									'controller' => 'config',
									'action' => 'mail',
									'resource' => 'config_mail',
							),
							array(
									'label' => '新增配置',
									'controller' => 'config',
									'action' => 'add',
									'resource' => 'config_add',
									'visible' => false,
							),
							array(
									'label' => '编辑会员资料',
									'controller' => 'member',
									'params' => array('id' => '[0-9]+'),
									'visible' => false,
							)
					)
			),
			array(
					'label' => '导航管理',
					'controller' => 'nav',
					'order' => 24,
					'pages' => array(
							array(
									'label' => '顶级分类',
									'controller' => 'nav',
									'action' => 'category',
									'resource' => 'nav_category',
							),
							array(
									'label' => '二级分类',
									'controller' => 'nav',
									'action' => 'subCategory',
									'resource' => 'nav_subCategory',
							),
							array(
									'label' => '网址库',
									'controller' => 'nav',
									'action' => 'items',
									'resource' => 'nav_items',
							),
							array(
									'label' => '常用网址',
									'controller' => 'nav',
									'action' => 'addRecommend',
									'resource' => 'nav_addRecommend',
							),
							array(
									'label' => '收录申请',
									'controller' => 'nav',
									'action' => 'applyUrl',
									'resource' => 'nav_applyUrl',
							)
					)
			)
	    ), 
	),
);