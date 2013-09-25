<?php
/**
 * @author     Menny_Zhang <Menny_Zhang@mezimedia.com>
 * @copyright  (C) 2004-2005 Mezimedia.com
 * @license    http://www.mezimedia.com  PHP License 5.0
 * @version    CVS: $Id: class.TrackingFE.php,v 1.19 2008/12/24 08:29:56
 * @link       http://www.smarter.com/
 */
namespace Custom\Util;

use Custom\Util\Utilities;
use Custom\Util\OrderTracking;
use Custom\Sponsor\Google\GoogleTagDao;

class TrackingFE {
    protected static $_instance = NULL;

    private  $offerImprInfo = array();


    public function __construct(){

    }

    public static function getSource(){
        return \Tracking_Session::getInstance()->getSource();
    }

    public static function getSourceGroup(){
        return \Tracking_Session::getInstance()->getSourceGroup();
    }
    
    /**
     * 调用重定向程序
     */
    public static function execStatHeader($url, $status) {
        //statHeader($url, $status);
        $status = (int)$status;
        \Tracking_Response::getInstance()->setRedirect($url, $status)->sendResponse();
        exit();
    }

    /**
     * 得到用户点击广告数量，tracking cookie 和 前台cookie 比较取最大值
     * */
    public static function getSponsorClicks() {
        if (!empty($_COOKIE['fe_slclkcnt'])) {
            //检测是否是同一个sessionID
            list($feAdsClickCount, $oldSessionID) = explode("|", $_COOKIE['fe_slclkcnt']);
            $sessionID = \Tracking_Session::getInstance()->getSessionId();
            //如果是新sessionID 重新设置fe_slclkcnt的值
            if ($feAdsClickCount && $oldSessionID != $sessionID) {
                $feAdsClickCount = 0;
                $new_fe_slclkcnt = "$feAdsClickCount|$sessionID";
                setcookie('fe_slclkcnt', $new_fe_slclkcnt, null, '/', 'dahongbao.com');
            }
        } else {
            $feAdsClickCount = 0;
        }
        $mmAdsClickCount = intval(\Tracking_Session::getInstance()->getSponsorClicks());
        return max(0, intval($feAdsClickCount), $mmAdsClickCount);
    }

    public static function isSponsorClickLimit() {
        return (self::getSponsorClicks() >= \Tracking_Session::MAX_SPONSOR_CLICKS) && \Tracking_Session::getInstance()->isLimitTraffic();
    }

    public static function isReloadPage() {
        return (self::getSponsorClicks() < \Tracking_Session::MAX_SPONSOR_CLICKS) && \Tracking_Session::getInstance()->isLimitTraffic();
    }

    public static function getTrafficType() {
        return \Tracking_Session::getInstance()->getTrafficType();
    }

    public static function getReferer(){
        return \Tracking_Session::getInstance()->getReferer();
    }

    public static function registerSponsorTransfer($keyword,$costTime,$resultCount, $showCount=0, $chid=0, $masterKeyword="",
        $channelTag = '', $requestcount, $sponsorType = \Tracking_Constant:: SPONSOR_GOOGLE){
        if($keyword == "") return; //Keyword为空,不调用google api

        $logParmas = array("sponsortype" => $sponsorType,
                           "channelid"   => $chid,
                            "keyword"     => ($masterKeyword?$keyword.'|'.$masterKeyword:$keyword),
                            "channeltag"  => $channelTag,
                            "costtime"    => $costTime,
                            "resultcount" => $resultCount,
                            "requestcount" => $requestcount,
                            "requestip"   => Utilities::getClientIPForGG());
        //百度,搜狗广告单独调用Impression
        if (!in_array($sponsorType, 
            array(\Tracking_Constant:: SPONSOR_BAIDU, \Tracking_Constant:: SPONSOR_SOUGOU))) {
            self::sponsorImpression($keyword,$showCount,$chid,$masterKeyword,$channelTag);
        }
        \Tracking_Logger::getInstance()->sponsorTransfer($logParmas);
    }

