<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    **/

namespace Newsletter\Service;
use Newsletter\Service\MailChimp;

Class EmailSubscriptions {
	/*
		*This function will synscronize all subscribers to mailchimp
	*/
	public function syncronizeSubscribersOnMailchip($data=array(),$keyArray=array()){
		$apiKey=$keyArray['api_key'];
		$mailchimpObj = new MailChimp($apiKey);
		$list_id = $keyArray['unique_list_id'];	
		try{
			if(sizeof($data)){
				$merge_vars = Array( 
									'EMAIL' => isset($data['email_address']) ? $data['email_address'] : "",
									'FNAME' => isset($data['first_name']) ? $data['first_name'] : "", 
									'LNAME' => isset($data['last_name']) ? $data['last_name']: ""
							);
				// Grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
				// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
				// By default this sends a confirmation email - you will not see new members
				// Until the link contained in it is clicked!
				if(isset($data['email_address']) && !empty($data['email_address'])){
					$mailchimpObj->listSubscribe($list_id,$data['email_address'],$merge_vars);
				}				
			}
		}catch (\Exception $ex) {}	
	}
	
	/*
		*This function will synscronize all unsubscribers to mailchimp.
	*/
	public function syncronizeUnsubscribersOnMailchip($email=""){
	
		#Get config object.
		$adminConfig = new AdminConfig();
		$whereApiArray = array('api_key');
		$configApiResults = $adminConfig->getConfigRow($whereApiArray);		
		$mailchimpObj = new MCAPI($configApiResults->value);
		$whereListArray = array('unique_list_id');
		$configListResults = $adminConfig->getConfigRow($whereListArray);
		$list_id = $configListResults->value;		
		try{
			if($email!=""){
				$retval = $mailchimpObj->listUnsubscribe($list_id,$email);				
			}
		}catch(Exception $e){
				//create an entry into error log
				$initLogger = Zend_Registry::get('initLogger');
				$initLogger->logErrors($e, 0,"Unable to load listUnSubscribe()!\n".$mailchimpObj->errorCode."::".$mailchimpObj->errorMessage, false);
		}		
	}

	// Get the fields to be fetched
	public function getEmailSUbscribersFields(){
        $fieldsConfigArray = array(
			array(
				'fieldName'=>'id',
				'label'=>'',
				'visible'=>0,
				'sorting'=>0,
				'searching'=>0,
			),
			array(
				'fieldName'=>'email_id',
				'label'=>'Email Address',
				'visible'=>1,
				'sorting'=>1,
				'searching'=>1,
			),
			array(
				'fieldName'=>'subscription_date',
				'label'=>'Subscribe Date',
				'visible'=>1,
				'sorting'=>1,
				'searching'=>0,
			)
		);
		return $fieldsConfigArray;
    }
}