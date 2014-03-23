<?php

namespace Radio;

return array(
    'controllers' => array(
        'invokables' => array(
            'RadioAdmin\Controller\Show' => 'RadioAdmin\Controller\Show',
            'RadioAdmin\Controller\Auth' => 'RadioAdmin\Controller\Auth',
            'RadioAdmin\Controller\User' => 'RadioAdmin\Controller\User',
            'RadioAdmin\Controller\Author' => 'RadioAdmin\Controller\Author',
            'RadioAdmin\Controller\Episode' => 'RadioAdmin\Controller\Episode',
            'RadioAdmin\Controller\Scheduling' => 'RadioAdmin\Controller\Scheduling',
            'RadioAdmin\Controller\Contribution' => 'RadioAdmin\Controller\Contribution',
            'RadioAdmin\Controller\Text' => 'RadioAdmin\Controller\Text',
            'RadioAdmin\Controller\Url' => 'RadioAdmin\Controller\Url',




        ),
    ),
    'router' => array(
        'routes' => array_merge(
            require("route.show.php"),
            require("route.auth.php"),
            require("route.user.php"),
            require("route.author.php"),
            require("route.episode.php"),
            require("route.scheduling.php"),
            require("route.contribution.php"),
            require("route.text.php"),
            require("route.url.php")





        )
    ),
    'view_manager' => array(
        'display_exceptions' => true,
        'exception_template' => 'error/index',
        'template_map' => array(
            'error/index' => __DIR__ . '/../../Radio/view/error/index.phtml',
            '404' => __DIR__ . '/../../Radio/view/error/404.phtml'
        ),
        'template_path_stack' => array(__DIR__ . '/../view',),
        'strategies' => array('ViewJsonStrategy'),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../../Radio/src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        ),
        'authentication' => array(
            'orm_default' => array(
                'objectManager' => 'doctrine.documentmanager.odm_default',
                'identityClass' => 'Radio\Entity\User',
                'identityProperty' => 'username',
                'credentialProperty' => 'password',
                'credentialCallable' => 'Radio\Entity\User::testPassword'
            ),
        ),
    )
);
