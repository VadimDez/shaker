<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 03/11/13
 * Time: 22:32
 */
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class FatherTable
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

    public function getRecord($key, $id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array($key => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }


    public function deleteRecord($key,$id)
    {
        $this->tableGateway->delete(array($key => $id));
    }
}