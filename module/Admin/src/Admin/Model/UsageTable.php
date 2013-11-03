<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/14/13
 * Time: 9:49 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Model;

class UsageTable extends FatherTable
{


    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
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
}