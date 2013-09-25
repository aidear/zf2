<?php
/**
 * class.CsvAPI.php
 *-------------------------
 *
 * cvs file import function
 *
 * PHP versions 5
 *
 * LICENSE: This source file is from Smarter Ver2.0, which is a comprehensive shopping engine 
 * that helps consumers to make smarter buying decisions online. We empower consumers to compare 
 * the attributes of over one million products in the computer and consumer electronics categories
 * and to read user product reviews in order to make informed purchase decisions. Consumers can then 
 * research the latest promotional and pricing information on products listed at a wide selection of 
 * online merchants, and read user reviews on those merchants.
 * The copyrights is reserved by http://www.mezimedia.com.  
 * Copyright (c) 2005, Mezimedia. All rights reserved.
 *
 * @author     Fan Xu <fan_xu@mezimedia.com>
 * @copyright  (C) 2004-2006 Mezimedia.com
 * @license    http://www.mezimedia.com  PHP License 5.0
 * @version    CVS: $Id: Csv.php,v 1.1 2013/04/15 10:56:30 rock Exp $
 * @link       http://www.smarter.com/
 * @deprecated File deprecated in Release 2.0.0
 */
namespace Custom\File;

use Exception;

class Csv {
    public $localEncoding = 'UTF-8';
    public $seperator = ",";
    public $enclosure = "\"";
    protected $topLineNum = 2;
    protected $maxLineNum = 1000000;
    protected $maxLineSize = 1048576;
    
    
    /**
     * 将CSV文件的内容转换到数据
     * @param $maxLineNum 取CSV文件中的行数,-1为不限.但不能超过$this->maxLineNum
     * 说明: 返回值数据类型: $result[$rowNo]['$colKey']
     *      若$hasHeadFlag==false 则 $rowNo=0相对于第一行,且$colKey对应列号.
     *      若$hasHeadFlag==true 则 $rowNo=0相对于第二行,且$colKey,对应为第一列的值.
     */
    public function loadToArray($pathfile, $hasHeadFlag=false, $maxLineNum=-1) {
        if($maxLineNum < 0) {
            $maxLineNum = $this->maxLineNum;
        }
        $handle = @fopen($pathfile, "r");
        if($handle == false) {
            return NULL;
            //throw new Exception("can not open [$pathfile](r).");
        }
        $result = array();
        $suffix = 0;
        
        for( $loopTop=0; $loopTop<$maxLineNum && !feof($handle) ; $loopTop++) {
            if(!empty($this->enclosure)) {
                $arr = $this->__fgetcsv($handle, $this->maxLineSize, $this->seperator, $this->enclosure);
            } else {
                $arr = $this->__fgetcsv($handle, $this->maxLineSize, $this->seperator);
            }
            if($arr == false) {
                break;
            }
            
            //utf8 => shift-jis
            for($loop=0, $count=count($arr); $arr && $loop<$count; $loop++) {
                $arr[$loop] = $this->decode($arr[$loop]);
            }
            if($hasHeadFlag) {
                if($loopTop==0) { //第一行,取HEAD
                    $head = $arr;
                } else {
                    //转换KEY为HEAD
                    for($loop=0; $arr && $loop<count($arr); $loop++) {
                        $result[$loopTop-1][$head[$loop]] = $arr[$loop];
                    }
                }
            } else {
                $result[$loopTop] = $arr;
            }
        }
        fclose ($handle);
        return $result;
    }
    
    /**
     * 将CSV文件的内容转换到数据
     * @param $maxLineNum 取CSV文件中的行数,-1为不限.但不能超过$this->maxLineNum
     * 
     * 可能是坏，不能使用的，暂没有用到过
     * 
     * 说明: 返回值数据类型: $result[$rowNo]['$colKey']
     *      若$hasHeadFlag==false 则 $rowNo=0相对于第一行,且$colKey对应列号.
     *      若$hasHeadFlag==true 则 $rowNo=0相对于第二行,且$colKey,对应为第一列的值.
     */
    public function loadStringToArray($str, $hasHeadFlag=false, $maxLineNum=-1) {
        if($maxLineNum < 0) {
            $maxLineNum = min($this->maxLineNum, strlen($str));
        }
        $result = array();
        $suffix = 0;
        for( $loopTop=0; $loopTop<$maxLineNum ; $loopTop++) {
            $arr = __fgetcsv($handle, $this->maxLineSize, $this->seperator, $this->enclosure);
            //utf8 => shift-jis
            for($loop=0; $arr && $loop<count($arr); $loop++) {
                $arr[$loop] = $this->decode($arr[$loop]);
            }
            if($hasHeadFlag) {
                if($loopTop==0) { //第一行,取HEAD
                    $head = $arr;
                } else {
                    //转换KEY为HEAD
                    for($loop=0; $arr && $loop<count($arr); $loop++) {
                        $result[$loopTop-1][$head[$loop]] = $arr[$loop];
                    }
                }
            } else {
                $result[$loopTop] = $arr;
            }
        }
        fclose ($handle);
        return $result;
    }
    
