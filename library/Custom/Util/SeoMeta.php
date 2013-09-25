<?php
/*
 * package_name : SeoMeta.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : richie (rizhang@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: SeoMeta.php,v 1.2 2013/07/22 08:34:08 rizhang Exp $
 */
namespace Custom\Util;

class SeoMeta 
{
    public static function getMeta($pageType, $params = array(), $siteId = 1) {
        $params = self::preProcess($pageType, $params, $siteId);
        $patterns = self::getPattern($pageType, $siteId);
        $meta = self::fillPattern($patterns, $params);
        return $meta;
    }

    public static function preProcess(&$pageType, $params, $siteId) {
        if ($params['pageno'] == 1) {
            unset($params['pageno']);
        }

        //如果url中没有sortby，不显示默认值
        $dealsPage = array('DealsList', 'SearchDeals');
        $url = $_SERVER['SCRIPT_URL'] ? $_SERVER['SCRIPT_URL'] : $_SERVER['REQUEST_URI'];
        if ($params['sortby'] && strpos($url, 'sortby') === false) {
            unset($params['sortby']);
        }
        if ($params['sortby'] == 'new') {
            if (in_array($pageType, $dealsPage) && $siteId == 1) {
                $params['sortby'] = '最新的';
            } else {
                $params['sortby'] = '最新优先的';
            }
        } elseif ($params['sortby'] == 'hot') {
            if (in_array($pageType, $dealsPage) && $siteId == 1) {
                $params['sortby'] = '最受欢迎的';
            } else {
                $params['sortby'] = '最热优先的';
            }
        }
        return $params;
    }

    public static function getPattern($pageType, $siteId) {
        $file  = APPLICATION_PATH . '/data/setting/seoMeta/'.$siteId.'/'.$pageType.'.php';
        return Utilities::getPhpArrayCache($file);
    }

    public static function fillPattern($patterns, $params) {
        @extract($params);
        foreach ((array)$patterns as $k => $v) {
            if ($v) {
                //convert pattern to php code
                $find = "/\{((?:[a-z])?[^a-z]+)?([a-z_]+)([^a-z]*)\}/";
                $to = '" . ($\\2 ? "\\1".$\\2."\\3" : "") ."';
                $phpCodeStr = preg_replace($find, $to, str_replace('"','\"',$v));
                eval('$pattern="'.$phpCodeStr.'";');
                $meta[$k] = self::cleanMeta($k, $pattern);
            }
        }
        return $meta;
    }

    public static function cleanMeta($metaType, $metaPattern) {
        return trim($metaPattern);
    }
}
?>