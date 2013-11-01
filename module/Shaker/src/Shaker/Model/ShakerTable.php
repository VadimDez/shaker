<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/21/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Shaker\Model;

use Zend\Db\TableGateway\TableGateway;

class ShakerTable
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

    public function getShaker($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveShaker(Shaker $shaker)
    {
        $data = array(
            'artist' => $shaker->artist,
            'title'  => $shaker->title,
        );

        $id = (int)$shaker->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getShaker($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Shaker id does not exist');
            }
        }
    }

    public function deleteShaker($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}