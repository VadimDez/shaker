<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 12:21 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class AdminTable
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

    public function getCocktail($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('idCocktail' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCocktail(Admin $admin)
    {
        $data = array(
            'idCocktail'          => $admin->idCocktail,
            'cocktailName'          => $admin->cocktailName,
            'cocktailImageAdress'   => $admin->cocktailImageAdress,
            'cocktailDescription'   => $admin->cocktailDescription,
        );

        $id = (int)$admin->idCocktail;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCocktail($id)) {
                $this->tableGateway->update($data, array('idCocktail' => $id));
            } else {
                throw new \Exception('Admin id does not exist');
            }
        }
    }

    public function deleteCocktail($id)
    {
        $this->tableGateway->delete(array('idCocktail' => $id));
    }
}