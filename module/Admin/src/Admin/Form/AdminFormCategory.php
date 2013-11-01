<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/20/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Db\Adapter\AdapterInterface;

class AdminFormCategory extends Form
{


    public function __construct()
    {

        // we want to ignore the name passed
        parent::__construct('admin');


        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));

        // category for ingridients
        $this->add(array(
            'name' => 'idCategory',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'categoryName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));
    }




}