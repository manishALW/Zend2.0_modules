<?php
    /**
    * @package   Newlesster management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    * @Detail        File contain layout path, email templates path, controllers mapping with the views, routing rules, services etc...
    **/
return array(
	'view_manager' => array(
        'template_path_stack' => array(
            'newsletter' => __DIR__ . '/../view',
        ),
    ),
     'controllers' => array(
         'invokables' => array(
            'Newsletter\Controller\Newsletter' => 'Newsletter\Controller\NewsletterController',
			'Newsletter\Controller\ManageSubscribers' => 'Newsletter\Controller\ManageSubscribersController',
         ),
     ),
	'service_manager' => array(
        'aliases' => array(
            'newsletter_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
    'router' => array(
         'routes' => array(
            'newsletter' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/newsletter[/:action]',
					  'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
						),
                     'defaults' => array(
                         'controller' => 'Newsletter\Controller\Newsletter',
                         'action'     => 'index',
                     ),
                 ),
            ),
			 'manage-subscribers' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/manage-subscribers[/:action][/:id][/:pid]',
					  'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						  'id'     => '[0-9a-zA-Z]+',
						   'pid'     => '[0-9a-zA-Z]+'
						),
                     'defaults' => array(
                         'controller' => 'Newsletter\Controller\ManageSubscribers',
                         'action'     => 'index',
                     ),
                 ),
            ),
        ),
    ),
	'jsincludes' => array(
        'newsletter' => array('jquery-1.8.1.min.js','jquery.validate.min.js','newslettersubs.js')
    ),
	'adminjsincludes' => array(
        'manage-subscribers' => array('jquery-1.8.1.min.js','thickbox.js')
    ),
	'admincssincludes' => array(
        'manage-subscribers' => array('thickbox.css')
    )
);
