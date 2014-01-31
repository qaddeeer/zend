<?php

/**
 * Absolute Db Table
 *
 * @category   Absolute
 * @package    Absolute_Db
 * @subpackage Table
 */
/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Class for SQL table interface.
 *
 * @category   Absolute
 * @package    Absolute_Db
 * @subpackage Table
 */
abstract class Absolute_Db_Table_Abstract extends Zend_Db_Table_Abstract {

    /**
     * Same like db table insert but for multiple rows
     * @author Qadeer Ahmad
     * @param array $data Multidimensional array
     * @return int Number of affected rows 
     */
    public function insertMulti(array $data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $valsAll = array();
        $bindData = array();
        $i = 0;
        foreach ($data as $key => $bind) {
            $vals = array();
            $cols = array();
            foreach ($bind as $col => $val) {
                $cols[] = $db->quoteIdentifier($col, true);
                if ($val instanceof Zend_Db_Expr) {
                    $vals[] = $val->__toString();
                    unset($bind[$col]);
                } else {
                    if ($db->supportsParameters('positional')) {
                        $vals[] = '?';
                        $bindData[$col . $key] = $val;
                    } else {
                        if ($db->supportsParameters('named')) {
                            unset($bind[$col]);
                            $bind[':col' . $i] = $val;
                            $vals[] = ':col' . $i;
                            $i++;
                        } else {
                            /** @see Zend_Db_Adapter_Exception */
                            require_once 'Zend/Db/Adapter/Exception.php';
                            throw new Zend_Db_Adapter_Exception(get_class($db) . " doesn't support positional or named binding");
                        }
                    }
                }
            }
            $valsAll[] = $vals;
        }
        $sqlDataArray = array();
        foreach ($valsAll as $key => $vals) {
            $sqlDataArray[] = ' (' . implode(', ', $vals) . ') ';
        }

        // build the statement
        $sql = 'INSERT INTO '
                . $db->quoteIdentifier($this->_name, true)
                . ' (' . implode(', ', $cols) . ') VALUES '
                . implode(', ', $sqlDataArray);


        // execute the statement and return the number of affected rows
        if ($db->supportsParameters('positional')) {
            $bindData = array_values($bindData);
        }

        $stmt = $db->query($sql, $bindData);
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Get the enum values from a table column. Only provide the column name which data type is enum
     * @author Qadeer Ahmad
     * @copyright Statigic System International
     * @param string $column column name for which you want to get enum values
     * @return array enum values as array key and value both will be enum values
     */
    public function getEnumValues($column) {
        $description = $this->info(self::METADATA);
        $enum = $description[$column]['DATA_TYPE'];
        $default = $description[$column]['DEFAULT'];

        $inizia_enum = strpos($enum, "'");
        $finisce_enum = strrpos($enum, "'");
        if ($inizia_enum === false || $finisce_enum === false)
            throw new Exception('errore enum database');

        $finisce_enum -= $inizia_enum;


        $enum = substr($enum, $inizia_enum, $finisce_enum + 1);
        //str_replace("'", '', $enum);
        $enum = explode(",", $enum);
        $output = array();
        if ($default)
            $output[$default] = $default;

        foreach ($enum as $key => $val) {
            $val = str_replace("'", '', $val);
            $output[$val] = $val;
        }
        return $output;
    }

    /**
     * Fetch Row By ID. For compound primary key use find method
     * @author Qadeer Ahmad
     * @copyright Statigic System International
     * @param int $id
     * @return Zend_Db_Table_Row 
     */
    public function fetchById($id) {
        $rows = $this->find($id);
        if(count($rows)) {
            return $rows->getRow(0);
        } else {
            return null;
        }
    }
    
    /**
     * Fetch All Specfic. For fetcing all specific columns data from table
     * @author Babar
     * @copyright Statigic System International
     * @param int $id
     * @return Zend_Db_Table_Row 
     */
    public function fetchAllSpecfic(array $select, $where='') {
        
        $s = new Zend_Db_Select(Zend_Db_Table::getDefaultAdapter());                     
        $s->from($this->_name, $select);
        if(!empty($where))
        {
            $s->where($where);
        }
        if(in_array('name', $select)){
            $s->order('name');
        }
        $rows = $s->query()->fetchAll();        
        
        if(count($rows)) {
            return $rows;
        } else {
            return null;
        }
    }
    
}
