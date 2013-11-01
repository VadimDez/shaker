<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:39 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Stuff\Model;

use Zend\Db\TableGateway\TableGateway;

class StuffTable
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

    public function getStuff($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('idStuff' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveStuff(Stuff $stuff)
    {
        $data = array(
            'idStuff'            => $stuff->idStuff,
            'stuffName'          => $stuff->stuffName,
            'stuffMinImageAdress'=> $stuff->stuffMinImageAdress,
            'stuffImageAdress'   => $stuff->stuffImageAdress,
            'stuffDescription'   => $stuff->stuffDescription
        );

        $id = (int)$stuff->idStuff;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getStuff($id)) {
                $this->tableGateway->update($data, array('idStuff' => $id));
            } else {
                throw new \Exception('Stuff id does not exist');
            }
        }
    }

    public function deleteStuff($id)
    {
        $this->tableGateway->delete(array('idStuff' => $id));
    }
}