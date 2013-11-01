<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 11:54 AM
 * To change this template use File | Settings | File Templates.
 */


namespace Cocktail\Form;

use Zend\Form\Form;

class CocktailForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('cocktail');

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
            'name' => 'cocktailImage',
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
    }
}