<?php
return array(
    'text-get' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/text/:id',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Text',
                'action' => 'get'
            ),
        )
    ),
    'text-list' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/text/:type/list',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Text',
                'action' => 'listOfTypeAction'
            ),
        )
    ),
    
);
