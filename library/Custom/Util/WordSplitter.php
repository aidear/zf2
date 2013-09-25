<?php
/**
 * class.WordSplitter.php
 */
namespace Custom\Util;

class WordSplitter {
    private $mKeywords = false;
    private $mDict = "/config/dba/dict.db";
    private $mDba = false;
    public $mSegment = array();
    
    const __CHAR_ENGLISH = 'ENGLISH';
    const __CHAR_NUM = 'NUM';
    const __CHAR_CHINESE = 'CHINESE';
    const __CHAR_UNKNOWN = 'UNKNOWN';
    const __CHAR_DIGITAL = 'DIGITAL';
    

    private static $selfObj = NULL;
    /**
     * 创建WordSplitter对象
     */
    public static function instance() {
        if(self::$selfObj == NULL) {
            self::$selfObj = new WordSplitter();
        }
        return self::$selfObj;
    }
    function WordSplitter($kw=false) {
        if ($kw) {
            $this->init($kw);
        }
    }
    
    function __destruct() {
        $this->free();
    }

    function init($kw) {
        $this->mKeywords = trim($kw);
        if (!$this->mDba) {
            $dbaPath = APPLICATION_PATH;
            if(DIRECTORY_SEPARATOR == "/") { //linux
                $this->mDba = dba_open($dbaPath . $this->mDict, "r", "db4");
            } else { //windows
                $this->mDba = dba_open($dbaPath . $this->mDict, "rl", "gdbm");
            }
        }
        return ($this->mDba ? true : false);
    }

    function free() {
        @dba_close($this->mDba);
        $this->mKeywords = false;
        $this->mSegment = array();
    }
    
    function wordtype ($word) {
        $ord = ord($word[0]);
        
        //FIXME: Never be __CHAR_DIGITAL
        if ($ord<176){ // not chinese
            if(preg_match("/^[A-Za-z0-9_.-]$/",$word[0])) {
                $type = self::__CHAR_ENGLISH;
            }
            elseif (preg_match("/[0-9]/", $word[0])) {
                $type = self::__CHAR_DIGITAL;
            }
            else {
                $type = self::__CHAR_UNKNOWN;
            }
        }
        else {
            $type = self::__CHAR_CHINESE;
        }
        return $type;
    }

    /**
     * 初步根据关键字中的英文，汉字，中文进行分割
     */
    function segment() {
        $len = mb_strlen($this->mKeywords, "UTF-8"); 
        $word = mb_substr($this->mKeywords, 0, 1, "UTF-8");
        
        $oldtype = self::wordtype($word);
        
        $begin = 0;
        
        for($i=1; $i<$len; $i++) {
            
            $word = mb_substr($this->mKeywords, $i, 1, "UTF-8");
            
            $newType = self::wordtype($word);
            
            if ($newType != $oldtype) {
                $ended = $i;
                $this->mSegment[] = array($oldtype, $begin, $ended);
                $oldtype = $newType;
                $begin = $i;
            }
        }
        
        $ended = $i;
        $this->mSegment[] = array($oldtype, $begin, $ended);
        return true;
    }

    function splitChinese($begin, $end) {
        $result = array();
        $sub = "";
        $word = false;
        $start = $begin - 1; //! there is a $i++ in the for()
        
        for ($i = $begin; $start < $end - 1; $i++) {
            if ($i >= $end) {
                if($word) {
                    $result[] = $word;
                }
                $sub = "";
                $word = false;
                $i = $start;
                continue;
            }
            $sub .= mb_substr($this->mKeywords, $i, 1, 'UTF-8'); 
            
            $value = dba_fetch($sub, $this->mDba);
            switch ($value) {
            case false: //! no found
                if (1 >= mb_strlen($sub, 'UTF-8')) {
                    $word = $sub;
                    $start += 1;
                }
                if($word) {
                    $result[] = $word;
                }
                $sub = "";
                $word = false;
                $i = $start;
                break;
            case 1: //! 001
                if (1 >= mb_strlen($sub, 'UTF-8')) {
                    $word = $sub;
                    $start += 1;
                }
                break;
            case 2: //! 010
                $word = $sub;
                $start = $i;
                $result[] = $word;
                $sub = "";
                $word = false;
                break;
            case 3: //! 011
                $word = $sub;
                $start = $i;
                break;
            case 6: //! 110
                $start = $i;
                $sub = "";
                $word = false;
                break;
            case 7: //! 111
                $start = $i;
                $word = false;
                break;
            default:
                break;
            }
        }
        if($word) {
            $result[] = $word;
        }
        return $result;
        
    }

    function validEnglish($word) {
        if ($value = dba_fetch($word, $this->mDba)) {
            if ($value & 4) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
    }

    function execute($kw) {
        $this->mSegment = array();
        $this->init($kw);
        $result = array();
        if ($this->mKeywords && $this->mDba) {
            $this->segment();
            foreach ($this->mSegment as $rSegment) {
                if (self::__CHAR_DIGITAL == $rSegment[0]) {
                    $word = mb_substr($this->mKeywords, $rSegment[1], $rSegment[2] - $rSegment[1], 'UTF-8');
                    $result[] = $word;
                }
                elseif (self::__CHAR_ENGLISH == $rSegment[0]) {
                    $word = mb_substr($this->mKeywords, $rSegment[1], $rSegment[2] - $rSegment[1], 'UTF-8');
                    if ($this->validEnglish($word)) { //英文停词判断
                        $result[] = $word;
                    }
                }
                elseif (self::__CHAR_CHINESE == $rSegment[0]) {
                    $result = array_merge($result, $this->splitChinese($rSegment[1], $rSegment[2]));
                }
                else { /// __CHAR_SPECIAL
                    ;
                }
            }
        }
        
        //free
        return $result;
    }
} 
