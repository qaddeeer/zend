<?php

/**
 * @see Absolute_JQuery_JqGrid_Plugin_Abstract
 */
require_once 'Absolute/JQuery/JqGrid/Plugin/Abstract.php';

/**
 * Use for events
 *
 * @package Absolute_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Absolute Sight. (http://www.absolutesight.com)
 * @author Qadeer Ahmad
 */
class Absolute_JQuery_JqGrid_Plugin_Events extends Absolute_JQuery_JqGrid_Plugin_Abstract {

    protected $_events;

    /**
     * List of events you want to enable
     * @link http://www.trirand.com/jqgridwiki/doku.php?id=wiki:events
     * @param array $events afterInsertRow, beforeProcessing, beforeRequest, beforeSelectRow, gridComplete, loadBeforeSend, loadComplete, loadError, onCellSelect, ondblClickRow, onHeaderClick, onPaging, onRightClickRow, onSelectAll, onSelectRow, onSortCol, resizeStart, resizeStop, serializeGridData
     */
    public function __construct($events = array()) {
        $this->_events = $events;
    }

    public function preRender() {
        $gridId = $this->_grid->getId();

        if (in_array('afterInsertRow', $this->_events)) {
            $this->_grid->setOption(
                    'afterInsertRow', new Zend_Json_Expr('
                    function(rowid, rowdata, rowelem) {
                        if(typeof ' . $gridId . '_afterInsertRow == "function") {
                            ' . $gridId . '_afterInsertRow(rowid, rowdata, rowelem); 
                        }
                    }
                    ')
            );
        }

        if (in_array('beforeProcessing', $this->_events)) {
        $this->_grid->setOption(
                'beforeProcessing', new Zend_Json_Expr('
                    function(data, status, xhr) {
                        if(typeof ' . $gridId . '_beforeProcessing == "function") {
                            ' . $gridId . '_beforeProcessing(data, status, xhr); 
                        }
                    }
                    ')
        );
        }

        if (in_array('beforeRequest', $this->_events)) {
        $this->_grid->setOption(
                'beforeRequest', new Zend_Json_Expr('
                    function() {
                        if(typeof ' . $gridId . '_beforeRequest == "function") {
                            ' . $gridId . '_beforeRequest(); 
                        }
                    }
                    ')
        );
        }

        if (in_array('beforeSelectRow', $this->_events)) {
        $this->_grid->setOption(
                'beforeSelectRow', new Zend_Json_Expr('
                    function(rowid, e) {
                        if(typeof ' . $gridId . '_beforeSelectRow == "function") {
                            ' . $gridId . '_beforeSelectRow(rowid, e); 
                        }
                    }
                    ')
        );
        }

        if (in_array('gridComplete', $this->_events)) {
        $this->_grid->setOption(
                'gridComplete', new Zend_Json_Expr('
                    function() {
                        if(typeof ' . $gridId . '_gridComplete == "function") {
                            ' . $gridId . '_gridComplete(); 
                        }
                    }
                    ')
        );
        }

        if (in_array('loadBeforeSend', $this->_events)) {
        $this->_grid->setOption(
                'loadBeforeSend', new Zend_Json_Expr('
                    function(xhr, settings) {
                        if(typeof ' . $gridId . '_loadBeforeSend == "function") {
                            ' . $gridId . '_loadBeforeSend(xhr, settings); 
                        }
                    }
                    ')
        );
        }

        if (in_array('loadComplete', $this->_events)) {
        $this->_grid->setOption(
                'loadComplete', new Zend_Json_Expr('
                    function(data) {
                        if(typeof ' . $gridId . '_loadComplete == "function") {
                            ' . $gridId . '_loadComplete(data); 
                        }
                    }
                    ')
        );
        }

        if (in_array('loadError', $this->_events)) {
        $this->_grid->setOption(
                'loadError', new Zend_Json_Expr('
                    function(xhr, status, error) {
                        if(typeof ' . $gridId . '_loadError == "function") {
                            ' . $gridId . '_loadError(xhr, status, error); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onCellSelect', $this->_events)) {
        $this->_grid->setOption(
                'onCellSelect', new Zend_Json_Expr('
                    function(rowid, iCol, cellcontent, e) {
                        if(typeof ' . $gridId . '_onCellSelect == "function") {
                            ' . $gridId . '_onCellSelect(rowid, iCol, cellcontent, e); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onDblClickRow', $this->_events)) {
        $this->_grid->setOption(
                'onDblClickRow', new Zend_Json_Expr('
                    function(rowid, iRow, iCol, e) {
                        if(typeof ' . $gridId . '_onDblClickRow == "function") {
                            ' . $gridId . '_onDblClickRow(rowid, iRow, iCol, e); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onHeaderClick', $this->_events)) {
        $this->_grid->setOption(
                'onHeaderClick', new Zend_Json_Expr('
                    function(gridstate) {
                        if(typeof ' . $gridId . '_onHeaderClick == "function") {
                            ' . $gridId . '_onHeaderClick(gridstate); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onPaging', $this->_events)) {
        $this->_grid->setOption(
                'onPaging', new Zend_Json_Expr('
                    function(pgButton) {
                        if(typeof ' . $gridId . '_onPaging == "function") {
                            ' . $gridId . '_onPaging(pgButton); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onRightClickRow', $this->_events)) {
        $this->_grid->setOption(
                'onRightClickRow', new Zend_Json_Expr('
                    function(rowid, iRow, iCol, e) {
                        if(typeof ' . $gridId . '_onRightClickRow == "function") {
                            ' . $gridId . '_onRightClickRow(rowid, iRow, iCol, e); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onSelectAll', $this->_events)) {
        $this->_grid->setOption(
                'onSelectAll', new Zend_Json_Expr('
                    function(aRowids, status) {
                        if(typeof ' . $gridId . '_onSelectAll == "function") {
                            ' . $gridId . '_onSelectAll(aRowids, status); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onSelectRow', $this->_events)) {
        $this->_grid->setOption(
                'onSelectRow', new Zend_Json_Expr('
                    function(id) {
                        if(typeof ' . $gridId . '_onSelectRow == "function") {
                            ' . $gridId . '_onSelectRow(id); 
                        }
                    }
                    ')
        );
        }

        if (in_array('onSortCol', $this->_events)) {
        $this->_grid->setOption(
                'onSortCol', new Zend_Json_Expr('
                    function(index, iCol, sortorder) {
                        if(typeof ' . $gridId . '_onSortCol == "function") {
                            ' . $gridId . '_onSortCol(index, iCol, sortorder); 
                        }
                    }
                    ')
        );
        }
        
        if (in_array('resizeStart', $this->_events)) {
        $this->_grid->setOption(
                'resizeStart', new Zend_Json_Expr('
                    function(event, index) {
                        if(typeof ' . $gridId . '_resizeStart == "function") {
                            ' . $gridId . '_resizeStart(event, index); 
                        }
                    }
                    ')
        );
        }

        if (in_array('resizeStop', $this->_events)) {
        $this->_grid->setOption(
                'resizeStop', new Zend_Json_Expr('
                    function(newwidth, index) {
                        if(typeof ' . $gridId . '_resizeStop == "function") {
                            ' . $gridId . '_resizeStop(newwidth, index); 
                        }
                    }
                    ')
        );
        }

        if (in_array('serializeGridData', $this->_events)) {
        $this->_grid->setOption(
                'serializeGridData', new Zend_Json_Expr('
                    function(postData) {
                        if(typeof ' . $gridId . '_serializeGridData == "function") {
                            ' . $gridId . '_serializeGridData(postData); 
                        }
                    }
                    ')
        );
        }
    }

    public function postRender() {    // Not implemented
    }

    public function preResponse() {    // Not implemented
    }

    public function postResponse() {    // Not implemented
    }

}