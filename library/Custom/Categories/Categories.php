<?php
/**
 * Categories.php
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
 * @version CVS: Id: Categories.php,v 1.0 2013-9-25 下午8:36:03 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Categories;

class Categories extends AbstractContainer
{
    function __construct($cates = null){
        if($cates instanceof \Traversable || is_array($cates)){
            $this->addCates($cates);
        }
    }
}