<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 02/11/13
 * Time: 00:45
 */
namespace Cocktail\Model;

use Zend\Db\Sql\Sql;

class Result
{
    public function getQueryResult($adapter,$like,$limit,$offset)
    {
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cocktails');
        $select->where->like('cocktailName','%%' . mysql_real_escape_string($like) . '%%');
        $select->limit(mysql_real_escape_string($limit));
        $select->offset(mysql_real_escape_string($offset));
        $selectString = $sql->getSqlStringForSqlObject($select);
        return $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
}