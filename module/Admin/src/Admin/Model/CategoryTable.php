<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/20/13
 * Time: 6:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class CategoryTable
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

    public function getCategory($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('idCategory' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCategory(Category $category)
    {
        $data = array(
            'idCategory'            => $category->idCategory,
            'categoryName'          => $category->categoryName
        );
        $id = $category->idCategory;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCategory($id)) {
                $this->tableGateway->update($data, array('idCategory' => $id));
            } else {
                throw new \Exception('Admin id does not exist');
            }
        }

    }

    public function deleteCategory($id)
    {
        $this->tableGateway->delete(array('idCategory' => $id));
    }
}