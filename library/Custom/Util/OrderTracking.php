<?php
/*
 * package_name : OrderTracking.php
 * ------------------
 * 共同函数
 *
 * PHP versions 5
 * 
 * @Author   : richie zhang(rizhang@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: OrderTracking.php,v 1.1 2013/08/12 09:22:52 rizhang Exp $
 */
namespace Custom\Util;

class OrderTracking {
	private static $cache;

	public static function getUrlString($merchantId, $channelId) {
		$result = null;
		if (self::$cache == null) {
			self::$cache = Utilities::getPhpArrayCache(self::getCacheFilePath());
		}

		$typeId = null;
		if (!isset(self::$cache['config'][$merchantId][$channelId])) {
			if (isset(self::$cache['config'][$merchantId][0])) {
				$typeId = self::$cache['config'][$merchantId][0];
			}
		} else {
			$typeId = self::$cache['config'][$merchantId][$channelId];
		}

		if ($typeId != null) {
			$type = self::$cache['type'][$typeId];
			$result = $type['VerifyString'].'|';
			if ($type['InsertPosition'] == 'FIRST') {
				$result .= '?';
			} else {
				$result .= '&';
			}
			$result .= $type['ClickIDParamName'].'=';
		}
		return $result;
	}

	private static function getCacheFilePath() {
	    return APPLICATION_PATH . "/data/setting/orderTracking/order_tracking.php";
	}
}
