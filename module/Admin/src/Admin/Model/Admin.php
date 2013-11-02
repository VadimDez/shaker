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