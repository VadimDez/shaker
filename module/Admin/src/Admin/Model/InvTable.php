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

class InvTable
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

    public function getInv($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('idInvUsage' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveInv(Inv $inv)
    {
        $data = array(
            'idInvUsage'       => $inv->idInvUsage,
            'idCocktail'       => $inv->idCocktail,
            'idStuff'          => $inv->idStuff
        );
        $id = (int)$inv->idInvUsage;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('idInvUsage' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteInv($id)
    {
        $this->tableGateway->delete(array('idInvUsage' => $id));
    }
}