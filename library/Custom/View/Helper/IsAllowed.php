<?php
/**
 * IsAllowed.php
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
 * @version CVS: Id: IsAllowed.php,v 1.0 2013-9-20 下午11:53:58 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\View\Helper;

use Zend\View\Helper\AbstractHelper;

class IsAllowed extends AbstractHelper
{
    protected $acl;
    
    function setAcl($acl){
        $this->acl = $acl;
    }
    
    function __invoke($resource){
        return $this->acl->isAllowed($_SESSION['user']['Role'] , $resource);
    }
}