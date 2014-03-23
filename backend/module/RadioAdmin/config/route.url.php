<?php
return array(

    'url-new' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'POST',
            'route' => '/api/v0/url',
            'permission' => 'admin',
            'defaults' => array(
                'controller' => 'RadioAdmin\Controller\Url',
                'action' => 'create'
            ),
        )
    ),
    'url-delete' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'DELETE',
            'route' => '/api/v0/url/:id',
            'permission' => 'admin',
            'defaults' => array(
                'controller' => 'RadioAdmin\Controller\Url',
                'action' => 'delete'
            ),
        )
    )
);
