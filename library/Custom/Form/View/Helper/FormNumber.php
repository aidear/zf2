<?php
/**
 * FormNumber.php
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
 * @version CVS: Id: FormNumber.php,v 1.0 2013-10-6 下午10:09:33 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Form\View\Helper;

use Zend\Form\ElementInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormNumber extends FormInput
{
    /**
     * Attributes valid for the input tag type="number"
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'list'           => true,
        'max'            => true,
        'min'            => true,
        'step'           => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'type'           => true,
        'value'          => true
    );

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
    */
    protected function getType(ElementInterface $element)
    {
        return 'number';
    }
}
