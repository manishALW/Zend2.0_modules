<?php
    /**
    * @package   Newletter management
    * @access       Public
    * @version      2.0
    * @Owner        ALW
    **/

namespace Newsletter\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;

class EmailsubscriptionTable{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
        $this->tableGateway = $tableGateway;
    }
    
    public function create($dataArray=array()){
        $id=$this->tableGateway->insert($dataArray);
        return $id;
    }

     /**
    * Fetching all data
    */
     public function fetchAll($optionArray=array(),$paginated=true){
		if($paginated) {
            // Create a new Select object for the table cmspage
            $select = new Select('email_subscription');
			if(!empty($optionArray['fieldArray'])){
				$select->columns($optionArray['fieldArray']);	
			}
			if(!empty($optionArray['sortByColumn']['sort_column']) && !empty($optionArray['sortByColumn']['sort_order'])){
				$orderBy=$optionArray['sortByColumn']['sort_column'].' '.$optionArray['sortByColumn']['sort_order'];
				$select->order($orderBy);	
			}else{
				if(!empty($optionArray['default_sort_column']) && !empty($optionArray['default_sort_order'])){
					$orderBy=$optionArray['default_sort_column'].' '.$optionArray['default_sort_order'];
					$select->order($orderBy);	
				}
			}
			if(!empty($optionArray['searchColumns']['searchKey']) && !empty($optionArray['searchColumns']['searchCol'])){
				$searchKey="%".$optionArray['searchColumns']['searchKey']."%";
				$searchCol=($optionArray['searchColumns']['searchCol']?$optionArray['searchColumns']['searchCol']:$optionArray['fieldArray'][1]);
				$select->where->like($searchCol,$searchKey);
			}

            //Create a new result set based on the cmspage entity
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Emailsubscription());

            //Create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
                // Our configured select object
                $select,
                // The adapter to run it against
                $this->tableGateway->getAdapter(),
                // The result set to hydrate
                $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
    }

    /**
    *Fetching row on the basis of column
    */
    public function fetchRow($column,$value){
        $resultSet = $this->tableGateway->select(array($column => $value));
        return $resultSet;
    }

    /**
    *Updating row on the basis of column
    */
    public function updateDetail($data = array(),$configId=0){
        if($configId && count($data)){
            return $this->tableGateway->update($data, array('id' => $configId));
        }
    }
    
    public function deleteEmailSubscriber($id){
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}