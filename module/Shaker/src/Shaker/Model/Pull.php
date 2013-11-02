<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 02/11/13
 * Time: 01:11
 */
namespace Shaker\Model;

class Pull
{
    public function getIngridients() // <-- Add variable to this
    {

    }

    public function getCocktailsByIngridient($ingridient, $adapter)
    {
        $ingridient = str_replace(' ',"",$ingridient);
        //$temp = str_replace(',',"' AND i.ingridientName = '",$ingridient);
        $temp = str_replace(',',"','",$ingridient);
        $mySql =   "SELECT *
                    FROM used as u, cocktails as c, ingridients as i
                    WHERE i.idIngridient = u.idIngridient AND u.idCocktail = c.idCocktail AND ingridientName IN ('$temp')
                    GROUP BY c.idCocktail";

        $resultQuery = $adapter->query($mySql)->execute();
        $records = array();
        foreach($resultQuery as $res)
        {
            $records[] = $res;
        }
        return $records;
    }
}