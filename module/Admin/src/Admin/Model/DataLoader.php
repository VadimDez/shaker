<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 02/11/13
 * Time: 00:29
 */
namespace Admin\Model;

use Zend\Db\Sql\Sql;

class DataLoader
{
    public function loadDataDynamically($adapter,$limit, $offset,$from)
    {
        // sql
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($from);
        $select->limit(mysql_real_escape_string($limit));
        $select->offset(mysql_real_escape_string($offset));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

        $records = array();
        foreach($results as $res)
        {
            $records[] = $res;
        }
        $results = array(
            'return1' => $records
        );
        return $results;
    }

    public function returnRecords($adapter,$id,$mySql)
    {
        $resultQuery = $adapter->query($mySql)->execute(array($id));
        $records = array();
        foreach($resultQuery as $res)
        {
            $records[] = $res;
        }
        return $records;
    }
}