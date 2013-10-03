<?php
/**
 * NavCategoryForm.php
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
 * @version CVS: Id: NavCategoryForm.php,v 1.0 2013-10-3 下午9:55:47 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;

class NavCategoryForm extends Form
{
    function __construct() {
        parent::__construct ( 'nav-category-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'enctype', 'multipart/form-data' );
        
        $this->add ( array (
        		'name' => 'id',
        		'options' => array (
        				'label' => 'ID'
        		)
        ) );
        
        $this->add ( array (
            'name' => 'name',
            'options' => array (
                'label' => '分类名称' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'desc',
            'options' => array (
                'label' => '分类描述' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'keyword',
            'options' => array (
                'label' => '分类关键字' 
            ) 
        ) );
        $this->add ( array (
        		'name' => 'parentID',
        		'type' => '\Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '上级分类'
        		)
        ) );
        $this->add ( array (
        		'name' => 'imgUrl',
        		'attributes' => array(
        			'notemsg' => '为了前台展示美观，宽度不超过120像素',
        		),
        		'options' => array (
        				'label' => '分类图片'
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