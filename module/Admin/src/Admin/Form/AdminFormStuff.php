<?php
/**
 * Created by PhpStorm.
 * User: vadimdez
 * Date: 29/10/13
 * Time: 22:31
 */

namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Db\Adapter\AdapterInterface;

class AdminFormStuff extends Form
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
            'name' => 'idInvUsage',
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
            'name' => 'stuffName',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'name'    => 'idStuff',
            'type'    => 'Zend\Form\Element\Select',
            'options' => array(
                'label'         => 'Stuff',
                'value_options' => $this->getOptionsForSelect(),
                'empty_option'  => '--- please choose ---'
            )
        ));

    }

    public function getOptionsForSelect()
    {
        $dbAdapter = $this->dbAdapter;
        $sql       = 'SELECT idStuff, stuffName FROM stuff ORDER BY stuffName ASC';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['idStuff']] = $res['stuffName'];
        }

        return $selectData;
    }



}