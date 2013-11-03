<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/14/13
 * Time: 9:49 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Model;

class InvTable extends FatherTable
{
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
            if ($this->getInv($id)) {
                $this->tableGateway->update($data, array('idInvUsage' => $id));
            } else {
                throw new \Exception('Stuff id does not exist');
            }
        }
    }
}