    public static function sponsorImpression($keyword,$impressionCount,$chid=0,$masterKeyword="",
        $channelTag = '', $sponsorType = \Tracking_Constant::SPONSOR_GOOGLE) {
        $logParmas = array(
                           'sponsortype'     => $sponsorType,
                           'channelid'       => $chid,
                           'keyword'         => $keyword,
                           'channeltag'      => $channelTag,
                           'impressionCount' => $impressionCount,
                           'keyword2'        => $masterKeyword
                          );
        \Tracking_Logger::getInstance()->sponsorImpression($logParmas);
    }

    /**
      * @register the redir url of sponsor
      * $params $keyword 广告关键字
      *         $bidpos 显示为位置
      *         $adsurl 广告地址
      *         $siteUrl 广告引用地址
      *         $extraParams = array("chid"=>0,
      *                              "masterKeyword"=>'',
      *                              "channelTag"=>'') 其他附加参数
      *
      * @return url
      */
    public static function registerSponsorLink($keyword, $bidpos, $adsurl, $siteUrl,$extraParams=array()) {
        if (empty($extraParams["sponsorType"])) {    //默认是GOOGLE 广告类型
            $extraParams["sponsorType"] = \Tracking_Constant:: SPONSOR_GOOGLE;
        }
        $baiduSponsorTypeArr = array(
            \Tracking_Constant::SPONSOR_BAIDU_PROMOTION,
            \Tracking_Constant::SPONSOR_BAIDU_ACCURATE,
            \Tracking_Constant::SPONSOR_BAIDU_INTELLIGENT
        );
        $sogouSponsorTypeArr = array(
            \Tracking_Constant::SPONSOR_SOUGOU_BIDDING,
            \Tracking_Constant::SPONSOR_SOUGOU_SUGGEST
        );
        if ($extraParams["sponsorType"] == \Tracking_Constant:: SPONSOR_GOOGLE) {
            $tagParams = GoogleTagDao::getTagParams($keyword);
        } else if (in_array($extraParams["sponsorType"], $baiduSponsorTypeArr)) {
           $tagParams = BaiduTagDao::getTagParams($keyword);
        } else if (in_array($extraParams["sponsorType"], $sogouSponsorTypeArr)) {
           $tagParams = array();
        } else {
            throw new Exception("sponsorType {$extraParams['sponsorType']} does not exists.");
        }
        $params = array(\Tracking_Uri::BUILD_TYPE            => "sponsor",
                       \Tracking_Uri::KEYWORD               => $keyword,
                       \Tracking_Uri::KEYWORD_SUPPLEMENTARY => $extraParams["masterKeyword"],
                       \Tracking_Uri::DESTINED_URL          => $adsurl,
                       \Tracking_Uri::DISPLAY_POSITION      => $bidpos,
                       \Tracking_Uri::CHANNEL_ID            => $extraParams["chid"],
                       \Tracking_Uri::SPONSOR_TYPE           => $extraParams["sponsorType"],
                       \Tracking_Uri::ADVERTISER_HOST       => $siteUrl);
        $params[\Tracking_Uri::CHANNEL_TAG] = $extraParams["channelTag"] ? $extraParams["channelTag"] : '';
        $params[\Tracking_Uri::IS_MATCHED] = $tagParams["isMatched"];
        $params[\Tracking_Uri::EXPIRED_TIME] = $tagParams["expireTime"];
        $params[\Tracking_Uri::REQUEST_COUNTRY] = $tagParams["country"];
        return \Tracking_Uri::build($params);
    }

    public static function execNotFoundHeader($status) {
        //statHeader($url, $status);
        $status = (int)$status;
        \Tracking_Response::getInstance()->setHttpResponseCode($status)->sendResponse();
    }

