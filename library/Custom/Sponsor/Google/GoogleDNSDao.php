<?php
/*
 * Created on 2009-3-18
 * GoogleDNSDao.php
 * -------------------------
 * 
 * 
 * 
 * @author Fan Xu
 * @email fan_xu@mezimedia.com; x.huan@163.com
 * @copyright  (C) 2004-2006 Mezimedia.com
 * @license    http://www.mezimedia.com  PHP License 5.0
 * @version    CVS: $Id: GoogleDNSDao.php,v 1.2 2013/04/22 10:39:44 rizhang Exp $
 * @link       http://www.smarter.com/
 */
namespace Custom\Sponsor\Google;

class GoogleDNSDao {
	private static $dnslookup = null;
	
	public static function setDnslookup($dnslookup) {
		$dnslookup = strtoupper($dnslookup);
		if($dnslookup == "IP" || $dnslookup == "DOMAIN") {
			self::$dnslookup = $dnslookup;
		} else {
			self::$dnslookup = null;
		}
	}
	
	public static function getDnslookup() {
		if(self::$dnslookup) return self::$dnslookup;
		return array ('ggdns' => 'DOMAIN');
	}
	
	public static function getGoogleIP() {
		$ips = self::loadGoogleIP();
		$cnt = count($ips);
		if($cnt == 0) return "";
		else if($cnt == 1) return $ips[0];
		else return $ips[rand(0, $cnt - 1)];
	}
	
	public static function loadGoogleIP() {
		$content = @file_get_contents(__SETTING_FULLPATH."config_main/google_ip.txt");
		$content = trim($content);
		if(!$content) return array();
		$arr = explode("\n", $content);
		$ips = array();
		for($i=0,$end=count($arr); $i<$end; $i++) {
			$ip = trim($arr[$i]);
			$len = strlen($ip);
			if($len >= 7 && $len <= 15) {
				$ips[] = $ip;
			}
		}
		return $ips;
	}
	
	public static function storeGoogleIP($ips) {
		if(count($ips) == 0) throw new \Exception("ip list is empty.");
		foreach($ips as $ip) {
			if(!preg_match("/^(\d{1,3}\.){3}(\d{1,3})\$/", $ip)) {
				throw new \Exception("ip[{$ip}] format error.");
			}
		}
		$content = implode("\n", $ips);
		file_put_contents(__SETTING_FULLPATH."config_main/google_ip.txt", $content);
	}
}
?>