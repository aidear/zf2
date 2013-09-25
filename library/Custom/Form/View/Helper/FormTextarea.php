<?php
/**
* FormTextarea.php
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
* @version CVS: $Id: FormTextarea.php,v 1.1 2013/04/15 10:56:30 rock Exp $
* @link http://www.dahongbao.com/
* @deprecated File deprecated in Release 3.0.0
*/
namespace Custom\Form\View\Helper;

use \Zend\Form\View\Helper\FormTextarea as Father;
use Zend\Form\ElementInterface;

class FormTextarea extends Father
{
    public function render(ElementInterface $element)
    {
        $name   = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }
    
        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $content            = (string) $element->getValue();
        $label              = $element->getLabel();
        $escapeHtml         = $this->getEscapeHtmlHelper();
        $errmsg = $element->getMessages();
    
        $temp = sprintf(
            '<label class="input-label" for="%s">%s</label><div class="input-item"><textarea %s>%s</textarea>',
            $name,
            $label,
            $this->createAttributesString($attributes),
            $escapeHtml($content)
        );
        if($errmsg){
            $temp = '<div class="input-group error">' . $temp;
            foreach($errmsg as $msg){
                $temp .= "<span class='help-inline'>{$msg}</span>";
            }
        }else{
            $temp = '<div class="input-group">' . $temp;
        }
        $temp .= '</div></div>';
        
        return $temp;
    }
}