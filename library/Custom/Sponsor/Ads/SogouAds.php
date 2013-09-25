<?php
/*
 * package_name : class.SogouAds.php
 * ------------------
 * 得到sogou spl 内容
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: SogouAds.php,v 1.2 2013/07/19 08:40:23 rizhang Exp $
 */

namespace Custom\Sponsor\Ads;

use Custom\Sponsor\Sogou\SogouTagDao;
use Custom\Util\TrackingFE;
use Custom\Util\Utilities;
use Custom\Util\CURL;
use DomDocument;

class SogouAds extends CommonAds {
    protected $sogouDomain = "http://www.sogou.com/websearch/xml/xml4ad.jsp";    //请求广告XML接口
    protected $pid = "smarter8";    //计费名
    protected $pingback = "";       //前台页面回调搜狗广告URL
    protected $varPreName = '_ads_sogou_';
    protected $channelTag = 'smarter8';
    protected $adsTypeMap = array(
        \Tracking_Constant::SPONSOR_SOUGOU_BIDDING,
        \Tracking_Constant::SPONSOR_SOUGOU_SUGGEST
    );
    protected $DisplaySingleTemplate = array();   //显示广告模板样式
    
    //初始化参数
    public function initParams($params) {
        if (!empty($params['varPreName'])) {
            $this->varPreName = $params['varPreName'];
        }
        if ($params["DisplaySingleTemplate"]) {
            $this->DisplaySingleTemplate = $params["DisplaySingleTemplate"];
        }
        if (parent::initParams($params) == false) {
            return false;
        }
        $this->channelTag = $this->pid = SogouTagDao::findtag($this->keyword, TrackingFE::getSource(), TrackingFE::getReferer());
        return true;
    }
    
    /**
     * 得到搜狗广告内容
     * @return array $adsArr
     */
    public function  getAdsContent() {
        $startTime = Utilities::getMicrotime();
        //1.得到请求SOGOU广告的URL
        $reqUrl = $this->makeRequestUrl();
        //2.得到搜狗广告内容
        $adsArr = $this->parseAds($reqUrl);
        //3.格式化广告显示的内容
        $adsArr = $this->formatToView($adsArr);
        //4.tracking Sponsor Transfer数据记录
        $costTime = Utilities::getMicrotime() - $startTime;
        TrackingFE::registerSponsorTransfer(
            $this->keyword,
            $costTime,
            $this->totalCount,
            $this->showCount,//showCount
            $this->chid,
            '',//masterKeyword
            $this->channelTag,
            $this->requestAdsCount,
            \Tracking_Constant::SPONSOR_SOUGOU
        );
        //5.打印信息
        if(isset($_COOKIE['DAHONGBAO_TEST']) && $_COOKIE['DAHONGBAO_TEST'] == "YES") {
            //logWarn("Test outer: ".$reqUrl);
            echo $reqUrl . "\n<BR><BR><PRE>\n";
            print_r($adsArr);
            echo "\n\n</PRE>\n";
        }
        return $adsArr;
    }
    
