<?php
namespace BackEnd\Model\Site;

use Custom\Paginator\Adapter\DbSelect;
use Zend\Db\TableGateway\TableGateway;
class AdvApplyTable extends TableGateway
{
    protected $table = "adv_apply";
    protected $select;
    function getOneById($id){
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    function deleteMuti($where) {
        return parent::delete($where);
    }
    function save(AdvApply $apply){
        $adv = $apply->toArray();
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
        if(!empty($data['k'])){
            $where->like('title',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('url',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('QQ',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('email',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('tel',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('position',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('build_time',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('dailyView',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('summary',  '%' . $data['k'] . '%');
            $where->or;
            $where->like('ip',  '%' . $data['k'] . '%');
        }
    
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