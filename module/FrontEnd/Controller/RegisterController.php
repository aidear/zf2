<?php
/**
 * RegisterController.php
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
 * @version CVS: Id: RegisterController.php,v 1.0 2013-9-28 下午11:14:18 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace FrontEnd\Controller;

use Custom\Mvc\ActionEvent;

use BackEnd\Form\AclForm;

use BackEnd\Model\Users\Acl;

use BackEnd\Model\Users\AclTable;

use BackEnd\Model\Users\ResourceTable;

use BackEnd\Model\Users\MyAcl;

use BackEnd\Model\Users\RoleTable;

use Zend\Db\ResultSet\ResultSet;
use Custom\Db\TableGateway\TableGateway;

use BackEnd\Model\Users\Resource;
use BackEnd\Model\Users\Role;
use Custom\Mvc\Controller\AbstractActionController;
use Zend\Cache\StorageFactory;
use Custom\Util\Utilities;


class RegisterController extends AbstractActionController
{
    function indexAction()
    {
    	$this->layout('layout/register');
        return array();
    }
	function captchaAction()
	{
		$captcha = new \Zend\Captcha\Image(array(
				'Expiration' => '300',
				'wordlen' => '4',
				'Height' => '28',
				'Width' => '77',
				'writeInFile'=>false,
				'Font' => APPLICATION_PATH.'/data/AdobeSongStd-Light.otf',
				'FontSize' => '24',
				'DotNoiseLevel' => 10,
				'ImgDir' => '/images/FrontEnd'
		));
		//设置验证码保存路径
// 		$captcha->setImgDir('D:/www/project/code/public/images/FrontEnd');
		//生成验证码
		$imgName = $captcha->generate();
// 		echo '/images/FrontEnd/'.$imgName.'.png';die;
		//获取验证码内容且输出
		//echo $captcha->getWord();
	}
}