    /*
     * 大红包US的Tracking，OfferUrl
     * $impression是否算展示，收藏和领取优惠券buildCoupon不算Impression
     */
    public function registerOfferLink ($params = array(), $impression = true, $outgoing = true){
        if ($impression) {
            \Tracking_Logger::getInstance()->affiliateImpression(array(
                                        "offerid" => 2,//1国内 ,2国外
                                        "channelid"  => $params['CouponType'] == 'COUPON' ? '1' : '2', //1Coupon 2Deals 3Merchant
                                        "productid"  => $params['CouponID'],
                                        "merchantid" => $params['MerchantID'],
            ));
        }
        if ($outgoing) {
	        $offerUrl = \Tracking_Uri::build(array(
	                                    \Tracking_Uri::BUILD_TYPE   => 'cmusaffiliate',
	                                    \Tracking_Uri::DESTINED_SITE => $params['Name'],
	                                    \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
	                                    \Tracking_Uri::OFFER_ID     => 2,
	                                    \Tracking_Uri::CHANNEL_ID   => $params['CouponType'] == 'COUPON' ? '1' : '2',
	                                    \Tracking_Uri::PRODUCT_ID   => $params['CouponID'],
	                                    \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
	                                    \Tracking_Uri::DESTINED_URL => $params['CouponUrl'],
	        ));
        }
        return base64_encode($offerUrl);
    }

    /*
     * 大红包US的Tracking，MerchantUrl
     */
    public function registerMerchantLink ($params = array()){
        /*
        \Tracking_Logger::getInstance()->affiliateImpression(array(
                                    "offerid" => 2,
                                    "channelid"  => 3,
                                    "productid"  => "",
                                    "merchantid" => $params['MerchantID'],
        ));
        */
        $merchantOfferUrl = \Tracking_Uri::build(array(
                                    \Tracking_Uri::BUILD_TYPE   => 'cmusaffiliate',
                                    \Tracking_Uri::DESTINED_SITE => $params['Name'],
                                    \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
                                    \Tracking_Uri::OFFER_ID     => 2,
                                    \Tracking_Uri::CHANNEL_ID   => 3,
                                    \Tracking_Uri::PRODUCT_ID   => "",
                                    \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                                    \Tracking_Uri::DESTINED_URL => $params['MerchantUrl'],
        ));
        return base64_encode($merchantOfferUrl);
    }
    
    /*
     * 大红包US的Trackimg,ProductUrl
     */
    public function registerProductLink($params = array()) {
        $productOfferUrl = \Tracking_Uri::build(array(
                                \Tracking_Uri::BUILD_TYPE   => 'cmusaffiliate',
//                                 \Tracking_Uri::DESTINED_SITE => $params['Name'],
//                                 \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
//                                 \Tracking_Uri::OFFER_ID     => 2,
//                                 \Tracking_Uri::CHANNEL_ID   => 3,
//                                 \Tracking_Uri::PRODUCT_ID   => "",
//                                 \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                                \Tracking_Uri::DESTINED_URL => $params['TargetUrl'],
        ));
        return base64_encode($productOfferUrl);
    }

    /*
     * 大红包US的Tracking，BannerUrl
    * $position = 1 首页右上角Banner
    */
    public function registerBannerLink ($params = array(), $position = 1) {

        $bannerOfferUrl = \Tracking_Uri::build(array(
                \Tracking_Uri::BUILD_TYPE   => 'cmusaffiliate',
                \Tracking_Uri::DESTINED_SITE => $params['Name'],
                \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
                \Tracking_Uri::OFFER_ID     => 2,
                \Tracking_Uri::CHANNEL_ID   => $position,
                \Tracking_Uri::PRODUCT_ID   => $params['CouponID'],
                \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                \Tracking_Uri::CLICK_AREA   => $params['dispos'],
                \Tracking_Uri::DESTINED_URL => $params['CouponUrl'] ? $params['CouponUrl'] : $params['MerchantUrl'] ,
        ));
        return base64_encode($bannerOfferUrl);
    }

