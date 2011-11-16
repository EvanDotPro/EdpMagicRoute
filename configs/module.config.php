<?php
return array( 
    'routes' => array(
        'default' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/[:namespace[/:controller[/:action]]]',
                'constraints' => array(
                    'namespace'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'namespace'  => 'application',
                    'controller' => 'index',
                    'action'     => 'index',
                ),
            ),
        ),
    ),
);
