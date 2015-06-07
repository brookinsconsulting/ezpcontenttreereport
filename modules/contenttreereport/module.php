<?php
/**
 * File containing the contenttreereport module configuration file, module.php
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.4
 * @package ezpcontenttreereport
*/

// Define module name
$Module = array('name' => 'Content Tree Report');

// Define module view and parameters
$ViewList = array();

// Define 'report' module view parameters
$ViewList['report'] = array( 'script' => 'report.php',
                             'functions' => array( 'report' ),
                             'default_navigation_part' => 'ezpcontenttreereportnavigationpart',
                             'post_actions' => array( 'Download', 'Generate' ),
                             'params' => array() );

// Define function parameters
$FunctionList = array();

// Define function 'report' parameters
$FunctionList['report'] = array();

?>