    /*
     * 大红包CN的Tracking，buildCoupon拼接CouponMountain的redir
     * $merchant是否商家跳转，默认是Coupon Offer，商家页面跳转商家地址
     * $impression是否算展示，收藏和领取优惠券buildCoupon不算Impression
     * 不需要bulid CouponMountain的redir.php
     */
    public function registerDHBOfferLink ($params = array(), $impression = true, $outgoing = true){
        if ($impression) {
            \Tracking_Logger::getInstance()->affiliateImpression(array(
                                        "offerid" => 1,
                                        "channelid"  => $params['CouponType'] == 'COUPON' ? '1' : '2',
                                        "productid"  => $params['CouponID'],
                                        "merchantid" => $params['MerchantID'],
            ));
        }
        if ($outgoing) {
            $destined_url = self::getFullDEestinedUrl($params);
            $offerUrl = \Tracking_Uri::build(array(
                                        \Tracking_Uri::BUILD_TYPE   => 'affiliate',
                                        \Tracking_Uri::DESTINED_SITE => $params['Name'],
                                        \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
                                        \Tracking_Uri::OFFER_ID     => 1,
                                        \Tracking_Uri::CHANNEL_ID   => $params['CouponType'] == 'COUPON' ? '1' : '2',
                                        \Tracking_Uri::PRODUCT_ID   => $params['CouponID'],
                                        \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                                        \Tracking_Uri::DESTINED_URL => $destined_url,
            ));
        }
        return base64_encode($offerUrl);
    }

    /*
     * 大红包CN的Tracking，MerchantUrl
     * 不需要bulid CouponMountain的redir.php
     */
    public function registerDHBMerchantLink ($params = array()) {
        /*
        \Tracking_Logger::getInstance()->affiliateImpression(array(
                                    "offerid" => 1,
                                    "channelid"  => 3,
                                    "productid"  => "",
                                    "merchantid" => $params['MerchantID'],
        ));
        */
        $params['IsAffiliateUrl'] = 'NO'; //默认需要拼接 AffiliateUrl
        $destined_url = self::getFullDEestinedUrl($params, true);
        $merchantOfferUrl = \Tracking_Uri::build(array(
                                    \Tracking_Uri::BUILD_TYPE   => 'affiliate',
                                    \Tracking_Uri::DESTINED_SITE => $params['Name'],
                                    \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
                                    \Tracking_Uri::OFFER_ID     => 1,
                                    \Tracking_Uri::CHANNEL_ID   => 3,
                                    \Tracking_Uri::PRODUCT_ID   => "",
                                    \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                                    \Tracking_Uri::DESTINED_URL => $destined_url,
        ));
        return base64_encode($merchantOfferUrl);
    }


    /*
     * 大红包CN的Tracking，BannerUrl
     * 不需要bulid CouponMountain的redir.php
     * $position = 4 首页右上角Banner
     * $position = 5 首页左上角Banner
     */
    public function registerDHBBannerLink ($params = array(), $position) {
        /*
        \Tracking_Logger::getInstance()->affiliateImpression(array(
                                    "offerid" => 1,
                                    "channelid"  => $position,             // 广告位置
                                    "productid"  => $params['CouponID'],   // 产品id
                                    "merchantid" => $params['MerchantID'], // 商家id
                                    "dispos"     => $params['dispos'],     // 第几个张图片
        ));
        */
        $destined_url = self::getFullDEestinedUrl($params, false);
        $bannerOfferUrl = \Tracking_Uri::build(array(
                                    \Tracking_Uri::BUILD_TYPE   => 'affiliate',
                                    \Tracking_Uri::DESTINED_SITE => $params['Name'],
                                    \Tracking_Uri::BEACON_ID    => $params['UrlVarible'],
                                    \Tracking_Uri::OFFER_ID     => 1,
                                    \Tracking_Uri::CHANNEL_ID   => $position,
                                    \Tracking_Uri::PRODUCT_ID   => $params['CouponID'],
                                    \Tracking_Uri::MERCHANT_ID  => $params['MerchantID'],
                                    \Tracking_Uri::CLICK_AREA   => $params['dispos'],
                                    \Tracking_Uri::DESTINED_URL => $destined_url,
        ));
        return base64_encode($bannerOfferUrl);
    }

