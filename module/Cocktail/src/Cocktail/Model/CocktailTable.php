<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 11:55 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Cocktail\Model;

use Zend\Db\TableGateway\TableGateway;

class CocktailTable
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

}