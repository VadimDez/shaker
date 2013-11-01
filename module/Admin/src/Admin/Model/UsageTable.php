<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/14/13
 * Time: 9:49 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class UsageTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getUsage($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveUsage(Usage $usage)
    {
        $data = array(
            'idCocktail'            => $usage->idCocktail,
            'idIngridient'          => $usage->idIngridient,
            'quantity'              => $usage->quantity,
        );

        $this->tableGateway->insert($data);

    }

    public function deleteUsage($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}