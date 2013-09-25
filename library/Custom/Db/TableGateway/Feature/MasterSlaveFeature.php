<?php
/**
 * MasterSlaveFeature.php
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
 * @version CVS: Id: MasterSlaveFeature.php,v 1.0 2013-9-25 下午8:37:52 Willing Exp
 * @link http://localhost
 * @deprecated File deprecated in Release 3.0.0
 */
namespace Custom\Db\TableGateway\Feature;

use Zend\Db\TableGateway\Feature;

class MasterSlaveFeature extends Feature\MasterSlaveFeature
{
    
    /**
     * preSelect()
     * Replace adapter with slave temporarily
     */
    public function preSelect()
    {
        $this->tableGateway->sql->setAdapter($this->slaveAdapter);
    }

    public function postSelect()
    {
        $this->tableGateway->sql->setAdapter($this->masterAdapter);
    }
}
?>