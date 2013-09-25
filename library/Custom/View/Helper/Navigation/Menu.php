<?php
/**
 * Menu.php
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
 * @version CVS: Id: Menu.php,v 1.0 2013-9-20 下午8:15:00 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\View\Helper\Navigation;

use Zend\View\Exception;
use \Zend\View\Helper\Navigation\Menu as Father;

class Menu extends Father
{
    public function renderPartial($container = null, $partial = null) {
        $this->parseContainer ( $container );
        if (null === $container) {
            $container = $this->getContainer ();
        }
        
        if (null === $partial) {
            $partial = $this->getPartial ();
        }
        
        if (empty ( $partial )) {
            throw new Exception\RuntimeException ( 'Unable to render menu: No partial view script provided' );
        }
        
        $found = $this->findActive($container);
        if ($found) {
            $foundPage  = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }
        
        $pages = array ();
        
        $iterator = new \RecursiveIteratorIterator ( $container, \RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < 0 || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } 
            $page->depth = $depth;
            $page->isActive = $isActive;
            $pages[] = $page;
            
        }
        
        $model = array (
            'container' => $pages 
        );
        
        if (is_array ( $partial )) {
            if (count ( $partial ) != 2) {
                throw new Exception\InvalidArgumentException ( 'Unable to render menu: A view partial supplied as ' . 'an array must contain two values: partial view ' . 'script and module where script can be found' );
            }
            
            $partialHelper = $this->view->plugin ( 'partial' );
            return $partialHelper ( $partial [0], /*$partial[1], */$model );
        }
        
        $partialHelper = $this->view->plugin ( 'partial' );
        return $partialHelper ( $partial, $model );
    }
}