<?php
/**
 * UserForm.php
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
 * @version CVS: Id: UserForm.php,v 1.0 2013-9-16 下午10:12:03 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;

class MemberForm extends Form
{
    function __construct() {
        parent::__construct ( 'member-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'enctype', 'multipart/form-data' );
        
        $this->add ( array (
        		'name' => 'UserID',
        		'options' => array (
        				'label' => 'ID'
        		)
        ) );
        
        $this->add ( array (
            'name' => 'UserName',
            'options' => array (
                'label' => '用户名' 
            ),
        	'attributes' => array(
        		'must' => '*'
        	)
        ) );
        
        $this->add ( array (
            'name' => 'Password',
            'options' => array (
                'label' => '密码' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'Nick',
            'options' => array (
                'label' => '昵称' 
            ) 
        ) );
        $this->add ( array (
        		'name' => 'ImgUrl',
        		'options' => array (
        				'label' => '头像'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Email',
        		'type' => 'Zend\Form\Element\Email',
        		'options' => array (
        				'label' => '邮箱'
        		),
        	'attributes' => array(
        		'must' => '*'
        	)
        ) );
        $this->add ( array (
        		'name' => 'Mobile',
        		'attributes' => array(
        				'maxlength' => 11,
        				'pattern'  => '^((\+86)|(86))?(1(3|5|8))\d{9}$',
        				'must' => '*'
        		),
        		'options' => array (
        				'label' => '手机'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Points',
        		'attributes' => array(
        			'pattern' => '^[0-9]+(\.[0-9]{1,2})?$',
        			'notemsg' => '允许保留两位小数',
        		),
        		'options' => array (
        				'label' => '积分'
        		)
        ) );
        $this->add ( array (
        		'name' => 'TrueName',
        		'options' => array (
        				'label' => '真实姓名'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Gender',
        		'type' => '\Zend\Form\Element\Select',
        		'attributes' => array(
        				'required' => 'required',
        				'value' => '0',
        		),
        		'options' => array (
        				'label' => '性别',
        				'value_options' => array('0'=>'保密', '1'=>'男', '2'=>'女'),
        		)
        ) );
        $this->add ( array (
        		'name' => 'Province',
        		'type' => '\Custom\Form\Element\Select',
        		'options' => array (
        				'label' => '省'
        		)
        ) );
        $this->add ( array (
        		'name' => 'City',
        		'type' => '\Custom\Form\Element\Select',
        		'options' => array (
        				'label' => '市'
        		)
        ) );
        $this->add ( array (
        		'name' => 'District',
        		'type' => '\Custom\Form\Element\Select',
        		'options' => array (
        				'label' => '区'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Address',
        		'options' => array (
        				'label' => '街道'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Tel',
        		'attributes' => array(
//         				'pattern'  => '^0[1-68]([-. ]?[0-9]{2}){4}$',
						'pattern' => '^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$',
        				'notemsg' => '格式：021-6543210-8888',
        		),
        		'options' => array (
        				'label' => '电话'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Birthday',
//         		'type' => '\Custom\Form\View\Helper\FormDate',
				'attributes' => array(
						'notemsg' => '格式:1988-12-08',
        		),
        		'options' => array (
        				'label' => '生日'
        		)
        ) );
        $this->add ( array (
        		'name' => 'QQ',
        		'attributes' => array(
        			'size' => 11,
        			'maxlength' => 11,
        			'pattern' => '^[1-9]{1}[0-9]{5,10}$',
        		),
        		'options' => array (
        				'label' => 'QQ'
        		)
        ) );
        $this->add ( array (
        		'name' => 'MSN',
        		'attributes' => array(
        			'pattern' => '^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$',
        		),
        		'options' => array (
        				'label' => 'MSN'
        		)
        ) );
        $this->add ( array (
        		'name' => 'Status',
        		'type' => '\Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '状态',
        				'value_options' => array('0'=>'未激活', '1'=>'正常', '2'=>'锁定'),
        		)
        ) );
        $this->add ( array (
        		'name' => 'Source',
        		'options' => array (
        				'label' => 'Source',
        		)
        ) );
        $this->add ( array (
            'name' => 'submit',
            'attributes' => array (
                'value' => '提交',
                'type' => 'submit',
             )
        ) );
        
        $this->add(array(
            'name' => 'ID',
            'attributes' => array(
                'value' => ''
             )
        ));
    }
    
}