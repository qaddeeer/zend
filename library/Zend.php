<?php

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if (APPLICATION_ENV == 'production' or APPLICATION_ENV == 'test' or @$_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
//    include_once '../library/ErrorHandling.php';
    include_once realpath(APPLICATION_PATH . '/../library/ErrorHandling.php');
}
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

//require_once 'Zend/Config/Ini.php';
//require_once 'Zend/Session.php';
//require_once 'Zend/Controller/Front.php';

$config = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV, array('allowModifications' => true)
);
$environment = new Zend_Config_Ini(
        APPLICATION_PATH . '/configs/' . APPLICATION_ENV . '.ini', APPLICATION_ENV
);

$config->merge($environment);

$application = new Zend_Application(
        APPLICATION_ENV, $config
);

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is used for geting Zend properties
 *
 * @author Qadeer Ahmad
 */
class Zend {

    /**
     * Containging the complete application.ini information.
     * @var Zend_Config_Ini 
     */
    public $config;
    public $front;

    public function __construct() {
        $this->front = Zend_Controller_Front::getInstance();

        $config = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV, array('allowModifications' => true)
        );
        $environment = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/' . APPLICATION_ENV . '.ini', APPLICATION_ENV
        );

        $config->merge($environment);

        $this->config = $config;
    }

}

?>
