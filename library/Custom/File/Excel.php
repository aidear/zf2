<?php
/*
 * package_name : Excel.php
 * ------------------
 * typecomment
 *
 * PHP versions 5
 * 
 * @Author   : thomas(thomas_fu@mezimedia.com)
 * @Copyright: Copyright (c) 2004-2011 Mezimedia Com. (http://www.mezimedia.com)
 * @license  : http://www.mezimedia.com/license/
 * @Version  : CVS: $Id: Excel.php,v 1.1 2013/04/15 10:56:30 rock Exp $
 */
namespace Custom\ReadFile;

class Execel implements SeekableIterator
{
    /**
     * 文件句柄
     */
    private $fp;
    
    /**
     * Execel当前活跃Sheet
     */
    private $activeSheetIndex;
    
    /**
     * 当前文件索引行数 
     */
    private $currentIndex = 0;
    
    private $options = array(
        'totalColNum' => null,
        'totalRowNum' => null,
    );
    
    public function __construct($fileName) 
    {
        if (empty($fileName)) {
            throw new ParseFileException('can not open file '.$fileName);
        }
        $this->fp = PHPExcel_IOFactory::load($fileName);
        $this->activeSheetIndex = $this->fp->getActiveSheet();
        $this->setOption('totalRowNum', $this->activeSheetIndex->getHighestRow());
        $this->setOption('totalColNum', $this->activeSheetIndex()->getHighestColumn());
    }
    
    /**
     * 设置Excel属性值
     */
    public function setOption($name, $value) 
    {
        $this->options[$name] = $value;
    }
    
    public function getOption($name) 
    {
        return $this->options[$name] ?: null;
    }
    
    public function current() 
    {
        $rowDate = array();
        for ($loop = 0; $loop <= $this->getOption('totalColNum'); $loop++) {
            $rowDate[$loop] = $this->activeSheetIndex->getCellByColumnAndRow($loop, $this->currentIndex + 1)->getValue();
        }
        return $rowDate;
    }
    
    public function key() 
    {
        return $this->currentIndex;
    }
    
    public function next() 
    {
        $this->currentIndex++;
    }
    
    public function rewind() 
    {
        $this->currentIndex = 0;
    }
    
    public function valid() 
    {
        return $this->currentIndex < $this->getOption('totalRolNum');
    }
    
    public function seek($index) 
    {
        if ($index > 0 && $index < $this->getOption('totalRolNum')) {
            $this->currentIndex = $index;
        } else {
            throw new OutOfBoundsException("Illegal index '$index'");
        }
    }
}
?>