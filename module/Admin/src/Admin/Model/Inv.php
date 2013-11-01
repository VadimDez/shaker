<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/14/13
 * Time: 9:48 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Model;
// Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Inv implements InputFilterAwareInterface
{
    public $idInvUsage;
    public $idCocktail;
    public $idStuff;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->idInvUsage      = (!empty($data['idInvUsage'])) ? $data['idInvUsage'] : null;
        $this->idCocktail      = (!empty($data['idCocktail'])) ? $data['idCocktail'] : null;
        $this->idStuff         = (!empty($data['idStuff'])) ? $data['idStuff'] : null;
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
            $inputFilter->add(array(
                'name'     => 'idInvUsage',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'idCocktail',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'idStuff',
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