    /**
     * 生成HTML
     * @param  array $adsGroupArr 广告数组结果
     * @return  string 广告结果
     */
    public function renderTemplate(&$adsGroupArr) {
        if (empty($adsGroupArr)) {
            return false;
        }
        $pingBack = false;
        $strHtml = '';
        $convertedKeyword = htmlspecialchars($this->keyword);
        for ($loop = 0; $loop <= count($adsGroupArr); $loop++) {
            if ($adsGroupArr[$loop]) {
                $position = $loop + 1;
                $strHtml .= " var {$this->varPreName}{$position} = '<div class=\"favBg\">".
                    "<div class=\"favTl\"><div class=\"favTr\"></div></div>". 
                    "<div class=\"favM\"><div class=\"\">";
                if ($this->IsDisplayKeywordArr[$loop] === true) {
                    $strHtml .= "<div class=\"search_keyword\">欢迎到这里选购 ".
                        "<strong>{$convertedKeyword}</strong> 相关产品</div>";
                } else if ($this->IsDisplayKeywordArr[$loop]) {
                    //显示特殊值
                    $decoratedKeyword = str_replace('{KEYWORD}', $convertedKeyword, $this->IsDisplayKeywordArr[$loop]);
                    $strHtml .= "<div class=\"search_keyword\">".$decoratedKeyword."</div>";
                }
                $strHtml .= "<div class=\"google_four\">";
                if ($this->DisplaySingleTemplate[$loop]) {
                    foreach ($adsGroupArr[$loop] as $ads) {
                        $strHtml .= "<ul onMouseOver=\"return glb_sogou_ss(\'{$ads['url']}\', this)\" onMouseOut=\"glb_sogou_cs(this)\">".
                                "<li><a href=\"{$ads['url']}\" target=\"_blank\">".
                                "<span class=\"b_title\">{$ads['title']}</span>".
                                "<span class=\"b_text\">{$ads['summary']}</span>".
                                "<span class=\"b_url\">{$ads['showurl']}</span></a></li>".
                                "</ul>";
                    }
                } else {
                    foreach ($adsGroupArr[$loop] as $ads) {
                        $strHtml .= "<ul onMouseOver=\"return glb_sogou_ss(\'{$ads['url']}\', this)\" onMouseOut=\"glb_sogou_cs(this)\">".
                            "<li class=\"li_title\"><a href=\"{$ads['url']}\" target=\"_blank\">".
                            "{$ads['title']}</a></li>".
                            "<li class=\"li_text\"><a href=\"{$ads['url']}\" target=\"_blank\">{$ads['summary']} </a></li>".
                            "<li class=\"li_url\"><a href=\"{$ads['url']}\" target=\"_blank\">".
                            "{$ads['showurl']}</a></li>".
                            "</ul>";
                    }
                }
                //前台回调sogou url
                if (!$pingBack) {
                    $strHtml .= "<img src=\"".$this->pingback."\" width=0 height=0 />";
                     $pingBack = true;
                }
                $strHtml .= "</div></div></div>".
                    "<div class=\"favBl\"><div class=\"favBr\"></div></div>".
                    "</div>';";
                $strHtml .= "\r\n";
            }
        }
        
        return $strHtml;
    }
    
    /**
     * 生成请求Sogou URL,JS再次调用
     */
    public function getRequestUrl() {
        if (defined("__MEZI_ADS")) {
            $adsURL = __MEZI_ADS;
        } 
        else {
            $adsURL = "http://www.smarter.com.cn/async_mezi_ads.php";
        }
        $this->oldParamArr['adsName'] = "sogou";
        $adsURL .= "?params=";
        $adsURL .= Utilities::encode(serialize($this->oldParamArr));
        return $adsURL;
    }

    /**
     * 得到请求的URL
     */
    public function makeRequestUrl() {
        $gbkWord = urlencode(iconv("UTF-8", "GBK", $this->keyword));
        $clientIP = Utilities::getClientIPForGG();//得到客户端请求的IP
        $url = $this->sogouDomain."?query=".$gbkWord."&cnt=".$this->requestAdsCount
               ."&leadip=".$clientIP.'&highlight=on&withad=on';
        if (!empty($this->pid)) {
            $url .= "&pid=".$this->pid;
        }
        return $url;
    }
    
