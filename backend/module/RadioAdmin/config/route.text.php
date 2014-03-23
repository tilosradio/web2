<?php
return array(
    'text-update' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'PUT',
            'route' => '/api/v0/text/:id',
            'permission' => 'admin',
            'defaults' => array(
                'controller' => 'RadioAdmin\Controller\Text',
                'action' => 'update'
            ),
        )
    ),
    'text-new' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'POST',
            'route' => '/api/v0/text',
            'permission' => 'admin',
            'defaults' => array(
                'controller' => 'RadioAdmin\Controller\Text',
                'action' => 'create'
            ),
        )
    ),

);
