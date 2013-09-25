<?php
/**
 * ArticleCategory.php
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
 * @version CVS: Id: ArticleCategory.php,v 1.0 2013-9-25 下午8:36:25 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Categories\Category;
use Custom\Categories\Category\AbstractCategory;

class ArticleCategory extends AbstractCategory
{
    function __construct(array $cate){
        $this->setProperties($cate);
    }
    function getId(){
        if(!isset($this->properties['CategoryID'])){
            throw new \Exception('incomplete Category');
        }
        
        return $this->properties['CategoryID'];
    }
    
    function toArray(){
        return $this->getProperties();
    }
}