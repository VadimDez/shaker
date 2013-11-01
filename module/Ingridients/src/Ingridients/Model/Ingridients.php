<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:39 PM
 * To change this template use File | Settings | File Templates.
 */



namespace Ingridients\Model;
// Add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Ingridients implements InputFilterAwareInterface
{
    public $idIngridient;
    public $ingridientName;
    public $ingridientDescription;
    public $ingridientImage;
    public $ingridientMinImageAdress;
    public $ingridientImageAdress;
    public $idCategory;
    protected $inputFilter;                       // <-- Add this variable


    public function exchangeArray($data)
    {
        $this->idIngridient                 = (!empty($data['idIngridient'])) ? $data['idIngridient'] : null;
        $this->ingridientName               = (!empty($data['ingridientName'])) ? $data['ingridientName'] : null;
        $this->ingridientMinImageAdress        = (isset($data['ingridientMinImageAdress']))  ? $data['ingridientMinImageAdress'] : null;
        $this->ingridientImageAdress        = (isset($data['ingridientImageAdress']))  ? $data['ingridientImageAdress'] : null;
        $this->ingridientDescription        = (isset($data['ingridientDescription']))  ? $data['ingridientDescription'] : null;
        $this->idCategory                   = (isset($data['idCategory']))  ? $data['idCategory'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function orderIngridientsByCategory($ingridients, $categories)
    {

        $byIngridients = array();

        foreach ($ingridients as $ingridient)
        {
            $byIngridients[] = array(
                'idIngridient'           => $ingridient->idIngridient,
                'ingridientName'         => $ingridient->ingridientName,
                'ingridientMinImageAdress'  => $ingridient->ingridientMinImageAdress,
                'ingridientImageAdress'  => $ingridient->ingridientImageAdress,
                'ingridientDescription'  => $ingridient->ingridientDescription,
                'idCategory'             => $ingridient->idCategory,
                'categoryName'           => $categories[$ingridient->idCategory]
            );

        }



        // group to categories
        $arr = array();
        foreach($byIngridients as $key => $item)
        {
            $arr[$item['categoryName']][$key] = $item;

        }

        return $arr;
    }

    public function getCategoriesName($adapter)
    {
        $mySql = "SELECT idCategory, categoryName FROM categories";
        $resultQuery = $adapter->query($mySql)->execute();
        $records = array();
        foreach($resultQuery as $res)
        {
            $records[$res['idCategory']] = $res['categoryName'];
        }
//        var_dump($records);
//        die();
        return $records;
    }

    // Add content to these methods:
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
                'name'     => 'idIngridient',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));


            $inputFilter->add(array(
                'name'     => 'ingridientName',
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
                'name'     => 'ingridientDescription',
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
                    'name'     => 'ingridientImageAdress',
                    'required' => true
                ))
            );

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'ingridientMinImageAdress',
                    'required' => true
                ))
            );

            $inputFilter->add(array(
                'name'     => 'idCategory',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}