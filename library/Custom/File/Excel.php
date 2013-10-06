<?php
/**
 * Excel.php
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
 * @version CVS: Id: Excel.php,v 1.0 2013-10-6 下午10:07:17 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
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