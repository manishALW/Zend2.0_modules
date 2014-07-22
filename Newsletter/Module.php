<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    **/

namespace Newsletter;

use BaconAssetLoader\Asset\Collection as AssetCollection;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Newsletter\Model\Config;
use Newsletter\Model\AdminConfigTable;
use Newsletter\Model\Emailsubscription;
use Newsletter\Model\EmailsubscriptionTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\ModuleManager\ModuleManager;

use Zend\Db\Adapter\Adapter as DbAdapter;

 class Module
 {

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
		$sm = $e->getApplication()->getServiceManager();
		$sm->get('BaconAssetLoader.AssetManager')->getEventManager()->attach(
            'collectAssetInformation',
            function(Event $event) {
                $event->getTarget()->addAssets(new AssetCollection(__DIR__ . '/public'));
            }
        );
		$renderer = $sm->get('viewhelpermanager')->getRenderer();
		$eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) use ($renderer){
            $match      = $e->getRouteMatch();
			$configValues = include __DIR__ . '/config/module.config.php';
            /* Setting layout on the basis of route matched */
			if($match->getMatchedRouteName()=='manage-subscribers'){
			   $controller      = $e->getTarget();
			   $controller->layout('adminlogin/adminlayout');
			   if(isset($configValues['adminjsincludes'])){
				  foreach($configValues['adminjsincludes']['manage-subscribers'] as $route => $jsName){
					 $renderer->headScript()->appendFile($renderer->basePath().'/js/admin/'.$jsName);
				  }
               }
			   if(isset($configValues['admincssincludes'])){
				  foreach($configValues['admincssincludes']['manage-subscribers'] as $route => $cssName){
					 $renderer->headLink()->appendStylesheet($renderer->basePath().'/css/admin/'.$cssName);
				  }
               }
			}
            if($match->getMatchedRouteName()=='newsletter'){
			   if(isset($configValues['jsincludes'])){
				  foreach($configValues['jsincludes']['newsletter'] as $route => $jsName){
					 $renderer->headScript()->appendFile($renderer->basePath().'/js/'.$jsName);
				  }
               }
            }
        },100);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	  /* If this module is executed first time then creating required database tables */
		$create="CREATE TABLE IF NOT EXISTS `email_subscription` (
					 `id` int(11) NOT NULL AUTO_INCREMENT,
					 `user_id` int(11) NOT NULL DEFAULT '0',
					 `email_id` varchar(255) DEFAULT NULL,
					 `is_active` tinyint(1) DEFAULT NULL,
					 `subscription_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					 `is_new_release_email` tinyint(1) DEFAULT '0',
					 `is_exclusive_member_email` tinyint(1) DEFAULT '0',
					 PRIMARY KEY (`id`)
					 ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$configResult = $dbAdapter->query($create);
        if($configResult){
            $configResult->execute(); // Execute Query
        }
    }
    
    public function getAutoloaderConfig()
    {
		 /* Load your define namespace  */
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

     public function getConfig()
     {
		/* Load your module config file */
         return include __DIR__ . '/config/module.config.php';
     }
	 public function getServiceConfig()
     {
		/* Create model class object to perform DB operation, And load email subscription class  */
         return array(
             'factories' => array(
                  'Newsletter\Model\AdminConfigTable' =>  function($sm) {
                    $tableGateway = $sm->get('AdminConfigTableGateway');
                    $table = new AdminConfigTable($tableGateway);
                    return $table;
				  },
				  'AdminConfigTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Config());
                    return new TableGateway('admin_config', $dbAdapter, null, $resultSetPrototype);
				  },
				   'Newsletter\Model\EmailsubscriptionTable' =>  function($sm) {
                    $tableGateway = $sm->get('EmailsubscriptionTableGateway');
                    $table = new EmailsubscriptionTable($tableGateway);
                    return $table;
				  },
				  'EmailsubscriptionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Emailsubscription());
                    return new TableGateway('email_subscription', $dbAdapter, null, $resultSetPrototype);
				  },
			   ),
			);
		 }
 }
