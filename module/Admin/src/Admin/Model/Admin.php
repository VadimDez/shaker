<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 2:00 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Sql;

class Admin implements InputFilterAwareInterface
{
    public $idCocktail;
    public $cocktailName;
    public $cocktailDescription;
    public $cocktailImageAdress;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->idCocktail           = (!empty($data['idCocktail'])) ? $data['idCocktail'] : null;
        $this->cocktailName         = (!empty($data['cocktailName'])) ? $data['cocktailName'] : null;
        $this->cocktailImageAdress  = (isset($data['cocktailImageAdress']))  ? $data['cocktailImageAdress'] : null;
        $this->cocktailDescription  = (isset($data['cocktailDescription']))  ? $data['cocktailDescription'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
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

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add(array(
                'name'     => 'idCocktail',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'cocktailName',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'cocktailDescription',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 600,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'cocktailImageAdress',
                    'required' => true,
                ))
            );
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}

class ActionsOnFolder
{
    public function createFolderAndReturnFolderName($name,$path)
    {
        $name = str_replace(' ','_',$name);
        // check if folder exists, and if doesn't - create folder
        $myFolder = $path . $name;
        If(!file_exists($myFolder))
        {
            mkdir($myFolder);
        }
        return $myFolder;
    }

    public function deleteFolder($dirPath)
    {
        // delete folder with FILES in it
        if (! is_dir($dirPath)) {
            throw new \Exception("$dirPath must be a directory");
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}

class LoadMoreData
{
    public function loadDataDynamically($adapter,$limit, $offset,$from)
    {
        // pars.
        $limit = mysql_real_escape_string($limit);
        $offset= mysql_real_escape_string($offset);
        // sql
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from($from);
        $select->limit($limit);
        $select->offset($offset);
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
}