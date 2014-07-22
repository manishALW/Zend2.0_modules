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

class Config implements InputFilterAwareInterface
{
    public $config_id;
    public $config_group_type;
    public $name;
    public $code;
    public $value;
    public $options;
    public $function_name;
    public $type;
    public $show_required;
    public $min;
    public $max;
    public $description;
    protected $last_updated_by;
    protected $inputFilter;

    public function exchangeArray($data){
        $this->config_id     = (!empty($data['config_id'])) ? $data['config_id'] : null;
        $this->config_group_type = (!empty($data['config_group_type'])) ? $data['config_group_type'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->value = (!empty($data['value'])) ? $data['value'] : null;
        $this->options = (!empty($data['options'])) ? $data['options'] : null;
        $this->function_name = (!empty($data['function_name'])) ? $data['function_name'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->show_required = (!empty($data['show_required'])) ? $data['show_required'] : null;
        $this->min = (!empty($data['min'])) ? $data['min'] : null;
        $this->max = (!empty($data['max'])) ? $data['max'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->last_updated_by = (!empty($data['last_updated_by'])) ? $data['last_updated_by'] : 0;
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