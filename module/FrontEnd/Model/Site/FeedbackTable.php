<?php
namespace FrontEnd\Model\Site;


use Custom\Paginator\Adapter\DbSelect;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Custom\Db\Sql\Sql;

class FeedbackTable extends TableGateway
{
    protected $table = "feedback";
    protected $select;
    function getOneById($id){
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    function save(Feedback $fed){
        $adv = $fed->toArray();
        unset($adv['inputFilter']);
        if(empty($adv['id'])){
            unset($adv['id']);
            if ($this->insert($adv)) {
                return $this->getLastInsertValue();
            }
        }else{
            $id = $link['id'];
            if($this->getOneById($id)){
                unset($adv['id']);
                unset($adv['add_time']);
                $this->update($adv , array('id' => $id));
            }else{
                throw new \Exception('Not find this id:' . $id);
            }
        }
    }
}