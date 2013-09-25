<?php
/**
 * AbstractCategory.php
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
 * @version CVS: Id: AbstractCategory.php,v 1.0 2013-9-25 下午8:36:16 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Categories\Category;
use Custom\Categories\AbstractContainer;

abstract class AbstractCategory extends AbstractContainer
{
    protected $order;
    protected $active;
    protected $properties;
    protected $parent;
    
    /**
     * 返回类别ID
     * @return string
     */
    abstract function getId();
    
    /**
     * 返回数组形式
     * @return array
     */
    abstract function toArray();
    
    /**
     * 返回排序
     */
    function getOrder(){
        if(isset($this->order)){
            return $this->order;
        }else{
            return null;
        }
    }
    
    /**
     * 返回属性值
     * @param string $property
     * @throws Exception
     * @return mixed
     */
    function get($property){
        if (!is_string($property) || empty($property)){
            throw new Exception('Invalid argument: $property must be a non-empty string');
        }
        
        $method = 'get' . ucwords($property);
        if(method_exists($this, $method)){
            return $this->$method($property);
        }elseif(isset($this->properties[$property])){
            return $this->properties[$property];
        }
        
        return null;
    }
    

    function __get($key){
        return $this->get($key);
    }
    
    /**
     * 返回所有属性值
     * @return mixed
     */
    function getProperties(){
        return $this->properties;
    }

    /**
     * 设置排序
     * @param int $order
     * @throws \Exception
     * @return \Custom\Categories\Category\AbstractCategory
     */
    function setOrder($order = null){
        if (null !== $order && !is_int($order)) {
            throw new \Exception(
                'Invalid argument: $order must be an integer, '
            );
        }
    
        $this->order = $order;
    
        if(isset($this->parent)){
            $this->parent->notifyOrderUpdated();
        }
    
        return $this;
    }
    
    /**
     * 设置属性
     * @param array $properties
     * @return \Custom\Categories\Category\AbstractCategory
     */
    function setProperties(array $properties){
        foreach($properties as $k => $v){
            $this->set($k , $v);
        }
        
        return $this;
    }
    /**
     * 设置属性
     * @param string $key
     * @param mixed $value
     * @throws \Exception
     * @return \Custom\Categories\Category\AbstractCategory
     */
    function set($key , $value){
        if(!is_string($key) || empty($key)){
            throw new \Exception('Invalid argument: $key must be a non-empty string');
        }
        
        $method = 'set' . ucwords($key);
        
        if($method != 'setOptions' && method_exists($this, $method)){
            $this->$key($value);
        }else{
            $this->properties[$key] = $value;
        }
        return $this;
    }
    
    function __set($key , $value){
        $this->set($key , $value);
    }
    
    /**
     * 设置父级分类
     * @param AbstractContainer $parent
     * @throws \Exception
     * @return \Custom\Categories\Category\AbstractCategory
     */
    function setParent(AbstractContainer $parent = null){
        if ($parent === $this) {
            throw new \Exception(
                'A category cannot have itself as a parent'
            );
        }
        
        if($parent === $this->parent){
            return $this;
        }
        
        if(null !== $this->parent){
            $this->parent->removeCate($this);
        }
        
        $this->parent = $parent;
        
        if(null !== $this->parent && !$this->parent->hasCate($this)){
            $this->parent->addCate($this);
        }
        
        return $this;
    }
    
    /**
     * 获取父级分类
     * @return AbstractContainer
     */
    function getParent(){
        return $this->parent;
    }
    
}