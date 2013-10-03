<?php
/**
 * UserForm.php
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
 * @version CVS: Id: UserForm.php,v 1.0 2013-9-16 下午10:12:03 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace BackEnd\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    function __construct() {
        parent::__construct ( 'user-form' );
        $this->setAttribute ( 'method', 'post' );
        
        $this->add ( array (
            'name' => 'UserName',
            'options' => array (
                'label' => '用户名' 
            ) 
        ) );
        
//         $this->add ( array (
//             'name' => 'Mail',
//             'options' => array (
//                 'label' => '电子邮箱' 
//             ) 
//         ) );
        
//         $this->add ( array (
//             'name' => 'Remark',
//             'options' => array (
//                 'label' => '职位' 
//             ) 
//         ) );
        
        $this->add ( array (
            'type' => 'Zend\Form\Element\Select',
            'name' => 'Role',
            'options' => array (
                'label' => '角色' 
            ) 
        ) );
        
        $this->add ( array (
            'name' => 'submit',
            'attributes' => array (
                'value' => '提交',
                'type' => 'submit',
             )
        ) );
        
        $this->add(array(
            'name' => 'ID',
            'attributes' => array(
                'value' => ''
             )
        ));
    }
    
    function setRole($roles, $selected = null) {
        $roles = $roles->toArray ();
        if (empty ( $roles )) {
            return null;
        }
        
        $re = array ();
        foreach ( $roles as $role ) {
            $row = array (
                'value' => $role ['Name'],
                'label' => $role ['Name'] 
            );
            if ($selected == $role ['Name']) {
                $row ['selected'] = 'selected';
            }
            
            $re [] = $row;
        }
        
        $this->get ( 'Role' )->setValueOptions ( $re );
    }
}