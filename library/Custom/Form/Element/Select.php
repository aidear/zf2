<?php
/**
 * Select.php
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
 * @version CVS: Id: Select.php,v 1.0 2013-10-6 下午10:07:55 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Form\Element;
use \Zend\Form\Element\Select as Father;

class Select extends Father
{
    public function getInputSpecification()
    {
        $spec = array(
            'name' => $this->getName(),
            'required' => false,
            'validators' => array(
            )
        );
    
        return $spec;
    }
    
    public function setSelectOptions(array $data , $hasEmpty = false){
        $re = array();
        $selected = $this->getValue();
        
        if($hasEmpty){
            $data['0'] = '所有';
        }
        foreach($data as $k => $v){
            $row = array(
                'value' => $k,
                'label' => $v,
            );
            
            if($selected == $k){
                $row['selected'] = 'selected';
            }
            
            $re[] = $row;
        }
        
        $this->setValueOptions($re);
    }
}