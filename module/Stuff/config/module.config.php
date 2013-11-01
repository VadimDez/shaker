<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/12/13
 * Time: 11:34 PM
 * To change this template use File | Settings | File Templates.
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Stuff\Controller\Stuff' => 'Stuff\Controller\StuffController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'stuff' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/stuff[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Stuff\Controller\Stuff',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'stuff' => __DIR__ . '/../view',
        ),
    ),
);