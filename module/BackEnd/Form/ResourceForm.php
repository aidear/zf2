<?php
/**
* ResourceForm.php
*-------------------------
*
* 
*
* PHP versions 5
*
* LICENSE: This source file is from Smarter Ver2.0, which is a comprehensive shopping engine 
* that helps consumers to make smarter buying decisions online. We empower consumers to compare 
* the attributes of over one million products in the common channels and common categories
* and to read user product reviews in order to make informed purchase decisions. Consumers can then 
* research the latest promotional and pricing information on products listed at a wide selection of 
* online merchants, and read user reviews on those merchants.
* The copyrights is reserved by http://www.mezimedia.com. 
* Copyright (c) 2006, Mezimedia. All rights reserved.
*
* @author Yaron Jiang <yjiang@corp.valueclick.com.cn>
* @copyright (C) 2004-2013 Mezimedia.com
* @license http://www.mezimedia.com PHP License 5.0
* @version CVS: $Id: ResourceForm.php,v 1.1 2013/04/15 10:57:07 rock Exp $
* @link http://www.dahongbao.com/
* @deprecated File deprecated in Release 3.0.0
*/
namespace BackEnd\Form;

use Zend\Form\Form;

class ResourceForm extends Form
{
    function __construct(){
        parent::__construct('resource-form');
        
        $this->add(array(
            'name' => 'ResourceID',
        ));
        
        $this->add(array(
            'name' => 'Name',
            'options' => array(
                'label' => '资源名',
            ),
            
            'attributes' => array(
                'disabled' => 'disabled',
            ),
        ));
        
        $this->add(array(
            'name' => 'Controller',
            'options' => array(
                'label' => '控制器'
            ),
            
            'attributes' => array(
                'disabled' => 'disabled'
            ),
        ));
        
        $this->add(array(
            'name' => 'ControllerName',
            'options' => array(
                'label' => '控制器别名',
            ),
        ));
        
        $this->add(array(
            'name' => 'Action',
            'options' => array(
                'label' => '动作'
            ),
            
            'attributes' => array(
                'disabled' => 'disabled'
            ),
        ));
        
        $this->add(array(
            'name' => 'ActionName',
            'options' => array(
                'label' => '动作别名',
            )
        ));
        
        $this->add(array(
            'name' => 'submit',
        	'attributes' => array(
                'value' => '保存',
                'id' => 'submitbutton',
            ),
        ));
    }
}