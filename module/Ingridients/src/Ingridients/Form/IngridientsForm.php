<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:38 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Ingridients\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Db\Adapter\AdapterInterface;

class IngridientsForm extends Form
{
    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        // we want to ignore the name passed
        parent::__construct('ingridients');

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
            'name' => 'ingridientImageAdress',
            'type' => 'file',
            'options' => array(
                'label' => 'Photo',
            ),
        ));

        $this->add(array(
            'name' => 'ingridientMinImageAdress',
            'type' => 'file',
            'options' => array(
                'label' => 'Min Photo',
            ),
        ));

        $this->add(array(
            'name'    => 'idCategory',
            'type'    => 'Zend\Form\Element\Select',
            'options' => array(
                'label'         => 'Category',
                'value_options' => $this->getOptionsForSelect(),
                'empty_option'  => '--- please choose ---'
            )
        ));

    }

    public function getOptionsForSelect()
    {
        $dbAdapter = $this->dbAdapter;
        $sql       = 'SELECT idCategory, categoryName FROM categories ORDER BY categoryName ASC';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['idCategory']] = $res['categoryName'];
        }

        return $selectData;
    }
}