<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:39 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Ingridients\Model;

use Zend\Db\TableGateway\TableGateway;

class IngridientsTable
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

    public function getIngridients($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('idIngridient' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveIngridients(Ingridients $ingridients)
    {
        $data = array(
            'idIngridient'            => $ingridients->idIngridient,
            'ingridientName'          => $ingridients->ingridientName,
            'ingridientMinImageAdress'=> $ingridients->ingridientMinImageAdress,
            'ingridientImageAdress'   => $ingridients->ingridientImageAdress,
            'ingridientDescription'   => $ingridients->ingridientDescription,
            'idCategory'              => $ingridients->idCategory
        );

        $id = (int)$ingridients->idIngridient;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getIngridients($id)) {
                $this->tableGateway->update($data, array('idIngridient' => $id));
            } else {
                throw new \Exception('Ingridients id does not exist');
            }
        }
    }

    public function deleteIngridients($id)
    {
        $this->tableGateway->delete(array('idIngridient' => $id));
    }
}