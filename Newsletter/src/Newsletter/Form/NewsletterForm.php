<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
	*@ Detail:     Manage Subscribers Controller
    **/

namespace Newsletter\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Application\Form\MyForm;

class NewsletterForm extends MyForm{
    public function __construct($name = null){
		//we want to ignore the name passed
		parent::__construct('newsletter');

		/*Add Text Box*/
		$this->addTextBox('email', '', '',true, 'email');
                
		///* Add Submit Button */
		$this->addSubmit('submit',array('id'=> 'submitbutton','value'=>'Login'));
	}
}