    /**
     * 将数据(二维数组)转换到CSV文件的内容
     * 说明: 返回值数据类型: void
     *      若$hasHeadFlag==false 则 $rowNo=0相对于第一行,且$colKey对应列号.
     *      若$hasHeadFlag==true 则 $rowNo=0相对于第二行,且$colKey,对应为第一列的值.
     */
    public function storeFromArray($pathfile, $result, $hasHeadFlag=false) {
        $handle = @fopen($pathfile, "a");
        if($handle == false) {
            throw new Exception("can not open [$pathfile](w).");
        }
        chmod($pathfile, 0666);
        if($result == NULL || !is_array($result)) {
            fclose($handle);
            return;
        }
        $output = '';
        if($hasHeadFlag == false) {
            for($loop=0; $loop<count($result); $loop++) {
                $line = $this->formatLine($result[$loop]);
                $output .= $line;
            }
        } else if(count($result) > 0) {
            //第一行是表头
            $head = array();
            foreach($result[0] as $key => $val) {
                $head[] = $key;
            }
            $line = $this->formatLine($head);
            $output .= $line;
            //fwrite($handle, $line);
            //其它行
            
            for($loop=0; $loop<count($result); $loop++) {
                $arr = array();
                foreach($result[$loop] as $val) { //convert Array
                    $arr[] = $val;
                }
                $line = $this->formatLine($arr);
                $output .= $line;
            }
            
        }
        fwrite($handle, $output);
        fclose ($handle);
    }
    
    public function storeStringFromArray($result, $hasHeadFlag=false) {
        $str = "";
        if($hasHeadFlag == false) {
            for($loop=0; $loop<count($result); $loop++) {
                $str .= $this->formatLine($result[$loop]);
            }
        } else if(count($result) > 0) {
            //第一行是表头
            $head = array();
            foreach($result[0] as $key => $val) {
                $head[] = $key;
            }
            $str .= $this->formatLine($head);
            //其它行
            for($loop=0; $loop<count($result); $loop++) {
                $arr = array();
                foreach($result[$loop] as $val) { //convert Array
                    $arr[] = $val;
                }
                $str .= $this->formatLine($arr);
            }
        }
        return $str;
    }
    
    /**
     * 按行读取CSV文件
     */
    public function &streamRead($fp, $length = NULL, $delimiter = ',', $enclosure = '"') {
        
        $result = false;
        if(feof($fp)) {
            return $result;
        }

        $arr = self::__fgetcsv($fp, $length, $delimiter, $enclosure);
        if($arr == false) {
            return $result;
        }
        //utf8 => shift-jis
        for($loop=0; $arr && $loop<count($arr); $loop++) {
            $arr[$loop] = $this->decode($arr[$loop]);
        }
        return $arr;
    }

    protected function &parseLine(&$line, $sortStyle="") {
        $inEnclosure = false;
        $cnt = strlen($line);
        $start = 0;
        $arr = array();
        if(is_array($sortStyle)) {
            for($loop=0; $loop<$sortStyle['count']; $loop++) {
                $arr[$loop] = "";
            }
            $overOffset = $sortStyle['count'];
        } else {
        }
        //line seperator
        for($loop=$cnt-1; $loop>=0; $loop--) {
            if($line[$loop] == "\r" || $line[$loop] == "\n") {
                $cnt--;
            } else {
                break;
            }
        }
        for($loop=0, $suffix=0; $loop<$cnt; $loop++) {
            if($this->enclosure != "" && $line[$loop] == $this->enclosure) {
                if(($loop == 0) || ($loop > 0 && $line[$loop-1] != '\\')) {
                    if($inEnclosure) {
                        $inEnclosure = false;
                    } else {
                        $inEnclosure = true;
                        $start = $loop + 1;
                    }
                }
            }
            if(!$inEnclosure && $line[$loop] == $this->seperator) {
                $end = $loop - strlen($this->enclosure);
                $val = $this->decode(substr($line, $start, $end - $start));
                $col = substr("00", strlen($suffix+1)) . ($suffix+1);
                if(is_array($sortStyle)) { //sort
                    //$key is current column num; $sortStyle is mapping the column actual postion
                    $key = (string)($suffix + 1);
                    if(isset($sortStyle[$key])) {
                        $arr[$sortStyle[$key]]['val'] = $val;
                        $arr[$sortStyle[$key]]['col'] = $col;
                    } else {
                        $arr[$overOffset]['val'] = $val; //$overOffset is the out of mapping offset
                        $arr[$overOffset]['col'] = $col;
                        $overOffset++;
                    }
                } else { //not sort
//del 1/16/2006                 $arr[$suffix] = $val;
                    $arr[$suffix]['val'] = $val;
                    $arr[$suffix]['col'] = $col;
                }
                $suffix++;
                $start = $loop + 1;
            }
        }
        if(!$inEnclosure) { //last attribute
            $end = $loop - strlen($this->enclosure);
            $val = $this->decode(substr($line, $start, $end - $start));
            $col = substr("00", strlen($suffix+1)) . ($suffix+1);
            if(is_array($sortStyle)) { //sort
                $key = (string)($suffix + 1);
                if(isset($sortStyle[$key])) {
                    $arr[$sortStyle[$key]]['val'] = $val;
                    $arr[$sortStyle[$key]]['col'] = $col;
                } else {
                    $arr[$overOffset]['val'] = $val;
                    $arr[$overOffset]['col'] = $col;
                    $overOffset++;
                }
            } else { //not sort
//del 1/16/2006             $arr[$suffix] = $val;
                $arr[$suffix]['val'] = $val;
                $arr[$suffix]['col'] = $col;
                $suffix++;
            }
        }
        return $arr;
    }
    
