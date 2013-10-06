<?php
/**
 * FormInput.php
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
 * @version CVS: Id: FormInput.php,v 1.0 2013-10-6 下午10:08:56 Willing Exp
 * @link http://localhost
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