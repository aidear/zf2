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
        						'resource' => 'role_index'
        				),
        				array (
        						'label' => '资源管理',
        						'controller' => 'resource',
        						'resource' => 'resource_index'
        				),
        				array (
        						'label' => '帐号管理',
        						'controller' => 'user',
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
							)
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
							)
					)
			)
	    ), 
	),
	
    array(
        'label' => '文章分类管理',
        'controller' => 'ArticleCategory',
        'resource' => 'articlecategory_index',
        'pages' => array(
            array(
                'label' => '新增分类',
                'controller' => 'ArticleCategory',
                'action' => 'save',
//                 'visible' => false
            ),
        )
    ),
    /*
    array(
        'label' => '文章管理',
        'controller' => 'article',
        'resource' => 'article_index'
    ),
    array(
        'label' => '联盟管理',
        'controller' => 'affiliate',
        'resource' => 'affiliate_index'
    ),
    array(
        'label' => '商家管理',
        'controller' => 'merchant',
        'resource' => 'merchant_index',
        'pages' => array(
            array(
                'label' => '聪明点商家导入',
                'controller' => 'SmcnMerFeed',
                'action' => 'index',
                'resource' => 'smcnmerfeed_index'
            ),
            array(
                'label' => 'SMUS商家导入',
                'controller' => 'SmusMerFeed',
                'action' => 'index',
                'resource' => 'smusmerfeed_index'
            ),
        ),
    ),
    array(
        'label' => '分类管理',
        'controller' => 'category',
        'resource' => 'category_index'
    ),
    
    array(
        'label' => 'Coupon管理',
        'controller' => 'coupon',
        'resource' => 'coupon_index',
        'pages' => array(
            array(
                'label' => '审核Coupon',
                'controller' => 'coupon',
                'action' => 'userDataFeed',
                'resource' => 'coupon_userDataFeed'
            ),
            array(
                'label' => '线上Coupon管理',
                'controller' => 'coupon',
                'action' => 'couponList',
                'resource' => 'coupon_couponList'
            )
        )
    ),
    array(
            'label' => '联盟feed下载导入管理',
            'controller' => 'FeedConfig',
            'resource' => 'feedconfig_index'
    ),
    array(
            'label' => '商家feed导入管理',
            'controller' => 'FeedConfig',
            'action'  => 'merchantFeedConfig',
            'resource' => 'feedconfig_merchantFeedConfig'
    ),
    
    array(
        'label' => '推荐管理',
        'controller' => 'Recommend',
        'resource' => 'recommend_index',
        'pages' => array(
            array(
                'label' => '推荐位置管理',
                'controller' => 'RecommendType',
                'resource' => 'recommendtype_index',
            ),
            array(
                'label' => 'Coupon推荐',
                'controller' => 'Recommend',
                'action' => 'couponList',
                'resource' => 'recommend_couponList'
            ),
            array(
                'label' => '商家推荐',
                'controller' => 'Recommend',
                'action' => 'merchantList',
                'resource' => 'recommend_merchantList'
            ),
            array(
                'label' => '文章推荐',
                'controller' => 'Recommend',
                'action' => 'articleList',
                'resource' => 'recommend_articleList'
            ),
            array(
                'label' => '缓存管理',
                'controller' => 'Recommend',
                'action' => 'index',
                'resource' => 'recommend_index'
            )
        )
    ),
    array(
        'label'      => 'Seo Meta 信息管理',
        'controller' => 'SeoPageType',
        'resource'   => 'seopagetype_index',
        'pages' => array(
            array(
                'label'      => '页面类型',
                'controller' => 'SeoPageType',
                'resource'   => 'seopagetype_index',
            ),
        )
    ),
    
    array(
        'label'      => '标签管理',
        'controller' => 'Tag',
        'action'  => 'index',
        'resource'   => 'tag_index',
        'pages' => array(
            array(
                'label'      => '类目标签管理',
                'controller' => 'CategoryTag',
                'resource'   => 'category_index',
            ),
        )
    )*/
);