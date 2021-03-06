<?php

/**
 * @see Zend_Paginator_Adapter_DbSelect
 */
require_once 'Zend/Paginator/Adapter/DbSelect.php';

/**
 * @see Absolute_JQuery_JqGrid_Adapter_Interface
 */
require_once 'Absolute/JQuery/JqGrid/Adapter/Interface.php';

/**
 * JqGrid DbSelect Adapter
 * 
 * @package Absolute_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Absolute Sight. (http://www.absolutesight.com)
 * @author andy.roberts
 */

class Absolute_JQuery_JqGrid_Adapter_DbSelect extends Zend_Paginator_Adapter_DbSelect implements Absolute_JQuery_JqGrid_Adapter_Interface
{
    protected $_operator = array(
        'EQUAL' => '= ?' , 
        'NOT_EQUAL' => '!= ?' , 
        'LESS_THAN' => '< ?' , 
        'LESS_THAN_OR_EQUAL' => '<= ?' , 
        'GREATER_THAN' => '> ?' , 
        'GREATER_THAN_OR_EQUAL' => '>= ?' , 
        'BEGIN_WITH' => 'LIKE ?' , 
        'NOT_BEGIN_WITH' => 'NOT LIKE ?' , 
        'END_WITH' => 'LIKE ?' , 
        'NOT_END_WITH' => 'NOT LIKE ?' , 
        'CONTAIN' => 'LIKE ?' , 
        'NOT_CONTAIN' => 'NOT LIKE ?',
        'IN' => 'IN (?)',
        'NOT_IN' => 'NOT IN (?)',
        'IS_NULL' => 'IS NULL',
        'IS_NOT_NULL' => 'IS NOT NULL'
    );

    /**
     * Sort the result set by a specified column.
     *
     * @param string $field Column name
     * @param string $direction Ascending (ASC) or Descending (DESC)
     * @return void
     */
    public function sort($field, $direction)
    {
        if (isset($field)) {
            $this->_select->order(array(
                
                $field . ' ' . $direction
            ));
        }
    }

    /**
     * Filter the result set based on criteria.
     *
     * @param string $field Column name
     * @param string $value Value to filter result set
     * @param string $operation Search operator
     */
    public function filter($field, $value, $expression, $options = array())
    {
        /**
         * Commented by Qadeer for multiple table query
         */
//        if (! array_key_exists($expression, $this->_operator)) {
//            return;
//        }
        
        switch($expression) {
            case 'IN':
            case 'NOT_IN':
                $value = new Zend_Db_Expr($value);
        }
        
        if (isset($options['multiple'])) {
            return $this->_multiFilter(array(
                
                'field' => $field , 
                'value' => $value , 
                'expression' => $expression
            ), $options);
        }
        /**
         * Modify by Qadeer for multiple table query
         */
//        return $this->_select->where($field . ' ' . $this->_operator[$expression], $this->_setWildCardInValue($expression, $value));
        return $this->_select->having('`' . $field . '` ' . $this->_operator[$expression], $this->_setWildCardInValue($expression, $value));
    }

       /**
     * Multiple filtering
     * 
     * @return
     */
    protected function _multiFilter($rules, $options = array())
    {
        
        $boolean = strtoupper($options['boolean']);
        foreach ($rules['field'] as $key=>$rule) {
            if ($boolean == 'OR') {
                /**
                 *  Modify by Qadeer for Multiple table query
                 */
//                $this->_select->orWhere($rule . ' ' . $this->_operator[$rules['expression'][$key]], $this->_setWildCardInValue($rules['expression'][$key], $rules['value'][$key]));
                $this->_select->orHaving('`' .$rule . '` ' . $this->_operator[$rules['expression'][$key]], $this->_setWildCardInValue($rules['expression'][$key], $rules['value'][$key]));
            } else {
                /**
                 * Modify by Qadeer for Multiple table query
                 */
//                $this->_select->where($rule. ' ' . $this->_operator[$rules['expression'][$key]], $this->_setWildCardInValue($rules['expression'][$key], $rules['value'][$key]));
                $this->_select->having('`' . $rule. '` ' . $this->_operator[$rules['expression'][$key]], $this->_setWildCardInValue($rules['expression'][$key], $rules['value'][$key]));
            }
        }
    }

    /**
     * Place wildcard filtering in value
     *
     * @return string
     */
    protected function _setWildCardInValue($expression, $value)
    {
        switch (strtoupper($expression)) {
            case 'BEGIN_WITH':
            case 'NOT_BEGIN_WITH':
                $value = $value . '%';
                break;
            
            case 'END_WITH':
            case 'NOT_END_WITH':
                $value = '%' . $value;
                break;
            
            case 'CONTAIN':
            case 'NOT_CONTAIN':
                $value = '%' . $value . '%';
                break;
        }
        
        return $value;
    }
}
