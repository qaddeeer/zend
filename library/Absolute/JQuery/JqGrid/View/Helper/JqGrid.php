<?php

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once 'ZendX/JQuery/View/Helper/UiWidget.php';

/**
 * JqGrid View Helper
 * 
 * @package Absolute_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Absolute Sight. (http://www.absolutesight.com)
 * @author Qadeer Ahmad (qadeer@najoomi.com)
 */

class Absolute_JQuery_JqGrid_View_Helper_JqGrid extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Render jqGrid
     * 
     * @param Absolute_JQuery_JqGrid $grid
     */
    public function jqGrid(Absolute_JQuery_JqGrid $grid)
    {
        
        $html = array();
        $js = array();
        $onload = array();
        
        
        $onload[] = sprintf('%s("#%s").jqGrid(%s);', 
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(), 
                $grid->getId(), ZendX_JQuery::encodeJson($grid->getOptions()));
        
        $html[] = '<div style="clear: both;"></div><table id="' . $grid->getId() . '"></table>';

        // Load the jqGrid plugin view variables
        $html   = array_merge($html, $this->view->jqGridPluginBroker['html']);
        $js     = array_merge($js, $this->view->jqGridPluginBroker['js']);
        $onload = array_merge($onload, $this->view->jqGridPluginBroker['onload']);
        
        
//        $this->jquery->addOnLoad(implode("\n", $onload));
//        $this->view->headScript()->appendScript(implode("\n", $js));
        $this->jquery->addOnLoad(stripcslashes(implode("\n", $onload)));
        $this->view->headScript()->appendScript(stripcslashes(implode("\n", $js)));

        return implode("\n", $html);
    }
}