<?php

/**
* FormSubmit.php
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
* @version CVS: $Id: FormSubmit.php,v 1.1 2013/04/15 10:56:30 rock Exp $
* @link http://www.dahongbao.com
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
    	).'</div>';
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