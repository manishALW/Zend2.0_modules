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
use Newsletter\Model\Config;
use Newsletter\Model\Emailsubscription;
use Newsletter\Form\NewsletterForm;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Newsletter\Service\EmailSubscriptions;

class NewsletterController extends AbstractActionController
{
	protected $configTable;
	protected $emailsubscriptionTable;
	protected $container;
	public function __construct(){ $this->container = new Container('namespace'); }

	/**
    * Newsletter subscription action
    */
    public function indexAction(){
		$form = new NewsletterForm();
		$request = $this->getRequest();
		$message='';
		if(isset($this->container->subscribeSuccess)){
			$message=$this->container->subscribeSuccess;
			unset($this->container->subscribeSuccess);
		}

        if ($request->isPost()) { /* Is Post */
			$emailSubsObj = new Emailsubscription();
            $form->setInputFilter($emailSubsObj->getInputFilter());
			$form->setData($request->getPost());
            if ($form->isValid()) { /* Is valid */
				$formData=$form->getData();
				$dataArray=array(
								'user_id'=>($this->container->user_id?$this->container->user_id:0),
								'email_id'=>$formData['email'],
								'is_active'=>1,
								'is_new_release_email'=>1,
								'is_exclusive_member_email'=>1
								);
				/* Add email in mailchimp list id and update database */
				$emailSubObj=new EmailSubscriptions();
				$keyArray=$this->getConfigTable()->getConfigValueByUrls(array('api_key','unique_list_id'));
				$response=$emailSubObj->syncronizeSubscribersOnMailchip(array('email_address'=>$formData['email'],'first_name'=>'','last_name'=>''),$keyArray);
				$this->getEmailSubscriptionTable()->create($dataArray);
				$this->container->subscribeSuccess='You are successfully subscribed.';
				return $this->redirect()->toRoute('newsletter'); /* Redirect user with success message */
			}
		}
		return array('form'=>$form,'message'=>$message);
    }

	/**
    * Checking if the email is already subscribed for newsletter subscription
    */
    public function checkemailAction()
    {
		$request = $this->getRequest();
		$params = $request->getQuery();
		if(!empty($params->email)){
			$cmspage = $this->getEmailSubscriptionTable()->fetchRow('email_id',$params->email);
			if(count($cmspage)>0){
				exit("1");
			}else{
				exit("2");
			}
		}
    }

	 /**
    *Getting admin_config table obj
    */
    public function getConfigTable(){
        if (!$this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Newsletter\Model\AdminConfigTable');
        }
        return $this->configTable;
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