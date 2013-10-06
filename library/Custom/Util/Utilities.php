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
}
?>