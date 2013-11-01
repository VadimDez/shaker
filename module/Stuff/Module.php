<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/12/13
 * Time: 11:29 PM
 * To change this template use File | Settings | File Templates.
 */


namespace Stuff;
// Add these import statements:
use Stuff\Model\Stuff;
use Stuff\Model\StuffTable;
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
                'Stuff\Model\StuffTable' =>  function($sm) {
                    $tableGateway = $sm->get('StuffTableGateway');
                    $table = new StuffTable($tableGateway);
                    return $table;
                },
                'StuffTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Stuff());
                    return new TableGateway('stuff', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}