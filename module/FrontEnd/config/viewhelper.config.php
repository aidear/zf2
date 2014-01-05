<?php

use Zend\Session\Container;

use Zend\View\HelperPluginManager;

return array(
    'invokables' => array(
//         'formText' => '\Custom\Form\View\Helper\FormText',
//         'formPassword' => '\Custom\Form\View\Helper\FormPassword',
//         'formSubmit' => '\Custom\Form\View\Helper\FormSubmit',
//         'formButton' => '\Custom\Form\View\Helper\FormButton',
//         'formSelect' => '\Custom\Form\View\Helper\FormSelect',
// //         'formTextarea' => '\Custom\Form\View\Helper\FormTextarea',
//         'formFile'=> '\Custom\Form\View\Helper\FormFile',
//         'formEmail' => '\Custom\Form\View\Helper\FormEmail',
//         'formCheckbox' => '\Custom\Form\View\Helper\FormCheckbox',
//         'formNumber' => '\Custom\Form\View\Helper\FormNumber',
//         'formDate' => '\Custom\Form\View\Helper\FormDate',
//         'formMultiSelect' => '\Custom\Form\View\Helper\FormMultiSelect',
//     	'formMultiCheckbox' => '\Custom\Form\View\Helper\FormMultiCheckbox',
    ),
    
    'factories' => array(
        'mynavigation' => function(Zend\View\HelperPluginManager $pm){
            $sm = $pm->getServiceLocator();
            $myacl = $sm->get('acl');
            $session = new Container('user');
            $navigation = $pm->get('\Custom\View\Helper\Navigation');
            $navigation->setAcl($myacl->acl)
                ->setRole($session->Role);
            
            return $navigation;
        },
        
        'isAllowed' => function(Zend\View\HelperPluginManager $pm){
            $sm = $pm->getServiceLocator();
            $myacl = $sm->get('acl');
            $isAllowed = $pm->get('\Custom\View\Helper\IsAllowed');
            $isAllowed->setAcl($myacl->acl);
            return $isAllowed;
        },
        'flashMessages' => function($sm) {
        	$flashmessenger = $sm->getServiceLocator()
        	->get('ControllerPluginManager')
        	->get('flashmessenger');
        
        	$messages = new \Custom\View\Helper\FlashMessages();
        	$messages->setFlashMessenger($flashmessenger);
        
        	return $messages;
        }
   	  ),
);