<?php
/**
 * LinkForm.php
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
 * @version CVS: Id: LinkForm.php,v 1.0 2013-10-4 下午3:21:48 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;

class LinkForm extends Form
{
    function __construct() {
        parent::__construct ( 'link-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'enctype', 'multipart/form-data' );
        
        $this->add ( array (
        		'name' => 'id',
        		'options' => array (
        				'label' => 'ID'
        		)
        ) );
        
        $this->add ( array (
            'name' => 'title',
        	'attributes' => array(
        		 'required' => 'required',
        	),
            'options' => array (
                'label' => '链接标题' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'url',
            'options' => array (
                'label' => '链接地址',
            ),
        	'attributes' => array(
        		'required' => 'required',
        		'notemsg' => '示例:http://www.baidu.com',
            )
        ) );
        
        $this->add ( array (
            'name' => 'target',
        	'type' => '\Zend\Form\Element\Select',
            'options' => array (
                'label' => '打开方式',
            	'value_options' => array('_blank'=>'新窗口打开', '_self'=>'当前窗口打开'),
            ) 
        ) );
        $this->add ( array (
        		'name' => 'category',
        		'type' => '\Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '分类'
        		)
        ) );
       $this->add ( array (
        		'name' => 'isShow',
        		'type' => '\Zend\Form\Element\Select',
        		'attributes' => array(
        				'required' => 'required',
        				'value' => '0',
        		),
        		'options' => array (
        				'label' => '是否显示',
        				'value_options' => array('1'=>'是', '0'=>'否'),
        		)
        ) );
        $this->add ( array (
        		'name' => 'order',
        		'attributes' => array(
        			'pattern' => '^[0-9]+$',
        			'notemsg' => '请输入非负整数',
        		),
        		'options' => array (
        				'label' => '排序'
        		)
        ) );
        $this->add ( array (
            'name' => 'submit',
            'attributes' => array (
                'value' => '提交',
                'type' => 'submit',
             )
        ) );
    }
    
}