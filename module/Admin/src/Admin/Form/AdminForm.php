<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 12:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;

class AdminForm extends Form
{

    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('admin');

        // cocktails
        $this->add(array(
            'name' => 'idCocktail',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'cocktailName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'cocktailDescription',
            'options' => array(
                'label' => 'Description',
            ),
        ));

        $this->add(array(
            'name' => 'cocktailImageAdress',
            'type' => 'file',
            'options' => array(
                'label' => 'Foto',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));


        // ingridients
        $this->add(array(
            'name' => 'idIngridient',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'ingridientName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'ingridientDescription',
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'ingridientImage',
            'type' => 'file',
            'options' => array(
                'label' => 'Foto',
            ),
        ));

        // usage
        /*$this->add(array(
            'type' => 'Zend\Form\Element\Number',
            'name' => 'quantity',
            'options' => array(
                'label' => 'Quantity'
            ),
            'attributes' => array(
                'min' => '0',
                'max' => '100000',
                'step'=> '0.1',
            )
        ));*/

        $this->add(array(
            'name' => 'quantity',
            'type' => 'Text',
            'options' => array(
                'label' => 'Amount',
            ),
        ));


    }


}