    protected function formatLine($arr) {
        $line = "";
        foreach($arr as $val) {
            if($line != "") {
                $line .= $this->seperator;
            }
            $line .= $this->enclosure . $this->encode($val) . $this->enclosure;
        }
        $line .= "\r\n";
        return $line;
    }
    
    public static function __set_csv($arr, $seperator=',', $enclosure='"' ) {
        $line = "";
        foreach($arr as $val) {
            if($line != "") {
                $line .= $seperator;
            }
            
            $line .= $enclosure . self::__encode($val, $seperator, $enclosure) . $enclosure;
        }
        $line .= "\r\n";
        return $line;
    }
    
    protected static function &__encode(&$str, $seperator, $enclosure) {
        $str = str_replace($enclosure, $enclosure.$enclosure, $str);
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("\r", "\\r", $str);
        $str = str_replace("\n", "\\n", $str);
//      $str = iconv("GB2312", "UTF-8//IGNORE", $str);

        return $str;
    }

    protected function &keyToIndexArray($keyArr, $filter = "") {
        $arr = array();
        if(!is_array($keyArr)) {
            return $arr;
        }
        $suffix = 0;
        foreach($keyArr as $key => $value) {
            if(isset($filter[$key])) {
                continue;
            }
            $arr[$suffix++] = $key;
        }
        sort($arr);
        return $arr;
    }

    protected function &encode(&$str) {
        $str = str_replace($this->enclosure, $this->enclosure.$this->enclosure, $str);
        $str = str_replace("\\", "\\\\", $str);
        $str = str_replace("\r", "\\r", $str);
        $str = str_replace("\n", "\\n", $str);
        if($this->localEncoding == NULL) {
            return $str;
        }
        if($this->localEncoding != "UTF-8") {
            $str = iconv($this->localEncoding, "UTF-8//IGNORE", $str);
        }

        return $str;
    }

    protected function &decode(&$str) {
        $str = str_replace($this->enclosure.$this->enclosure, $this->enclosure, $str);
        $str = str_replace("\\\\", "\\", $str);
        $str = str_replace("\\r", "\r", $str);
        $str = str_replace("\\n", "\n", $str);
        if($this->localEncoding == NULL) {
            return $str;
        }
        if($this->localEncoding != "UTF-8//IGNORE") {
            $str = iconv("UTF-8", $this->localEncoding."//IGNORE", $str);
        }
        

        return $str;
    }
    
    /* method for Crawled csv data */
    /* add by brand */ //edit by cooc 070103
    public static function __fgetcsv($handle, $length = NULL, $delimiter = ',', $enclosure = '"') {
        for($loopTop = 0; $loopTop<1000; $loopTop++) {
            if(feof($handle)) {
                return false;
            }
            if(is_null($length)) {
                $buffer = fgets($handle);
            } else {
                $buffer = fgets($handle, $length);  
            }
            $buffer = trim($buffer);
            if($buffer == "") {
                continue; //忽略空行
            }
            
            //U8-DOS以0xFFFE开头
            $ch = substr($buffer, 0, 1);
            if(ord($ch) == 0xEF) {
                if(ord(substr($buffer, 1, 1)) == 0xBB
                    && ord(substr($buffer, 2, 1)) == 0xBF) {
                    $buffer = substr($buffer, 3); //跳前三字符
                    $ch = substr($buffer, 0, 1);
                }
            }
            
            if(substr($buffer, 0, 1) == $enclosure) {
                if(substr($buffer, -1) == ',') {
                    $buffer = substr($buffer, 1, -2);
                } else {
                    $buffer = substr($buffer, 1, -1);
                }
                $aBuffer = explode($enclosure.$delimiter.$enclosure, $buffer);
            } else {
                $aBuffer = explode($delimiter, $buffer);
            }
            for($i=0; $i<count($aBuffer); $i++) {
                $aBuffer[$i] = trim($aBuffer[$i], '"');
                $aBuffer[$i] = str_replace('""', '"', $aBuffer[$i]);
            }
            return $aBuffer;
        }
    }
}
?>