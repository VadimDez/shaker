<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/21/13
 * Time: 10:16 PM
 * To change this template use File | Settings | File Templates.
 */


return array(
    'controllers' => array(
        'invokables' => array(
            'Shaker\Controller\Shaker' => 'Shaker\Controller\ShakerController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'shaker' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/shaker[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Shaker\Controller\Shaker',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'shaker' => __DIR__ . '/../view',
        ),
    ),
);