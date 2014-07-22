<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
	*@ Detail:     Manage Subscribers Controller
    **/

namespace Newsletter\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Newsletter\Model\Emailsubscription;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Newsletter\Service\EmailSubscriptions;

class ManageSubscribersController extends AbstractActionController
{
	protected $configTable;
	protected $emailsubscriptionTable;
	protected $container;
	public function __construct(){
        $this->container = new Container('namespace');
    }

	/**
    * Check if the admin is login otherwise redirect it to login page
    */
	public function onDispatch(\Zend\Mvc\MvcEvent $e) 
    {
        if(!isset($this->container->admin_id)){
            return $this->redirect()->toRoute('adminlogin');
        }
        return parent::onDispatch($e);
    }

	/**
    * Fetching all data of email subscribers to show grid
    */
   public function indexAction(){
		$message='';
		if(isset($this->container->message)){
			$message=$this->container->message;
			unset($this->container->message);
		}
		$request = $this->getRequest();
		$params = $request->getQuery();
		$optionArray=array();
		/* Check if sort. order and sortByColumn varible is not null */		
		if(!empty($params['sort']) && !empty($params['order'])){
			$optionArray['sortByColumn']['sort_column']=$params['sort'];
			$optionArray['sortByColumn']['sort_order']=$params['order'];
		}
		if(!empty($params['search'])){
			$optionArray['searchColumns']['searchKey']=$params['search'];
		}
		$emailsubscriptionClassObj=new EmailSubscriptions();
		$fields=$emailsubscriptionClassObj->getEmailSUbscribersFields();
		/* Prepare a query string */	
		foreach($fields as $field){
			$optionArray['fieldArray'][]=$field['fieldName'];
			if($field['searching']==1){
				$optionArray['searchColumns']['searchCol']=$field['fieldName'];
			}
		}
		$optionArray['default_sort_column']='subscription_date';
		$optionArray['default_sort_order']='ASC';
		$paginator=$this->getEmailSubscriptionTable()->fetchAll($optionArray,true);
		
		/* Set the current page to what has been passed in query string, or to 1 if none set  */
		$page=(int)$this->params()->fromQuery('page', 1);
		$paginator->setCurrentPageNumber($page);
		
		/*Set the number of items per page to 10 */
		$serialNumber=($page-1)*10+1;
		$paginator->setItemCountPerPage(10);
		return new ViewModel(array(
            'emailSubscribers' => $paginator,
			'message'=>$message,
			'fields'=>$fields,
			'showSearch'=>1,
			'defaultSortOrder'=>(($params['order']=='ASC' || empty($params['order']))?'DESC':'ASC'),
			'serialNumber'=>$serialNumber
        ));
    }

	/**
    * Delete email subscribers action 
    */
	public function deletesubscriberAction()
	{
		$id = $this->params()->fromRoute('id', 0);
		$view = new ViewModel(array('id' => $id,)); 
		$view->setTerminal(true); //Disabling layout
		if($id='pid') {
			$id=(int) $this->params()->fromRoute('pid', 0);
			if($id!=0) {
				$this->getEmailSubscriptionTable()->deleteEmailSubscriber($id);
				$this->container->message="Email subscriber has been deleted successfully.";
				return $this->redirect()->toRoute('manage-subscribers');	// Redirect with success message
			}
		}
        return $view;
	}

	/**
    * Getting email_subscription table obj
    */
    public function getEmailSubscriptionTable(){
        if (!$this->emailsubscriptionTable) {
            $sm = $this->getServiceLocator();
            $this->emailsubscriptionTable = $sm->get('Newsletter\Model\EmailsubscriptionTable');
        }
        return $this->emailsubscriptionTable;
    }
 }