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
use Zend\Db\Adapter\AdapterInterface;

class AdminFormIngridients extends Form
{
    protected $dbAdapter;

    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        // we want to ignore the name passed
        parent::__construct('admin');

        $this->add(array(
            'name' => 'idCocktail',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));

        $this->add(array(
            'name' => 'ingridientName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
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
                'label' => 'Quantity',
            ),
        ));

        $this->add(array(
            'name'    => 'idIngridient',
            'type'    => 'Zend\Form\Element\Select',
            'options' => array(
                'label'         => 'Ingridient',
                'value_options' => $this->getOptionsForSelect(),
                'empty_option'  => '--- please choose ---'
            )
        ));

    }

    public function getOptionsForSelect()
    {
        $dbAdapter = $this->dbAdapter;
        $sql       = 'SELECT idIngridient, ingridientName FROM ingridients ORDER BY ingridientName ASC';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['idIngridient']] = $res['ingridientName'];
        }

        return $selectData;
    }



}