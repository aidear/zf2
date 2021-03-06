<?php
/**
 * DbSelect.php
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
 * @version CVS: Id: DbSelect.php,v 1.0 2013-10-6 下午10:13:30 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */

namespace Custom\Paginator\Adapter;

use Zend\EventManager\EventManager;

use Zend\Db\TableGateway\Feature\EventFeature;

use Zend\Paginator\Adapter\DbSelect as Father;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class DbSelect extends Father
{
    protected $events;
    
    function __construct($select, $adapterOrSqlObject , ResultSetInterface $resultSetPrototype = null)
    {
        parent::__construct($select, $adapterOrSqlObject , $resultSetPrototype);
        $this->events = new EventFeature(new EventManager('Zend\Db\TableGateway\TableGateway'));
    }
    
    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset           Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $select = clone $this->select;
        $select->offset($offset);
        $select->limit($itemCountPerPage);
        
        $this->events->preSelect($select);
    
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
    
        $resultSet = clone $this->resultSetPrototype;
        $resultSet->initialize($result);
        
        $this->events->postSelect($statement, $result, $resultSet);
    
        return $resultSet;
    }
    
    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        $select = clone $this->select;
        $select->reset(Select::COLUMNS);
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        $select->reset(Select::ORDER);

        $select->columns(array('c' => new Expression('COUNT(1)')));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        
        $this->events->preSelect($select);
        
        $row       = $result->current();
        
        if($result->count() > 1){
            $this->rowCount = $result->count();
        }else{
            $this->rowCount = $row['c'];
        }
        
        $this->events->postSelect($statement, $result, $this->resultSetPrototype);

        return $this->rowCount;
    }
    
}
