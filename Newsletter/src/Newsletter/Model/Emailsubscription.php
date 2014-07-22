<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    **/

namespace Newsletter\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Emailsubscription implements InputFilterAwareInterface
{
    public $id;
    public $user_id;
    public $email_id;
    public $is_active;
    public $subscription_date;
    public $is_new_release_email;
    public $is_exclusive_member_email;
    protected $inputFilter;

    public function exchangeArray($data){
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->email_id = (!empty($data['email_id'])) ? $data['email_id'] : null;
        $this->is_active = (!empty($data['is_active'])) ? $data['is_active'] : null;
        $this->subscription_date = (!empty($data['subscription_date'])) ? $data['subscription_date'] : null;
        $this->is_new_release_email = (!empty($data['is_new_release_email'])) ? $data['is_new_release_email'] : null;
        $this->is_exclusive_member_email = (!empty($data['is_exclusive_member_email'])) ? $data['is_exclusive_member_email'] : null;
    }

    /**
    * Add content to these methods:
    */
    public function setInputFilter(InputFilterInterface $inputFilter){
        throw new \Exception("Not used");
    }

    /**
    * Input form validations for admin config module
    */
    public function getInputFilter(){
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                    array(
                        'name'    => 'EmailAddress',
                        'options' => array(
                        )
                    )
                )
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}