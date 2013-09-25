<?php
/*
 * package_name : PathManager.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: PathManager.php,v 1.3 2013/06/14 02:24:44 rizhang Exp $
 */
namespace Custom\Util;

use Custom\Util\Utilities;

class PathManager 
{
    /**
     * 
     * Merchant List
     */
    public static function getAllMerchantUrl(){
        return "/all-merchant/";
    }

    /**
     * 
     * Mrchant Type [A-Z]{1}
     */
    public static function getMerchantKeyUrl($key){
        if (empty($key)) {
            return self::getAllMerchantUrl();
        }

        return "/all-merchant-{$key}/";
    }

    /**
     * 
     * Merhant Detail [0-9]+
     */
    public static function getMerchantDetailUrl($merid, $page = null){
        $merid = (int)$merid;
        if (empty($page)) {
            return "/merchant-{$merid}/";
        }
        return "/merchant-{$merid}/page-{$page}/";
    }
    /**
     *
     * Merhant Detail Deals [0-9]+
     */
    public static function getMerchantDealsUrl($merid, $page = null){
        $merid = (int)$merid;
        if (empty($page)) {
            return "/merchant-{$merid}/deals/";
        }
        return "/merchant-{$merid}/deals/page-{$page}/";
    }

    /**
     * 
     * Merchant Category
     */
    public static function getMerchantCateUrl($merid, $catid) {
        $merid = (int)$merid;
        $catid = (int)$catid;
        return "/category-{$catid}-merchant-{$merid}/";
        
    }

    /**
     * 
     * All Category List
     */
    public static function getAllCateUrl() {
        return "/category/";
    }

    /**
     * 
     * Category List
     */
    public static function getCateListUrl($catid, $page = null) {
        $catid = (int)$catid;
        if ($page) {
            return "/category-{$catid}/page-{$page}/"; 
        }
        return "/category-{$catid}/";
    }
    
    /**
     * 
     * Deal List
     */
    public static function getDealsListUrl() {
        return "/deals/";
    }

    /**
     * 
     * Search Coupon
     */
    public static function getSearchCouponUrl($keyword) {
        return "/s-coupon-{$keyword}/";
    }

    /**
     * 
     * Search Deals
     */
    public static function getSearchDealsUrl($keyword) {
        return "/s-deals-{$keyword}/";
    }

    /**
     * 
     * Article Category
     */
    public static function getArticleCateUrl($catid = null) {
        if (empty($catid)) {
            return "/help/";
        }
        return "/help-{$catid}/";
    }

    /**
     * 
     * Article Detail
     */
    public static function getArticleDetailUrl($aid) {
        return "/article-{$aid}/";
    }

    //------------------ 大红包  ------------------//
    /*
     * Coupon Overall List Page
     */
    public function getDhbCouponUrl($page = null, $sortby = null) {
        $str = "/quan-all/";
        if ($page) {
            $str .= 'page-'.$page.'/';
        }
        if ($sortby) {
            $str .= 'sortby-'.$sortby.'/';
        }
        return $str;
    }

    /*
     * Coupon Category List Page
     */
    public static function getDhbCouponCateUrl($cid, $page = null, $sortby = null) {
        $str = "/category-{$cid}/";
        if ($page) {
            $str .= 'page-'.$page.'/';
        }
        if ($sortby) {
            $str .= 'sortby-'.$sortby.'/';
        }
        return $str;
    }

    /*
     * Coupon Detail Page
     */
    public static function getDhbCouponDetailUrl($id) {
        return "/quan-{$id}/";
    }

    /*
     * Merchant Detail Page
     */
    public static function getDhbMerchantDetailUrl($merid, $catid = null) {
        if (empty($catid)) {
            return "/merchant-{$merid}/";
        }
        return "/merchant-{$merid}-cate-{$catid}/";
    }

    /*
     * Deals List Page
     */
    public static function getDhbDealsUrl($page = null, $sortby = null) {
        $str = "/cuxiao/";
        if ($page) {
            $str .= 'page-'.$page.'/';
        }
        if ($sortby) {
            $str .= 'sortby-'.$sortby.'/';
        }
        return $str;
    }

    /*
     * Deals Detail Page
     */
    public static function getDhbDealsDetailurl($id) {
        return "/cuxiao-{$id}/";
    }

    /*
     * Search coupon page
     */
    public static function getDhbSearchCouponUrl($keyword) {
        return "/s-quan-{$keyword}/";
    }

    /*
     * Search Deals page
     */
    public static function getDhbSearchDealsUrl($keyword, $page = null, $sortby = null) {
        $str = "/s-cuxiao-{$keyword}/";
        if ($page) {
            $str .= 'page-'.$page.'/';
        }
        if ($sortby) {
            $str .= 'sortby-'.$sortby.'/';
        }
        return $str;
    }

    /**
     * 
     * Article Category
     */
    public static function getDhbArticleCateUrl($catid = null) {
        if (empty($catid)) {
            return "/help/";
        }
        return "/help-{$catid}/";
    }

    /*
     * Article detail page
     */
    public static function getDhbArticleDetailUrl($aid) {
        return "/article-{$aid}/";
    }

    /*
     * 老大红包产品图片
     */
    public static function getSmarterProductImage($chid, $prodid, $type=NULL, $hasImage="YES"){
        if($type=="big"){
            $img = "product_image_b";
        } else {
            $img = "product_image_s";
        }
        $tPID = ltrim(substr($prodid,3),"0");
        if(strlen($prodid) >= 2) {
            $tSID = substr($prodid, -2);
        } else {
            $tSID = "0" . $prodid;
        }
        return __IMAGE_SMARTER_DOMAIN_NAME."/".$img."/".$chid."/".$tSID."/".$prodid.".jpg";
    }
    
    /**
     * Products
     */
    public static function getAllProductsUrl() {
    	return '/products/';
    }
    
    /**
     * Merchant Products
     */
    public static function getMerProductsUrl($merid, $page = null) {
        $merid = (int)$merid;
        if (empty($page)) {
            return "/merchant-{$merid}/products/";
        }
        return "/merchant-{$merid}/products/page-{$page}/";
    }
    
    /**
     * Merchant Category Products
     */
    public static function getMerCateProductUrl($merid, $catid, $page = null) {
    	$merid = (int)$merid;
    	$catid = (int)$catid;
    	if (empty($page)) {
    	    return "/merchant-{$merid}/category-{$catid}/products/";
    	}
    	return "/merchant-{$merid}/category-{$catid}/page-{$page}/";
    }
    
    /**
     * Category Products Url
     */
    public static function getCateProductUrl($catid, $page = null) {
        $catid = (int)$catid;
        if (empty($page)) {
            return "/category-{$catid}-products/";
        }
        return "/category-{$catid}-products/page-{$page}/";
    }
    
    /*
     *Category Merchant Products List 
     */
    public static function getCateMerProductUrl($catid, $merid, $page = null) {
        $merid = (int)$merid;
        $catid = (int)$catid;
        if (empty($page)) {
            return "/category-{$catid}-merchant-{$merid}/products/";
        }
        return "/category-{$catid}-merchant-{$merid}/products/page-{$page}/";
    }
    
    /**
     * Products Category  Url
     */
    public static function getProductCateUrl($catid, $page = null) {
        $catid = (int)$catid;
        if (empty($page)) {
            return "/category-{$catid}/products/";
        }
        return "/category-{$catid}/products/page-{$page}/";
    }
}
 ?>