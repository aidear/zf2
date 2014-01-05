<?php
namespace FrontEnd\Controller;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Custom\Util\Utilities;
use FrontEnd\Model\Nav\RecommendLink;
use FrontEnd\Form\AdvApplyForm;
use FrontEnd\Model\Site\AdvApply;
use FrontEnd\Form\FeedbackForm;
use FrontEnd\Model\Site\Feedback;
class SiteController extends AbstractActionController
{
    function indexAction()
    {
    }
    function aboutUsAction()
    {
        $this->_isSite();
        return array('link' => 'aboutUs');
    }
    function applyAction()
    {
        $this->_isSite();
        $req = $this->getRequest();
        $assign = array('save' => 0);
        if ($req->isPost()) {
        	$params = $req->getPost();
        	$recommendTable = $this->_getTable('RecommendLinkTable');
        	$recommendLink = new RecommendLink();
        	$recommendLink->exchangeArray($params->toArray());
        	$rs = $recommendTable->save($recommendLink);
        	if ($rs) {
        	   $assign['save'] = 1;
        	}
        }
        return $assign;
    }
    function advApplyAction()
    {
        $this->_isSite();
        $form = new AdvApplyForm();
        $req = $this->getRequest();
        $assign = array('save' => 0);
        if ($req->isPost()) {
            $params = $req->getPost();
            $table = $this->_getTable('AdvApplyTable');
            $advApply = new AdvApply();
            $advApply->exchangeArray($params->toArray());
            $rs = $table->save($advApply);
            if ($rs) {
                $assign['save'] = 1;
            }
        }
        $assign['form'] = $form;
        return $assign;
    }
    function feedbackAction()
    {
        $this->_isSite();
        $form = new FeedbackForm();
        $req = $this->getRequest();
        $assign = array('save' => 0);
        if ($req->isPost()) {
            $params = $req->getPost();
            $table = $this->_getTable('FeedbackTable');
            $feedback = new Feedback();
            $feedback->exchangeArray($params->toArray());
            $rs = $table->save($feedback);
            if ($rs) {
                $assign['save'] = 1;
            }
        }
        $assign['form'] = $form;
        return $assign;
    }
    private function _isSite()
    {
        $this->layout('layout/site-layout');
    }
}