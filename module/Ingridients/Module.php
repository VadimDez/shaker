<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 6:30 PM
 * To change this template use File | Settings | File Templates.
 */




namespace Ingridients;
// Add these import statements:
use Ingridients\Model\Ingridients;
use Ingridients\Model\IngridientsTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }


    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Ingridients\Model\IngridientsTable' =>  function($sm) {
                    $tableGateway = $sm->get('IngridientsTableGateway');
                    $table = new IngridientsTable($tableGateway);
                    return $table;
                },
                'IngridientsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Ingridients());
                    return new TableGateway('ingridients', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}