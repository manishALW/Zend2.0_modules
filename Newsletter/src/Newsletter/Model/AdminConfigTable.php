<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    **/
namespace Newsletter\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class AdminConfigTable{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }

     /**
    * Fetching all data
    */
    public function fetchAll(){
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
    *Fetching row on the basis of column
    */
    public function fetchConfigRow($column,$value){
        $resultSet = $this->tableGateway->select(array($column => $value));
        return $resultSet;
    }

    /**
    * Updating row on the basis of column
    */
    public function updateDetail($data = array(),$configId=0){
        if($configId && count($data)){
            return $this->tableGateway->update($data, array('config_id' => $configId));
        }
    }

    /**
    * Getting values on the basis of url_key
    */
    public function getConfigValueByUrls($dataArray=array()){
        $resultSet = $this->tableGateway->select(function(Select $select) use ($dataArray){
            $select->columns(array('value','code'));
            $select->where->in('code',$dataArray);
        });
        $resultArray=array();
        foreach($resultSet as $res){
            $resultArray[$res->code]=$res->value;
        }
        return $resultArray;
    }
}