<?php
/*
 * package_name : CommonAds.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : richie(rizhang@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: CommonAds.php,v 1.2 2013/04/22 10:39:44 rizhang Exp $
 */
namespace Custom\Sponsor\Ads;

use Custom\Util\TrackingFE;
use Custom\Util\Utilities;

class CommonAds {
	
	protected $keyword = NULL;
	protected $keyword2 = NULL;
	protected $isRepeatArr = array();	//各个位置，是否需要重复显示 
	protected $splitCountArr = array();	//tpl模板显示位置及每个位置的广告数目
	protected $adsNeedJS = true;	//当无广告是，是否要重新用JS请求
	protected $alreadyInJS = false;	//当前状态， true表示当前是用JS调用的
	protected $adsPositionCount = 0;
	protected $chid = NULL;
	protected $timeout = 2;	//超时时间为2秒
	protected $oldParamArr = array();	//原始参数
	protected $adsCount = 0;
	protected $IsHighlight = '';
	protected $IsDisplayKeywordArr = array();	//各个位置是否需要在头部显示搜索关键词
	protected $requestAdsCount = 0;	//请求广告的数量
    protected $sleep = 0;   //休眠时间
    protected $noStyle = FALSE;
    public $adsOvertime = false;	//是否超时
	private static $pageTypeID = -1;
	protected static $googleAdtest = NULL;
    //是否是只异步请求广告， PHP端只获取请求广告的URL，在页面上用异步等方式请求
    protected $onlySyncRequestAds = false;
    protected $notRuturnResult = false;   //不需要返回广告结果
	
	public function getPageTypeID() {
		if(self::$pageTypeID < 0) {
			return 15000007; //default: Other pages.
		} else {
			return self::$pageTypeID;
		}
	}
		 
	public function setPageTypeID($pageTypeID) { self::$pageTypeID = $pageTypeID; }
	
		
	public function setGoogleAdtest($adtest) {
		self::$googleAdtest = $adtest == 'on' ? 'on' : 'off';
	}
	
