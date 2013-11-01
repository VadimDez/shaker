<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:38 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Stuff\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Db\Adapter\AdapterInterface;

class StuffForm extends Form
{
    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        // we want to ignore the name passed
        parent::__construct('stuff');

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));


        // stuff
        $this->add(array(
            'name' => 'idStuff',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'stuffName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'stuffDescription',
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'stuffImageAdress',
            'type' => 'file',
            'options' => array(
                'label' => 'Photo',
            ),
        ));

        $this->add(array(
            'name' => 'stuffMinImageAdress',
            'type' => 'file',
            'options' => array(
                'label' => 'Min Photo',
            ),
        ));


    }

}