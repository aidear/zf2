<?php
/**
 * Uploader.php
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
 * @version CVS: Id: Uploader.php,v 1.0 2013-9-20 下午7:43:21 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\File;

use Zend\File\Transfer\Adapter\Http;

class Uploader
{
    /**
     * 上传
     * @param array $files POST返回的FILE
     * @param string $newName 新文件名
     * @param string $path 目标路径
     * @param array $options 设置 , validators为验证数组
     * @throws \Exception
     * @return string 新文件名
     */
    static function upload($files , $newName , $path , array $options = array()){
        $filename = $newName . strrchr($files['name'] , '.');
        $adapter = new Http();
        
        if(isset($options['validators'])){
            $adapter->setValidators(
                $options['validators'] , $files['name']
            );
        }
        
        $adapter->addFilter('rename' , array(
            'target' => $filename
        ));
        
        if($adapter->isValid()){
            if(!is_dir($path)){
                if(!mkdir($path , 0777 , true)){
                    throw new \Exception('没有生成文件夹的权限：' . $path);
                }
            }
            $adapter->setDestination($path);
            $adapter->receive($files['name']);
            return $filename;
        }else{
            throw new \Exception(implode(',' , $adapter->getMessages()));
        }
    }
}