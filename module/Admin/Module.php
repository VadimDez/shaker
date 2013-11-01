<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 1:53 AM
 * To change this template use File | Settings | File Templates.
 */



namespace Admin;
// Add these import statements:
use Admin\Model\Admin;
use Admin\Model\AdminTable;
use Admin\Model\Category;
use Admin\Model\CategoryTable;
use Admin\Model\Usage;
use Admin\Model\UsageTable;
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
                'Admin\Model\AdminTable' =>  function($sm) {
                    $tableGateway = $sm->get('AdminTableGateway');
                    $table = new AdminTable($tableGateway);
                    return $table;
                },
                'AdminTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Admin());
                    return new TableGateway('cocktails', $dbAdapter, null, $resultSetPrototype);
                },

                'Admin\Model\UsageTable' =>  function($sm) {
                    $tableGateway = $sm->get('UsageTableGateway');
                    $table = new UsageTable($tableGateway);
                    return $table;
                },
                'UsageTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Usage());
                    return new TableGateway('used', $dbAdapter, null, $resultSetPrototype);
                },

                'Admin\Model\CategoryTable' =>  function($sm) {
                    $tableGateway = $sm->get('CategoryTableGateway');
                    $table = new CategoryTable($tableGateway);
                    return $table;
                },
                'CategoryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Category());
                    return new TableGateway('categories', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}