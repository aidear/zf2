<?php
/**
 * ConfigForm.php
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
 * @version CVS: Id: ConfigForm.php,v 1.0 2013-9-21 下午9:58:58 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;

class ConfigForm extends Form
{
    function __construct() {
        parent::__construct ( 'config-form' );
        $this->setAttribute ( 'method', 'post' );
        $this->setAttribute ( 'class', 'box-form' );
//         $this->setAttribute ( 'action', 'box-form' );
        
        $this->add ( array (
            'name' => 'ID',
            'options' => array (
                'label' => 'ID' 
            ) 
        ) );
        $this->add ( array (
        		'name' => 'PID',
        		'type' => 'Zend\Form\Element\Select',
        		'options' => array (
        				'label' => '上级分类'
        		)
        ) );
        
        $this->add ( array (
            'name' => 'cKey',
            'options' => array (
                'label' => '配置代码' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'cName',
            'options' => array (
                'label' => '名称' 
            ) 
        ) );
        $this->add ( array (
        		'name' => 'cRange',
        		'options' => array (
        				'label' => '参数范围'
        		)
        ) );
        $this->add ( array (
        		'name' => 'cRangeName',
        		'options' => array (
        				'label' => '参数范围对应的名字'
        		),
        		'attributes' => array(
        				'title' => ''
        		),
        ) );
        $this->add(array(
        		'name' => 'summary',
        		'options' => array (
        				'label' => '说明'
        		),
        		'attributes' => array(
        				'value' => ''
        		)
        ));
        $this->add ( array (
            'type' => 'Zend\Form\Element\Select',
            'name' => 'cType',
            'options' => array (
                'label' => '类型',
            	'value_options' => array('text' => 'text', 'select' => 'select', 'textarea' => 'textarea', 'file' => 'file'),
            ) 
        ) );
        $this->add ( array (
        		'type' => 'Zend\Form\Element\Select',
        		'name' => 'cShow',
        		'options' => array (
        				'label' => '是否可配置',
        				'value_options' => array('1' => '是', '0' => '否'),
        		)
        ) );
        $this->add ( array (
        		'name' => 'sortOrder',
        		'options' => array (
        				'label' => '排序',
        		),
        		'attributes' => array(
        				'notemsg' => '顺序排列，值越大越靠后'
        		),
        ) );
        
        $this->add ( array (
            'name' => 'submit',
            'attributes' => array (
                'value' => '提交',
                'type' => 'submit',
             )
        ) );
        
    }
    
    function setRole($roles, $selected = null) {
        $roles = $roles->toArray ();
        if (empty ( $roles )) {
            return null;
        }
        
        $re = array ();
        foreach ( $roles as $role ) {
            $row = array (
                'value' => $role ['Name'],
                'label' => $role ['Name'] 
            );
            if ($selected == $role ['Name']) {
                $row ['selected'] = 'selected';
            }
            
            $re [] = $row;
        }
        
        $this->get ( 'Role' )->setValueOptions ( $re );
    }
}