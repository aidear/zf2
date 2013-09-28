<?php
/**
 * EngineController.php
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
 * @version CVS: Id: EngineController.php,v 1.0 2013-9-28 下午10:10:40 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace FrontEnd\Controller;

use Custom\Mvc\ActionEvent;

use Zend\Paginator\Paginator;

use BackEnd\Model\Users\RoleTable;

use BackEnd\Model\Users\Role;

use Custom\Mvc\Controller\AbstractActionController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use BackEnd\Model\Users\UserTable;
use BackEnd\Model\Users\User;
use BackEnd\Form\UserForm;
use Zend\Form\Annotation\AnnotationBuilder;
use Custom\Util\Utilities;
use Custom\Form\Form;

class EngineController extends AbstractActionController
{
	protected $baidu_engine = array(
					'site' => array(
						'url' => 'http://www.baidu.com/s',
						'k' => 'word',
					),
					'news' => array(
						'url' => 'http://news.baidu.com/ns',
						'k' => 'word',
					),
					'video' => array(
							'url' => 'http://video.baidu.com/v',
							'k' => 'word',
					),
					'image' => array(
							'url' => 'http://image.baidu.com/i',
							'k' => 'word',
					),
					'music' => array(
							'url' => 'http://music.baidu.com/search', 
							'k' => 'key',
					),
					'map' => array(
							'url' => 'http://map.baidu.com/m', 
							'k' => 'word',
					),
					'zhidao' => array(
							'url' => 'http://zhidao.baidu.com/search',
							'k' => 'word',
					),
					'shopping' => array(
							'url' => 'http://tuan.baidu.com/search',
							'k' => 'wd',
					),
			);
	protected $google_engine = array(
					'site' => array(
							'url' => 'http://www.google.com.hk/search',
							'k' => 'q',
						),
					'news' => array(
							'url' => 'http://www.google.com.hk/search?tbm=nws',
							'k' => 'q',
					),
					'video' => array(
							'url' => 'http://www.google.com.hk/search?tbm=vid',
							'k' => 'q',
					),
					'image' => array(
							'url' => 'http://www.google.com.hk/search?tbm=isch',
							'k' => 'q',
					),
					'map' => array(
							'url' => 'http://ditu.google.cn/maps?engine=google_map',
							'k' => 'q',
					),
				);
    function indexAction(){
    	
        $params = $this->params()->fromPost();
        
        $engine = isset($params['engine']) ? $params['engine'] : 'baidu_web';
        $word = isset($params['word']) ? $params['word'] : '';
        $tab = isset($params['tab']) ? $params['tab'] : 'site';
        $url = '';
        switch ($engine) {
        	case 'baidu_web':
        		$script = isset($this->baidu_engine[$tab]) ? $this->baidu_engine[$tab] : '';
        		$url = $script['url'].'?'.$script['k'].'='.$word;
        		break;
        	case 'google':
        		$script = isset($this->google_engine[$tab]) ? $this->google_engine[$tab] : '';
        		if (!$script) {
        			die('参数错误!');
        		}
        		$url = $script['url'].'&'.$script['k'].'='.$word;
        		break;
        	default:
        		break;
        }//print_r($url);die;
        $this->redirect()->toUrl($url);
    }
}
