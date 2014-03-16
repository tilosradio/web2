<?php
return array(
    'show-get' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/show/:id',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Show',
                'action' => 'get'
            ),
        )
    ),
    'show-list' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/show',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Show',
                'action' => 'getList'
            ),
        )
    ),
    'show-episodes' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/show/:id/episodes',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Show',
                'action' => 'listOfEpisodes'
            )
        )
    ),
    'show-episode-by-alias' => array(
        'type' => 'Radio\Util\CustomSegmentRouter',
        'options' => array(
            'method' => 'GET',
            'route' => '/api/v0/show/:show/episode/:year/:month/:day',
            'permission' => 'guest',
            'defaults' => array(
                'controller' => 'Radio\Controller\Show',
                'action' => 'getEpisodeByAlias'
            )
        )
    ),
);