	/**
	 * 参数设置
	 * @param array $paramArr 二维数组 array('splitCountArr' => array(3, 3, 6), 'keyword' => $keyword, 
	 * 										'keyword2' => $keyword2, isRepeatArr = array(false, false),
	 * 										'adsNeedJS' => true, chid => $chid
	 * 										)
	 * @return boolean 
	 */
	public function initParams($paramArr) {
		//0. 参数检查
		if(empty($paramArr['keyword'])
			|| is_array($paramArr['splitCountArr']) == false
			|| count($paramArr['splitCountArr']) == 0) {
			return false;
		}
		//1. 两个关键字
		$this->keyword = $paramArr['keyword'];
		if(!empty($paramArr['keyword2'])) {
			$this->keyword2 = $paramArr['keyword2'];
		} else {
			$this->keyword2 = "";
		}
		//2. 分隔广告及每个位置的广告数
		$this->splitCountArr = $paramArr['splitCountArr'];
		$this->adsPositionCount = count($this->splitCountArr);
		//3. 广告可重复的位置。快速参数，传递bool or string类型，自动拷贝到所有位置
		if(!empty($paramArr['isRepeatArr'])) {
			if(is_array($paramArr['isRepeatArr'])) {
				if(count($paramArr['isRepeatArr']) != $this->adsPositionCount) {
					throw new \Exception("array size must equal between 'isRepeatArr' to 'splitCountArr'.");
				}
				$this->isRepeatArr = $paramArr['isRepeatArr'];
			}
			else {
				if(is_bool($paramArr['isRepeatArr'])) {
					$isRepeat = $paramArr['isRepeatArr'];
				} else {
					$isRepeat = strtolower($paramArr['isRepeatArr']) == 'true' ? true : false;
				}
				for($i=0; $i<$this->adsPositionCount; $i++) {
					$this->isRepeatArr[] = $isRepeat;
				}
			}
		} else {
			$this->isRepeatArr[] = array();
		}
		//4. 无广告时，是否需要JS再次请求
		if (isset($paramArr['adsNeedJS'])) {
			$this->adsNeedJS = $paramArr['adsNeedJS'];
		} else {
			$this->adsNeedJS = true;
		}
		//5. 是否已在JS请求的进程中
		if (isset($paramArr['alreadyInJS'])) {
			$this->alreadyInJS = $paramArr['alreadyInJS'];
		} else {
			$this->alreadyInJS = false;
		}
		//6. 是否需要转换 <b> 到 <strong>
		if (isset($paramArr['IsHighlight'])) {
			$this->IsHighlight = $paramArr['IsHighlight'];
		} else {
			$this->IsHighlight = false;
		}
		//7. 超时参数设置
		if(isset($paramArr['timeout']) && $paramArr['timeout'] > 0) {
			$this->timeout = $paramArr['timeout'];
		} else {
			$this->timeout = 2; //默认2秒
		}
		//8. 是否需要在头部显示 搜索关键词
		if (!empty($paramArr['IsDisplayKeywordArr']) && is_array($paramArr['IsDisplayKeywordArr'])) {
			$this->IsDisplayKeywordArr = $paramArr['IsDisplayKeywordArr'];
		} else {
			$this->IsDisplayKeywordArr = array();
		}
        //9. 是否是只异步请求广告
        if (isset($paramArr['onlySyncRequestAds'])) {
            $this->onlySyncRequestAds = $paramArr['onlySyncRequestAds'];
        } else {
            $this->onlySyncRequestAds = false;
        }
		//10. 其它
		$this->chid = $paramArr['chid'];
        
        //11. 不需要返回结果
        if (isset($paramArr['notRuturnResult'])) {
            $this->notRuturnResult = $paramArr['notRuturnResult'];
        }
                
        //11.休眠时间
        if (isset($paramArr['isSleep']) && $paramArr['isSleep']) {
            $hour = getDateTime('H');
            if ($hour >= 0 && $hour < 11) {
                $this->sleep = 1; //北京时间0:00-11:00 时段内，延时1秒
            } else {
                $this->sleep = 3; //北京时间11:00-24:00时段内，延时3秒
            }
        }
		
		foreach ($this->splitCountArr as $splitCount) {
			$this->requestAdsCount += $splitCount;
		}
		$this->oldParamArr = $paramArr;	//保存原始的数据
		return true;
	}
	
	/**
	 * 得到SPL内容
	 */
	public function getAdsScript($params) {
		$adsContent = "";
		$startTime = time();
		//1. 流量质量判断
		$sourceFlag = trim(strtoupper(TrackingFE::getSourceGroup()));
		if ($sourceFlag == "BAIDU" && TrackingFE::getTrafficType() >= 0) {
			if(Utilities::getRobotLimit() && Utilities::getRobotType()==1) {
				return ''; //机器人,不显示广告
			}
		}
		if (TrackingFE::getTrafficType() < 0
			&& TrackingFE::getTrafficType() != -4
			&& TrackingFE::getTrafficType() != -11
			&& TrackingFE::getTrafficType() != -12) {
				return false; //未进入白名单的无效流量不请求广告
		}
		/*
		if (BlockAdSenceDao::isBlockAdSence() == true) {
			return false;	//此URL禁止调用广告
		}
		*/
		//2. 初始化参数
		if($this->initParams($params) == false) {
			return "";
		}
		//3. 调用ads API取得广告, 只异步请求(onlySyncRequestAds)只有第一次php端会设置此值，JS请求的时候会移除
        if ($this->onlySyncRequestAds === true) {
            $adsArr = '';
        } else {
            $adsArr = $this->getAdsContent();
        }
		$this->adsCount = count($adsArr);
		//4. 高亮显示
		if ($this->IsHighlight) {
			$adsArr = $this->highlightAds($adsArr);
		}
		//5. 按$this->splitCountArr分隔广告
		$adsGroupArr = $this->splitAds($adsArr);
		//6. 生成JS代码
		$adsContent = $this->renderTemplate($adsGroupArr);
		$this->adsOvertime = false;
        //7. 不需要返回结果
        if ($this->notRuturnResult === true) {
            return true;
        }
		if ($adsContent === false) { //无广告
			if (time() - $startTime >= $this->timeout) { //超时
				$this->adsOvertime = true;
			}
            $adsURL = $this->getRequestUrl();
			if ($this->adsNeedJS) { //需要在用JS请求一遍
//				if (GoogleDNSDao::getDnslookup() == "IP") {
//					$this->setGoogleParam("dnslookup", "DOMAIN");
//				}
//				else if($adsOvertime) {
//					$this->setGoogleParam("dnslookup", "IP");
//				}
                $str = "<script type=\"text/javascript\" src=\"{$adsURL}\"></script>\r\n";
			} else if ($this->onlySyncRequestAds === true) {
			    $str = "<script type=\"text/javascript\">//<![CDATA[\r\n";
                $str .= "glb_SyncRequestAds('{$adsURL}');\r\n//]]>\r\n</script>\r\n";
			}
		}
		else {	//输出广告内容
			if ($this->alreadyInJS) {
				$str = "{$adsContent}\r\n";
			}
			else {
				$str = "<script type=\"text/javascript\">//<![CDATA[\r\n{$adsContent}\r\n//]]>\r\n</script>\r\n";
			}
		}
		return $str;
	}
	
