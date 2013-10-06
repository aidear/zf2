<?php
/**
 * FormTextarea.php
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
 * @version CVS: Id: FormTextarea.php,v 1.0 2013-10-6 下午10:10:11 Willing Exp
 * @link http://localhost
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