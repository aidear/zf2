<?php
/**
 * Utilities.php
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
 * @version CVS: Id: Utilities.php,v 1.0 2013-10-6 下午10:16:59 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Util;

class Utilities 
{
    const __CHARSET = "UTF-8";
    protected static $a = 0;
    protected static $A = 0;
    protected static $z = 0;
    protected static $Z = 0;
    protected static $n0 = 0;
    protected static $n9 = 0;
    protected static $filter = array();
    protected static $saveRefAdsCount = false;  //是否需要保存Ref keyworeds 广告数量
    protected static $pinyinmap = '';

	protected static function init() {
        if(self::$a != 0) {
            return;
        }
        self::$a = ord('a');
        self::$A = ord('A');
        self::$z = ord('z');
        self::$Z = ord('Z');
        self::$n0 = ord('0');
        self::$n9 = ord('9');
//      self::$filter = array(ord('.')=>1, ord(',')=>1);
        self::$filter = array(ord('.')=>1);
    }

    /**
     * get php cache file
     * @return array
     */
    static public function getPhpArrayCache($file) 
    {
        return include $file;
    }
    
    /**
     * set array to php file
     * @param string $file
     * @param array $data
     * @return null
     */
    static public function setPhpArrayCache($file, $data)
    {
        if(!file_exists(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        $phpStr = var_export($data, true);
        $phpFile = "<?php return " . $phpStr . '; ?'.'>';
        file_put_contents($file, $phpFile);
    }
    
    /**
     * 中文编码
     * @params $str UTF8 Encode string
     */
    public static function &encode($str) {
        if(!is_string($str)) {
            return $str;
        }

        $str = iconv(self::__CHARSET, "GBK//IGNORE", $str);
        
        self::init();
        
        $ret = "";
        $len = strlen($str);
        $in = false;
        for($loop=0; $loop<$len; $loop++) {
            $ch = ord($str[$loop]);
            if(($ch >= self::$a && $ch <= self::$z)
                || ($ch >= self::$A && $ch <= self::$Z)
                || ($ch >= self::$n0 && $ch <= self::$n9)
                || isset(self::$filter[$ch])) {
                if($in) {
                    $ret .= "_";
                    $in = false;
                }
                $ret .= chr($ch);
                continue;
            }
            if($in == false) {
                $ret .= "_";
                $in = true;
            }
            $ret .= chr(self::$a + intval($ch/16));
            $ret .= chr(self::$a + intval($ch%16));
            
            if($ch > 127 && $loop+1<$len) { //中文字的第二字节处理
                $ch = ord($str[++$loop]);
                $ret .= chr(self::$a + intval($ch/16));
                $ret .= chr(self::$a + intval($ch%16));
            }
        }
        return $ret;
    }
    
    public static function replaceChineseMarks($string) {
        $chineseMarks = array("“","‘", "　", "，", "。", "（", "）", "［", "］", "－");
        $englishMarks = array("\"", "'", " ", ",", ".", "(", ")", "[", "]", "-");
        return str_replace($chineseMarks, $englishMarks, $string);
    }

    /**
     * 中文解码
     * @params $str GBK Encoded string
     */
    public static function &decode($str) {
        self::init();
        $ret = "";
        $len = strlen($str);
        for($loop=0; $loop<$len; $loop++) {
            if($str[$loop] == '_') {
                for($loop++; $loop<$len; $loop+=2) {
                    if($str[$loop] == '_' || $loop+1 == $len) { //最后一位不加'_'
                        break;
                    }
                    $ch = $str[$loop];
                    $ch2 = $str[$loop+1];
                    $word = (ord($ch) - self::$a) * 16;
                    $word += ord($ch2) - self::$a;
                    if($word < 0) {
                        $word = ord('?');
                    }
                    $ret .= chr($word);
                }
            } else {
                $ret .= $str[$loop];
            }
        }
        $ret = iconv("GBK", self::__CHARSET, $ret);
        return $ret;
    }

    /**
     * 命令行下：实时输出打印信息
     */
    static public function println($msg) 
    {
        echo PHP_EOL . $msg . PHP_EOL;
        flush();
    }
    
    /**
     * 获得当前时间
     * @return datetime Format:2006-03-06 18:10:10
     */
    static function getDateTime($format="Y-m-d H:i:s", $timeStamp = NULL, $timezone = 0) {
        $offset = $timezone * 60 * 60;
        if ($timeStamp == NULL) {
            $timeStamp = time();
        }
        $timeStamp += $offset;
        return date($format, $timeStamp);
    }
    
    /**
     * 系统后台执行脚本
     * @param string $cmd 后台运行脚本
     * @param boolean true|false
     * @param string $outFile
     */
    static function sysCall($cmd, $backgroupFlag = false, $outFile = NULL) {
        if($outFile != null && is_dir(dirname($outFile)) == false) {
            mkdir(dirname($outFile), 0777, true);
        }
        if ($outFile != null && !file_exists($outFile)) {
            file_put_contents($outFile, "");
            chmod($outFile, 0777);
        }
        
        if($backgroupFlag == true) {
            if($outFile == NULL) {
                $outFile = "stdout.log";
            }
            $cmd .= " 1> $outFile 2>&1 &";
        } else if($outFile != NULL) {
            $cmd .= " 1> $outFile 2>&1";
        }
        $ret = system($cmd);
        return $ret;
}

    function getinitial($str, $encoding="UTF-8") {
        return self::getChineseFirstLetter(substr($str, 0, 3), $encoding);
        
        $specialChinese = array('婷' => 'T', '倩' => 'Q', '黛' => 'D');
        if ($letter = $specialChinese[substr($str, 0, 3)]) {
            return $letter;
        }
        $str = iconv($encoding, "GBK", $str);
        $asc=ord(substr($str,0,1));
        if ($asc<160)   { //非中文
            if ($asc>=48 && $asc<=57){
                return '~';//数字
            }elseif ($asc>=65 && $asc<=90){
                return chr($asc); // A--Z
            }elseif ($asc>=97 && $asc<=122){
                return chr($asc-32); // a--z
            }else{
                return '~'; //其他
            }
        }   else { //中文
            $asc=$asc*1000+ord(substr($str,1,1));
            //获取拼音首字母A--Z
            if ($asc>=176161 && $asc<176197){
                return 'A';
            }elseif ($asc>=176197 && $asc<178193){
                return 'B';
            }elseif ($asc>=178193 && $asc<180238){
                return 'C';
            }elseif ($asc>=180238 && $asc<182234){
                return 'D';
            }elseif ($asc>=182234 && $asc<183162){
                return 'E';
            }elseif ($asc>=183162 && $asc<184193){
                return 'F';
            }elseif ($asc>=184193 && $asc<185254){
                return 'G';
            }elseif ($asc>=185254 && $asc<187247){
                return 'H';
            }elseif ($asc>=187247 && $asc<191166){
                return 'J';
            }elseif ($asc>=191166 && $asc<192172){
                return 'K';
            }elseif ($asc>=192172 && $asc<194232){
                return 'L';
            }elseif ($asc>=194232 && $asc<196195){
                return 'M';
            }elseif ($asc>=196195 && $asc<197182){
                return 'N';
            }elseif ($asc>=197182 && $asc<197190){
                return 'O';
            }elseif ($asc>=197190 && $asc<198218){
                return 'P';
            }elseif ($asc>=198218 && $asc<200187){
                return 'Q';
            }elseif ($asc>=200187 && $asc<200246){
                return 'R';
            }elseif ($asc>=200246 && $asc<203250){
                return 'S';
            }elseif ($asc>=203250 && $asc<205218){
                return 'T';
            }elseif ($asc>=205218 && $asc<206244){
                return 'W';
            }elseif ($asc>=206244 && $asc<209185){
                return 'X';
            }elseif ($asc>=209185 && $asc<212209){
                return 'Y';
            }elseif ($asc>=212209){
                return 'Z';
            }else{
                return '~';
            }
        }
    }

        /**
     * 
     * 返回中文拼音首字母
     * 
     * @static $pinyinmap
     * @access public
     * 
     * @author leiminglin
     * @param string $chinese
     * @param string $encoding
     * @return string
     */
    public static function getChineseFirstLetter( $chinese, $encoding='UTF-8' ){
        if($encoding != 'UTF-8'){
            $chinese = iconv( 'UTF-8', $encoding, $chinese );
        }
        $firstchar = substr( $chinese, 0, 1 );
        $ascii = ord($firstchar);
        if ($ascii<160) { 
            if ($ascii>=48 && $ascii<=57){
                return '~'; 
            }elseif ($ascii>=65 && $ascii<=90){
                return $firstchar; // A--Z
            }elseif ($ascii>=97 && $ascii<=122){
                return strtoupper($firstchar); // a--z
            }else{
                return '~'; //others
            }
        }
        $chinese = substr($chinese, 0, 3);
        if(empty(self::$pinyinmap)){
            $path = 'config/pinyin_map.php';
            self::$pinyinmap = require_once "$path";
        }
        if(is_array(self::$pinyinmap[$chinese])){
            $return = strtoupper(substr(self::$pinyinmap[$chinese][0], 0, 1));
        }else{
            $return = strtoupper(substr(self::$pinyinmap[$chinese], 0, 1));
        }
        if($return) return $return;
        return '~';
    }

    /**
     * 截取字符串
     * 
     * 根据中英文及大小写，对截取出来的长度作了适当的调整
     * @author rollenc
     * @param $str 原字符串
     * @param $len 截取的长度
     * 
     * 
     */
    public static function cutString($str, $len,$dotFlag=true) {
        self::init();
        $strlen = strlen($str);
        
        if($strlen < $len * 0.8) {
            return $str;
        }
        $ignoreTag = array("<br>" => 1, "<hr>" => 1, "<img>" => 1);
        $startTag = array();    //存放开始html标签
        $tmpHtmlTag = '';
        $strlen_utf8 = mb_strlen($str, "UTF-8");
        for($i=0,$l=0;$i<$strlen_utf8 && $l<$len; $i++) {
            $char = mb_substr($str, $i, 1, "UTF-8");
            $nextChar = mb_substr($str, $i+1, 1, "UTF-8");
            $ord = ord($char[0]);
            if (($char == "<" && preg_match("/[a-z]/i", $nextChar))|| $tmpHtmlTag) {//是HTML标签
                $tmpHtmlTag .= $char;
                if ($char == ">") { //标签结束
                    if (strpos($tmpHtmlTag, "</") === 0) {//是闭合标签结束
                        for ($loop = count($startTag)-1; $loop >= 0; $loop--) {
                            //如果开始标签存在此标签，则删除该标签,
                            if ($startTag[$loop] == str_replace("/", "", $tmpHtmlTag)) {
                                $startTag[$loop] = "";
                                break;
                            }
                        }
                    }
                    else {//是开始标签结束 , 去除标签中样式
                        $tmpHtmlTag = preg_replace('/(<)([a-z]+).*?(>)/si', "\\1\\2\\3", $tmpHtmlTag);
                        if ($tmpHtmlTag && !$ignoreTag[strtolower($tmpHtmlTag)]){ //处理标记
                            $startTag[] = $tmpHtmlTag;
                        }
                    }
                    $tmpHtmlTag = "";
                }
            }
            else {
                if('A'  <= $ord && $ord <= "Z") {
                    $l+=1.28;
                }
                elseif($ord > 127) {
                    $l+=2;
                }
                else {
                    $l++;
                }
            }
        }
        $tail = '';
        if($dotFlag && $i< $strlen_utf8) {
            $tail = "...";
            //$i-=2; //应当减2, 但为了不需要修改现有已实现的函数，暂不减
        }
        //补全被截掉的HTML标签
        $str = mb_substr($str, 0, $i, "UTF-8");
        if ($startTag) {
            for ($loop = count($startTag)-1; $loop >= 0; $loop--) {
                if ($startTag[$loop]) {
                    $str .= str_replace("<", "</", $startTag[$loop]);
                }
            }
        }
        return  $str.$tail;
    }

    /**
     * 得到Referer Keyword 来自搜索引擎
     * @return string $keyword 
     */
    public static function getRefererKeyword() {
        $SEARCH_ENGINE = array(
            "www.baidu.com" => "wd", "www.google.cn" => "q",
            "www.google.com" => "q", "www.soso.com" => "w",
            "www.sogou.com" => "query", "www.bing.com" => "q",
            "www.google.com.hk" => "q"
        );
        $refererUrl = parse_url($_SERVER["HTTP_REFERER"]);
        $hostName = trim(strtolower($refererUrl["host"]));
        if ($hostName && $SEARCH_ENGINE[$hostName]) {   //判断是否是来自搜索引擎
            parse_str($refererUrl['query']);
            $keyword = trim(${$SEARCH_ENGINE[$hostName]});
            if ($keyword) {
                $encode = mb_detect_encoding($keyword, "UTF-8,GBK");
                if ($encode != __CHARSET) {
                    $keyword = trim(iconv($encode, __CHARSET, $keyword));
                }
                return $keyword;
            }
        }
        return  NULL;
    }

    //得到Referer keryword 
    public static function getRefKeywordForAds() {
        //$sourceFlag = trim(strtoupper(TrackingFE::getSourceGroup()));
        if ($sourceFlag == false
            && ($refKeywords = self::getRefererKeyword())) {
            $md5Keyworeds = md5("adwords_".$refKeywords);
            self::$saveRefAdsCount = SmarterMemcache::instance()->get($md5Keyworeds);
            if (self::$saveRefAdsCount === false || self::$saveRefAdsCount > 0) {
                return $refKeywords;
            }   
        }
        return NULL;
    }

    /**
     * 取得客户IP
     */
    public static function onlineIP($enableForward = true) {
//      if($_SERVER['HTTP_CLIENT_IP']){
//           $onlineip=$_SERVER['HTTP_CLIENT_IP'];
//      }elseif($_SERVER['HTTP_X_FORWARDED_FOR']){
//           $onlineip=$_SERVER['HTTP_X_FORWARDED_FOR'];
//      }else{
//           $onlineip=$_SERVER['REMOTE_ADDR'];
//      }
//      return $onlineip;
        //check the forward address X-Forwarded-For (this parameter required by Wills(SEM Team))
        if($enableForward && isset($_REQUEST['X-Forwarded-For']) && $_REQUEST['X-Forwarded-For'] !=""){
            $ip = $_REQUEST['X-Forwarded-For'];
        } else {
            //check the remote x client ip address
            if(isset($_SERVER["HTTP_RLNCLIENTIPADDR"]) && $_SERVER["HTTP_RLNCLIENTIPADDR"] !="") {
                $ip = $_SERVER["HTTP_RLNCLIENTIPADDR"];
            } else {
                //set the default client ip
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        return $ip;
    }

    public static function getForwardIP() {
        return (empty($_SERVER['HTTP_X_FORWARDED_FOR']) || $_SERVER['HTTP_X_FORWARDED_FOR']=='unknown') ? '' : $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    public static function getClientIPForGG() {
        if(Utilities::getForwardIP() != '') {
            return Utilities::getForwardIP();
        } else {
            return Utilities::onlineIP();
        }
    }

    /**
     * 取得客户IP
     */
    public static function onlineUserAgent() {
        if(isset($_SERVER["HTTP_USER_AGENT"])) {
            return $_SERVER["HTTP_USER_AGENT"];
        } else {
            return $_ENV["HTTP_USER_AGENT"];
        }
    }
	public static function get_onlineip() {
		$url = 'http://iframe.ip138.com/ic.asp';
		$curl = CURL::getInstance();
		$a = $curl->get_contents($url);
		preg_match('/\[(.*)\]/', $a, $ip);
		return isset($ip[1]) ? $ip[1] : '';
	}
	public static function getCityByIP() {
		$url = 'http://ip.taobao.com/service/getIpInfo.php?ip=';
// 		$ip = Utilities::get_onlineip();
		$ip = Utilities::onlineIP();
		$url .= $ip;
// 		$url .= '222.139.198.22';
		$curl = CURL::getInstance();
		$rs = $curl->get_contents($url);
		$rs = json_decode($rs, true);
		if ($rs && isset($rs['code']) && $rs['code'] == 0) {
			return $rs['data'];
		} else {
			return null;
		}
	}
	public static function unescape($str){
		$ret = '';
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++){
			if ($str[$i] == '%' && $str[$i+1] == 'u'){
				$val = hexdec(substr($str, $i+2, 4));
				if ($val < 0x7f) $ret .= chr($val);
				else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				$i += 5;
			}
			else if ($str[$i] == '%'){
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			}
			else $ret .= $str[$i];
		}
		return $ret;
	}
    /**
     * 取得时间
     */
    function getMicrotime(){ 
        return microtime(TRUE);
    }
    /**
     * @st 开始时间
     * @tm 真人标识 2 未知 1 仿真人 0 真人
     * @lm limit 受限
     */
    function robotCheck() {
        global $_SESSION,$_COOKIE;
        if(!isset($_SESSION['st'])) {
            $_SESSION['st'] = time();  //StartTime开始时间
            $_SESSION['tm'] = 2;   //未知真人
            $_SESSION['pv'] = 1;   //PV计数
            $limitRate = rand(1,1000) / 1000;
            if($limitRate <= __UNKNOWN_BLOCK_RATE) {
                $_SESSION['lm'] = 1;
            } else {
                $_SESSION['lm'] = 0;
            }
        } else {
            //$pv = intval($_COOKIE['TRACKING_PAGE_VISIT_ORDER']);
            if(substr($_SERVER['REQUEST_URI'], 0, 7) != '/async_') {
                $_SESSION['pv']++;   //PV计数
            }
            $pv = intval($_SESSION['pv']);
            $dualTime = (time() - $_SESSION['st']);
            if($pv == 4 && $dualTime >= 30) {
                $_SESSION['tm'] = 1;   //仿真人
                $limitRate = rand(1,1000) / 1000;
                if($limitRate <= __FRAUD_BLOCK_RATE) {
                    $_SESSION['lm'] = 1;
                } else {
                    $_SESSION['lm'] = 0; //不受限
                }
            }elseif($pv >=10 && $_SESSION['tm'] == 2) {
                $_SESSION['tm'] = 1;   //仿真人
                $limitRate = rand(1,1000) / 1000;
                if($limitRate <= __FRAUD_BLOCK_RATE) {
                    $_SESSION['lm'] = 1;
                } else {
                    $_SESSION['lm'] = 0; //不受限
                }
            }
        }
    }
    
    //返回是否机器人比例
    function getRobotLimit() {
        global $_SESSION;
        if(!isset($_SESSION['lm']) || $_SESSION['lm'] !=1) {
            return false;
        } else {
            return true;
        }
    }
    
    function getRobotType() {
        global $_SESSION;
        if($_SESSION['tm']) {
            return $_SESSION['tm'];
        } else {
            return 0;
        }
    }
    public static function get_domain(){
    	/* 协议 */
    	$protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    
    	/* 域名或IP地址 */
    	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
    	{
    		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    	}
    	elseif (isset($_SERVER['HTTP_HOST']))
    	{
    		$host = $_SERVER['HTTP_HOST'];
    	}
    	else
    	{
    		/* 端口 */
    		if (isset($_SERVER['SERVER_PORT']))
    		{
    			$port = ':' . $_SERVER['SERVER_PORT'];
    
    			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
    			{
    				$port = '';
    			}
    		}
    		else
    		{
    			$port = '';
    		}
    
    		if (isset($_SERVER['SERVER_NAME']))
    		{
    			$host = $_SERVER['SERVER_NAME'] . $port;
    		}
    		elseif (isset($_SERVER['SERVER_ADDR']))
    		{
    			$host = $_SERVER['SERVER_ADDR'] . $port;
    		}
    	}
    	return $protocol . $host;
    }
    
    static function Pinyin($_String, $_Code='gb2312')
    {
    	$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
    			"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
    			"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
    			"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
    			"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
    			"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
    			"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
    			"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
    			"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
    			"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
    			"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
    			"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
    			"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
    			"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
    			"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
    			"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
    	$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
    			"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
    			"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
    			"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
    			"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
    			"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
    			"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
    			"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
    			"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
    			"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
    			"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
    			"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
    			"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
    			"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
    			"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
    			"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
    			"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
    			"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
    			"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
    			"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
    			"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
    			"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
    			"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
    			"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
    			"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
    			"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
    			"|-10270|-10262|-10260|-10256|-10254";
    	$_TDataKey = explode('|', $_DataKey);
    	$_TDataValue = explode('|', $_DataValue);
    	$_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : self::_Array_Combine($_TDataKey, $_TDataValue);
    	arsort($_Data);
    	reset($_Data);
    	if($_Code != 'gb2312') $_String = self::_U2_Utf8_Gb($_String);
    	$_Res = '';
    	for($i=0; $i<strlen($_String); $i++)
    	{
    		$_P = ord(substr($_String, $i, 1));
    		if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
    		$_Res .= self::_Pinyin($_P, $_Data);
    }
    return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }
    static function _Pinyin($_Num, $_Data)
    {
    if ($_Num>0 && $_Num<160 ) return chr($_Num);
    elseif($_Num<-20319 || $_Num>-10247) return '';
    else {
    foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
    return $k;
    }
    }
    static function _U2_Utf8_Gb($_C)
    {
    $_String = '';
    if($_C < 0x80) $_String .= $_C;
    elseif($_C < 0x800)
    {
    $_String .= chr(0xC0 | $_C>>6);
    $_String .= chr(0x80 | $_C & 0x3F);
    }elseif($_C < 0x10000){
    $_String .= chr(0xE0 | $_C>>12);
    $_String .= chr(0x80 | $_C>>6 & 0x3F);
    $_String .= chr(0x80 | $_C & 0x3F);
    } elseif($_C < 0x200000) {
    $_String .= chr(0xF0 | $_C>>18);
    $_String .= chr(0x80 | $_C>>12 & 0x3F);
    $_String .= chr(0x80 | $_C>>6 & 0x3F);
    $_String .= chr(0x80 | $_C & 0x3F);
    }
    return iconv('UTF-8', 'GB2312', $_String);
// 	return mb_convert_encoding($_String, 'UTF-8', 'GB2312');
    }
    static function _Array_Combine($_Arr1, $_Arr2)
    {
    for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
    return $_Res;
    }
    /**
     * 获取当前页面完整URL地址
    */
    static function get_url() {
    	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
}
?>