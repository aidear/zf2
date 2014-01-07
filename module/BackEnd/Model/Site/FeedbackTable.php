<?php
namespace BackEnd\Model\Site;


use Custom\Paginator\Adapter\DbSelect;
use Zend\Db\TableGateway\TableGateway;
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
            $id = $adv['id'];
            if($this->getOneById($id)){
                unset($adv['id']);
                unset($adv['add_time']);
                $this->update($adv , array('id' => $id));
            }else{
                throw new \Exception('Not find this id:' . $id);
            }
        }
    }
    function formatWhere(array $data){
        $where = $this->_getSelect()->where;
        if(!empty($data['title'])){
            //             $where->like('title', '%' . $data['title'] . '%')->orPredicate(new Like('Url', '%' . $data['title'] . '%'));
        }
        //         if(!empty($data['cid'])){
        //             $where->equalTo('link.category', $data['cid']);
        //         }
    
        $this->select->where($where);
        return $this;
    }
    public function getListToPaginator($order = array())
    {
        $select = $this->_getSelect();
        if (!empty($order)) {
            $select->order($order);
        }//echo str_replace('"', '', $select->getSqlString());die;
    
        return new DbSelect($select, $this->getAdapter());
    }
    protected function _getSelect(){
        if(!isset($this->select)){
            $this->select = $this->getSql()->select();
        }
        return $this->select;
    }
}