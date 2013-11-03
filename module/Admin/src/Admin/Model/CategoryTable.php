<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/20/13
 * Time: 6:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Model;

class CategoryTable extends FatherTable
{

    public function saveCategory(Category $category)
    {
        $data = array(
            'idCategory'            => $category->idCategory,
            'categoryName'          => $category->categoryName
        );
        $id = (int)$category->idCategory;
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
}