<?php
/**
 * FormSubmit.php
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
 * @version CVS: Id: FormSubmit.php,v 1.0 2013-10-6 下午10:09:56 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormSubmit extends \Zend\Form\View\Helper\FormInput
{
    /**
     * Attributes valid for the input tag type="submit"
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'type'           => true,
        'value'          => true,
    );

    /**
     * Translatable attributes
     *
     * @var array
     */
//     protected $translatableAttributes = array(
//         'value' => true
//     );
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
    	
    	return '<div class="button-group">'.sprintf(
    			'<input class="button-element" %s%s',
    			$this->createAttributesString($attributes),
    			$this->getInlineClosingBracket()
    	).'&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value="重填" name="reset" class="button-element"></div>';
    }

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'submit';
    }
}