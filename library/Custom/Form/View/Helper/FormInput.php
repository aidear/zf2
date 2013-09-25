<?php

/**
* FormInput.php
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
* @author Yaron Jiang<yjiang@corp.valueclick.com>
* @copyright (C) 2004-2013 Mezimedia.com
* @license http://www.mezimedia.com PHP License 5.3
* @version CVS: $Id: FormInput.php,v 1.1 2013/04/15 10:56:30 rock Exp $
* @link http://www.dahongbao.com
* @deprecated File deprecated in Release 3.0.0
*/
namespace Custom\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class FormInput extends \Zend\Form\View\Helper\FormInput
{
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $attributes['type']  = $this->getType($element);
        $attributes['value'] = $element->getValue();
        $attributes['label'] = $element->getLabel();
        $errmsg = $element->getMessages();

        $re = sprintf(
            '<label class="input-label" for="%s">%s</label><div class="input-item"><input class="input-element" %s%s',
            $attributes['name'],
            $attributes['label'],
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );
        
        if(isset($attributes['notemsg'])){
            $re .= "<span class='help-inline note'><i class='icon-info-sign'></i>{$attributes['notemsg']}</span>";
        }
        
        if($errmsg){
            $re = '<div class="input-group error">' . $re;
            foreach($errmsg as $msg){
                $re .= "<span class='help-inline'>{$msg}</span>";
            }
        }else{
            $re = '<div class="input-group">' . $re;
        }
        
        $re .= '</div></div>';
        
        return $re;
    }
}