    public function getFullDEestinedUrl($params = array(), $merchant = false) {
        if (empty($params)) {
            return false;
        }
        $couponUrl   = trim($params['CouponUrl']);
        $merchantUrl = trim($params['MerchantUrl']);
        //CouponUrl没有就显示MerchantUrl
        $couponUrl = $couponUrl ? $couponUrl : $merchantUrl;
        $destined_url = $merchant ? $merchantUrl : $couponUrl;
        $affiliateUrl = trim($params['AffiliateUrl']);

        if ($params['IsAffiliateUrl'] == 'NO' && $affiliateUrl) {
            preg_match("/[{]+dhb_cl+[}]+/", $affiliateUrl, $match);
            if ($match) {
                //包含{dhb_cl}，{dhb_cl} => 替换$destined_url，再有{}urlencode
                $repalce = str_replace(array('dhb_cl', '}'), '', $match[0]);
                $urlEncodeCount = strlen($repalce) - 1;
                for ($i = 0; $i < $urlEncodeCount; $i ++) {
                    $destined_url = urlencode($destined_url);
                }
                $destined_url = str_replace($match[0], $destined_url, $affiliateUrl);
            } else {
                //默认affiliateUrl + urlencode(CouponUrl);
                $needUrlcodeAffiliates = array(
                  'http://track.weiyi.com/'=>1, //唯一联盟        
                  'http://click.linktech.cn/'=>1, //领克特联盟
                  'http://www.leecr.com/'=>1, //MerchantId:61779
                  'http://www.herbuy.com.cn/' => 1 //Merchant 61706
                );
	            $pos = strpos($affiliateUrl, '/', 10); //必须以“http://”开头 
	            if($pos > 0 && isset($needUrlcodeAffiliates[substr($affiliateUrl, 0, 1+$pos)])) {
	                $destined_url = $affiliateUrl."".urlencode($destined_url);
	            } else {
	                $destined_url = $affiliateUrl."".$destined_url;
	            }
            }
        }
        return $destined_url;
    }

    public static function addSearchLog($searchLog) {
        if(!$searchLog || !is_array($searchLog)){
            return false;
        }
        $source = self::getSource();
        $semKeyword = \Tracking_Session::getInstance()->getKeyword();
        if(isset($searchLog['isrealsearch'])){
            $isrealsearch = $searchLog['isrealsearch'];
        } else {
            $isrealsearch = "YES";
        }
        
        if(strlen($source)>0 && $semKeyword == $searchLog['keyword']) {
               $isrealsearch = "NO";
        }
        if(!$searchLog['resultsize']) {
            $searchLog['resultsize'] = 0;
        }
        if(!$searchLog['searchtype']) {
            $searchLog['searchtype'] = 1;
        }
        if(!$searchLog['channelid']) {
            $searchLog['channelid']  = 0;
        }
        if(!$searchLog['categoryid']) {
            $searchLog['categoryid'] = 0;
        }
        \Tracking_Logger::getInstance()->search(array(
                       "channelid"        => $searchLog['channelid'],
                       "categoryid"       => $searchLog['categoryid'],
                       "costtime"         => $searchLog['costtime'],
                       "desturl"          => $searchLog['desturl'],
                       "iscached"         => $searchLog['iscached'] ? "YES" : "NO",
                       "isrealsearch"     => $isrealsearch,
                       "keyword"          => $searchLog['keyword'],
                       "matchkeyword"     => $searchLog['matchkeyword'],
                       "productid"        => $searchLog['productid'],
                       "resultcount"      => $searchLog['resultcount'],
                       "resultsize"       => $searchLog['resultsize'],
                       "resulttype"       => $searchLog['resulttype'],
                       "searchenginetype" => $searchLog['searchtype'],
                       "source"           => $source,
                       "totalcosttime"    => $searchLog['totalcosttime'],
                       "responsetime"     => $searchLog['ResponseTime'],
        ));
    }

