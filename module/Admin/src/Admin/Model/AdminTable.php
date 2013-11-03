<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 12:21 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Model;


class AdminTable extends FatherTable
{

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
            if ($this->getRecord('idCocktail',$id)) {
                $this->tableGateway->update($data, array('idCocktail' => $id));
            } else {
                throw new \Exception('Admin id does not exist');
            }
        }
    }
}