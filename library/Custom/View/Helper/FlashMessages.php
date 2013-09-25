<?php
 /**
  * FlashMessages.php
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
  * @version CVS: Id: FlashMessages.php,v 1.0 2013-9-23 下午10:20:28 Willing Exp
  * @link http://localhost
  * @deprecated File deprecated in Release 3.0.0
  */
namespace Custom\View\Helper;
 
use Zend\View\Helper\AbstractHelper;
use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessenger;
 

class FlashMessages extends AbstractHelper
{
    /**
     * @var FlashMessenger
     */
    protected $flashMessenger;
 
    public function setFlashMessenger(FlashMessenger $flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }
 
    public function __invoke($includeCurrentMessages = false)
    {
        $messages = array(
            FlashMessenger::NAMESPACE_ERROR => array(),
            FlashMessenger::NAMESPACE_SUCCESS => array(),
            FlashMessenger::NAMESPACE_INFO => array(),
            FlashMessenger::NAMESPACE_DEFAULT => array()
        );
 
        foreach ($messages as $ns => &$m) {
            $m = $this->flashMessenger->getMessagesFromNamespace($ns);
            if ($includeCurrentMessages) {
                $m = array_merge($m, $this->flashMessenger->getCurrentMessagesFromNamespace($ns));
                $this->flashMessenger->clearCurrentMessagesFromNamespace($ns);
            }
        }
 
        return $messages;
    }
}