<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:39 PM
 * To change this template use File | Settings | File Templates.
 */



namespace Stuff\Model;
// Add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Stuff implements InputFilterAwareInterface
{
    public $idStuff;
    public $stuffName;
    public $stuffDescription;
    public $stuffImageAdress;
    public $stuffMinImageAdress;
    protected $inputFilter;                       // <-- Add this variable


    public function exchangeArray($data)
    {
        $this->idStuff                 = (!empty($data['idStuff'])) ? $data['idStuff'] : null;
        $this->stuffName               = (!empty($data['stuffName'])) ? $data['stuffName'] : null;
        $this->stuffMinImageAdress        = (isset($data['stuffMinImageAdress']))  ? $data['stuffMinImageAdress'] : null;
        $this->stuffImageAdress        = (isset($data['stuffImageAdress']))  ? $data['stuffImageAdress'] : null;
        $this->stuffDescription        = (isset($data['stuffDescription']))  ? $data['stuffDescription'] : null;
    }

    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
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
                'name'     => 'idStuff',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));


            $inputFilter->add(array(
                'name'     => 'stuffName',
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
                'name'     => 'stuffDescription',
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
                    'name'     => 'stuffImageAdress',
                    'required' => true
                ))
            );

            $inputFilter->add(
                $factory->createInput(array(
                    'name'     => 'stuffMinImageAdress',
                    'required' => true
                ))
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}