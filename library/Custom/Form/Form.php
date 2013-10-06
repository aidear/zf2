<?php
/**
 * Form.php
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
 * @version CVS: Id: Form.php,v 1.0 2013-10-6 下午10:10:20 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Form;

use Zend\Form\Form as Father;

class Form extends Father
{
    function __construct($name = null){
        parent::__construct($name);
        
        $this->add(array(
            'name' => 'submit'
        ));
    }
}