	/**
	 * 解析 JS 请求的Url
	 * @param string $urlParam
	 * @return array
	 */
	public static function urlParamToArray($urlParam) {
		if (trim($urlParam)) {
			$params = unserialize(Utilities::decode($urlParam));
			return $params;
		}
		return NULL;
	}
	
	/**
	 * 分隔广告数组 根据参数splitCountArr和isRepeatArr
	 * @param array $adsArr
	 * @return array 
	 */
	protected function splitAds(&$adsArr) {
		if (!is_array($adsArr)) {
			return false;
		}
		$totalCount = count($adsArr);
		$startPostion = 0;
		$newAdsArr = array();
		for ($loop = 0; $loop < count($this->splitCountArr); $loop++) {
			if ($loop != 0) {
				if ($this->isRepeatArr[$loop] === true) {	//重复显示
					if ($totalCount < $startPostion) { //数量不够需要添补
						$newAdsArr[$loop] = array_slice($adsArr, 0, $this->splitCountArr[$loop]);
					}
					else {//数量充足, 正常显示
						$newAdsArr[$loop] = array_slice($adsArr, $startPostion, $this->splitCountArr[$loop]);
					}
				}
				else if ($totalCount > $startPostion) { //不需要重复显示
					$newAdsArr[$loop] = array_slice($adsArr, $startPostion, $this->splitCountArr[$loop]);
				}
			}
			else {
				$newAdsArr[$loop] = array_slice($adsArr, $startPostion, $this->splitCountArr[$loop]);
			}
			$startPostion += $this->splitCountArr[$loop];
		}
		return $newAdsArr;
	}
	

	/**
	 * 返回广告数量
	 * @return int
	 */
	public function getAdsCnt() {
		return $this->adsCount;
	}
	
	/**
	 * 高亮显示
	 * @param array $adsData 广告数组
	 * @return array
	 */
	protected function highlightAds(&$adsData) {
		if($adsData == null) {
			return $adsData;
		}
		$searchWords = array('<b>', '</b>');
		$replaceWords = array('<strong>', '</strong>');
		//end modify.
		for($i=0; $i<count($adsData); $i++) {
			if(!empty($adsData[$i]['LINE1'])) {
				$adsData[$i]['LINE1'] = str_ireplace(
					$searchWords, $replaceWords, $adsData[$i]['LINE1']);
			}
			if(!empty($adsData[$i]['LINE2'])) {
				$adsData[$i]['LINE2'] = str_ireplace(
					$searchWords, $replaceWords, $adsData[$i]['LINE2']);
			}
			if(!empty($adsData[$i]['LINE3'])) {
				$adsData[$i]['LINE3'] = str_ireplace(
					$searchWords, $replaceWords, $adsData[$i]['LINE3']);
			}
		}
		return $adsData;
	}
}

