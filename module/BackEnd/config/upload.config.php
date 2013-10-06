<?php
/**
 * upload.config.php
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
 * @version CVS: Id: upload.config.php,v 1.0 2013-9-20 下午7:23:51 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

use Zend\Validator\File\Size;
return array(
    //sys_config
    'sys_config' => array(
        'uploadPath' => APPLICATION_PATH . '/public/images/sys/',
        'showPath' => '/images/sys/',
        'validators' => array(
             new Size(array(
                 'max' => '256000' //单位Bit
             ))
         ),
     ),
    //member图片
     'member' => array(
         'uploadPath' => APPLICATION_PATH . '/public/images/member/',
         'showPath' => '/images/member/',
         'validators' => array(
             new Size(array(
                 'max' => '256000'
             ))
         ),
     ),
     //nav图片
     'nav' => array(
         'uploadPath' => APPLICATION_PATH . '/public/images/nav/',
         'showPath' => '/images/nav/',
         'validators' => array(
             new Size(array(
                 'max' => '256000'
             ))
         ),
     ),
    
    //推荐图片
    'recommend' => array(
         'uploadPath' => APPLICATION_PATH . '/public/img/other/recommend/',
         'showPath' => '/img/other/recommend/',
         'validators' => array(
             new Size(array(
                 'max' => '256000'
             ))
         ),
     ),
    
);