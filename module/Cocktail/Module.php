<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/13/13
 * Time: 1:53 AM
 * To change this template use File | Settings | File Templates.
 */



namespace Cocktail;
// Add these import statements:
use Cocktail\Model\Cocktail;
use Cocktail\Model\CocktailTable;
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
                'Cocktail\Model\CocktailTable' =>  function($sm) {
                    $tableGateway = $sm->get('CocktailTableGateway');
                    $table = new CocktailTable($tableGateway);
                    return $table;
                },
                'CocktailTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Cocktail());
                    return new TableGateway('cocktails', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}