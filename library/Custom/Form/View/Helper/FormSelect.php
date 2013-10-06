<?php
/**
 * FormSelect.php
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
 * @version CVS: Id: FormSelect.php,v 1.0 2013-10-6 下午10:09:49 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Form\View\Helper;

use Zend\Form\ElementInterface;

class FormSelect extends \Zend\Form\View\Helper\FormSelect
{
    public function render(ElementInterface $element){
        $element->setAttribute('class', 'input-element select-element');
        $label = $element->getLabel();
        $re = '<div class="input-item">' . parent::render($element) . '</div>';
        
        if($label){
            $re = '<label class="input-label">' . $label . '</label>'. $re;
        }
        
        $re = '<div class="input-group">' . $re . '</div>';
        
        return $re;
    }
}