    /*
     * 老大红包 google source tracking
     */
    public static function getSmarterOfferUrl($offerInfo = array()) {
        \Tracking_Logger::getInstance()->offerImpression(array(
                "channelid"          => $offerInfo['chid'],
                "offerid"            => $offerInfo['offerid'],
                "productid"          => $offerInfo['prodid'],
                "merchantid"         => $offerInfo['merid'],
                "bidposition"        => $offerInfo['bidpos'],
                "totalmerchantcount" => $offerInfo['mercount'],
                "showarea"           => 0,
                "displayposition"    => 1,
                "datasource"         => $offerInfo['datasource'],
                "sdcofferid"         => 0,
                "businesstype"       => $offerInfo['mertype']
        ));

        $orderTrackingString =  OrderTracking::getUrlString($offerInfo['merid'], $offerInfo['chid']);

        $offerUrl = \Tracking_Uri::build(array(
                        \Tracking_Uri::BUILD_TYPE        => 'offer',
                        \Tracking_Uri::CHANNEL_ID        => $offerInfo['chid'],
                        \Tracking_Uri::OFFER_ID          => $offerInfo['offerid'],
                        \Tracking_Uri::MERCHANT_COUNT    => $offerInfo['mercount'],
                        \Tracking_Uri::DISPLAY_POSITION  => 1,
                        \Tracking_Uri::PRICE_RANK        => $offerInfo['pricerank'],
                        \Tracking_Uri::RATE_RANK         => $offerInfo['raterank'],
                        \Tracking_Uri::SORT_BY           => $offerInfo['sortby'],
                        \Tracking_Uri::COUPON_BEACON     => $orderTrackingString,
        ));
        $destinedHost = \Mezi_Config::getInstance()->tracking->affiliate->smcn->redir;
        $offerUrl = $destinedHost . $offerUrl;

        $affiliateUrl = \Tracking_Uri::build(array(
            \Tracking_Uri::BUILD_TYPE        => 'smcnaffiliate',
            \Tracking_Uri::DESTINED_SITE     => $offerInfo['mertype'],
            \Tracking_Uri::BEACON_ID         => $offerInfo['beacon'],
            \Tracking_Uri::OFFER_ID          => $offerInfo['offerid'],
            \Tracking_Uri::CHANNEL_ID        => $offerInfo['chid'],
            \Tracking_Uri::PRODUCT_ID        => $offerInfo['prodid'],
            \Tracking_Uri::MERCHANT_ID       => $offerInfo['merid'],
            \Tracking_Uri::CLICK_AREA        => 0,
            \Tracking_Uri::DESTINED_URL      => $offerUrl,
        ));    
        //return base64_encode($offerUrl);
        return base64_encode($affiliateUrl);
    }

    public static function getClickTrackUrl ($pos, $keyword, $tagInfo = array()) {
        $ct = array(
            'clickArea'  => $pos,
            'channelTag' => $tagInfo['clientID']."|".$tagInfo['channelTag'],
            'keyword'    => $keyword,
            'currandstr' => \Tracking_Session::getInstance()->getRequestId(),
            'pathname'   => '',
            'revenuePartner'     => '',
            'revenuePartnerType' => '',
            'countryCode' => \Tracking_Session::getInstance()->getSiteId(),
            'stateCode' => '',
            'cityCode'  => '',
            'sessionID' => \Tracking_Session::getInstance()->getSessionId(),
        );
        $ct = implode('{&&}', $ct);
        return \Mezi_Utility::encrypt($ct).'&pos='.$pos;
    }
 }