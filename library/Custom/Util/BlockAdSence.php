<?php
/*
 * package_name : BlockAdSence.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: BlockAdSence.php,v 1.1 2013/05/29 06:58:00 rizhang Exp $
 */
namespace Custom\Util;

use Custom\Util\Utilities;

class BlockAdSence 
{
    public static function getBlockList() {
        static $allBlockAdSence = array();
        if ($allBlockAdSence) {
            return $allBlockAdSence;
        }
        $fileName = APPLICATION_PATH . "/data/setting/blockAdSence/blockAdSence.php";
        $allBlockAdSence = Utilities::getPhpArrayCache($fileName);
        return $allBlockAdSence;
    }

    public static function isBlockKeywords($keywords) {
        $allBlockAdSence = self::getBlockList();
        // 原来dhb的词是gbk的
        $keywords = iconv("UTF-8", "GBK//IGNORE", $keywords);
        $keywords = urldecode(strtolower(urlencode($keywords)));
        if ($allBlockAdSence[md5($keywords."Keywords")]) {
            return true;
        }
        return false;
    }
}
?>