    //根据请求URL, 得到XML数组，解析成PHP数组格式
    public function parseAds($reqUrl) { 
        $curl = CURL::getInstance();
        $curl->setTimeout($this->timeout);
        $str = $curl->get_contents($reqUrl);
        if (empty($str)) {
            //logWarn("Sogou ads XML Result Is Empty (URL={$reqUrl}\nContent:\n{$str}\n).");
            return array();
        }
        //设置转换高亮
        $str = str_replace(
            array(chr(0xfd) . chr(0xa1), chr(0xfd) . chr(0xa2)), 
            array("<![CDATA[<strong>]]>", "<![CDATA[</strong>]]>"), 
            $str
        );
        $strCharPos = stripos($str, "?>");
        $utf8Str = '<?xml version="1.0" encoding="UTF-8"'. substr($str, $strCharPos);
        $utf8Str = iconv("GBK", "UTF-8//IGNORE", $utf8Str);
        $dom = new DomDocument();
        $dom->loadXML($utf8Str, LIBXML_NOCDATA);
        if (is_object($dom->documentElement) == false) {
            //注:只能记正式环境中的XML文件内容
            //logWarn("Sogou ads XML error(URL={$reqUrl}\nContent:\n{$utf8Str}\n).");
            return array();
        }
        $arr = array();
        $adsCount = 0;
        //页面中回调API链接地址
        $this->pingback = $dom->documentElement->getElementsByTagName("pingback")->item(0)->nodeValue;
        $adItems = $dom->documentElement->getElementsByTagName("aditem");
        if ($adItems->length <= 0 || empty($this->pingback)) {
            //logWarn("Sogou ads XML NODE aditem or pingback Is Empty(URL={$reqUrl}\nContent:\n{$str}\n).");
            return array();
        }
        //搜狗实际返回广告数量
        $this->totalCount = intval($adItems->length);
        foreach ($adItems as $index => $adItem) {
            if ($adsCount >= $this->requestAdsCount) {
                break;
            }
            $lines = $adItem->childNodes;
            $tmpArr = array();
            foreach ($lines as $line) {
                if ($line->nodeName == "title") {
                    $tmpArr['title'] = $line->nodeValue;
                }
                if ($line->nodeName == "summary") {
                    $tmpArr['summary'] = $line->nodeValue;
                }
                if ($line->nodeName == "link") {
                    $tmpArr['link'] = trim($line->nodeValue);
                }
                if ($line->nodeName == "showurl") {
                    $tmpArr['showurl'] = trim($line->nodeValue);
                    //过滤聪明点和大红包自己的广告
                    $pos1 = stripos($tmpArr['showurl'], 'dahongbao.com');
                    $pos2 = stripos($tmpArr['showurl'], 'smarter.com.cn');
                    if ($pos1 !== false || $pos2 !== false) {
                        $tmpArr = array();
                        break;
                    }
                }
                if ($line->nodeName == "type") {
                    $tmpArr['sponsorType'] = $this->adsTypeMap[intval($line->nodeVlaue)];
                }
            }
            
            if ($tmpArr) {
                $tmpArr['bidPos'] = $index + 1;
                $adsCount++;
                $arr[] = $tmpArr;
            }
        }
        //搜狗实际展示数量
        $this->showCount = $adsCount;
        return $arr;
    }
    
    /**
     * 高亮sogou广告元素
     * @param string $adsFieldText
     * @return string
     */
    public function formatAds($adsFieldText) {
        if (empty($adsFieldText)) {
            return '';
        }
        $adsFieldText = Utilities::cutString(trim($adsFieldText), 120);
        return addslashes($adsFieldText);
    }
    
    //格式化广告显示的内容
    public function formatToView($adsData) {
        if (count($adsData) == 0) {
            return NULL;
        }
        //tracking 统计SOGOU 广告类型
        $sponsorTypeCountArr = array();
        for ($loop=0, $cnt=count($adsData); $loop<$cnt; $loop++) {
            $adsData[$loop]['title'] = $this->formatAds($adsData[$loop]['title']);
            $adsData[$loop]['summary'] = $this->formatAds($adsData[$loop]['summary']);
            $sponsorTypeCountArr[$adsData[$loop]['sponsorType']]++;
            //SponsorRedir URL
            $adsData[$loop]['url'] = TrackingFE::registerSponsorLink($this->keyword, 
                    $adsData[$loop]['bidPos'],
                    $adsData[$loop]['link'],
                    $adsData[$loop]['showurl'],
                    array( "sponsorType" => $adsData[$loop]['sponsorType'],
                            'channelTag' => $this->channelTag)
            );
        }
        //记录sponsor Impression
        foreach ($sponsorTypeCountArr as $sponsorType => $showCount) {
            TrackingFE::sponsorImpression(
                $this->keyword, 
                $showCount, 
                '', 
                '', 
                $this->channelTag,
                $sponsorType
            );
        }
        return $adsData;
    }
    
    protected function highlightAds(&$adsData) {
        return $adsData;
    }
}
?>