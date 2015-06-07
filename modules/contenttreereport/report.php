<?php
/**
 * File containing the contenttreereport/report module view.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.4
 * @package ezpcontenttreereport
 */

/**
 * Disable memory and time limit
 */
set_time_limit( 0 );
ini_set( "memory_limit", -1 );

/**
 * Default module parameters
 */
$module = $Params["Module"];

/**
* Default class instances
*/

/** Parse HTTP POST variables **/
$http = eZHTTPTool::instance();

/** Access system variables **/
$sys = eZSys::instance();

/** Init template behaviors **/
$tpl = eZTemplate::factory();

/** Access ini variables **/
$ini = eZINI::instance();
$iniContentTreeReport = eZINI::instance( 'ezpcontenttreereport.ini' );

/** Report file variables **/
$storageDirectory = eZSys::cacheDirectory();
$contentTreeContentCsvReportName = 'ezpcontenttreereport';
$contentTreeContentCsvReportFileName = $contentTreeContentCsvReportName;
$contentTreeContentCsvReportFileNameWithExtension = $contentTreeContentCsvReportName . '.csv';
$contentTreeContentCsvReportFileNameWithExtensionFullPath = $storageDirectory . '/' . $contentTreeContentCsvReportFileNameWithExtension;

/** Default variables **/
$siteNodeUrlHostname = $ini->variable( 'SiteSettings', 'SiteURL' );
$adminSiteAccessName = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'AdminSiteAccessName' );
$currentSiteAccessName = $GLOBALS['eZCurrentAccess']['name'];


/**
 * If current siteaccess is not admin siteaccess use admin siteaccess for report generate cli parameter instead
 */
if( $currentSiteAccessName === $adminSiteAccessName )
{
    $reportGenerateSiteAccessName = $currentSiteAccessName;
}
else
{
    $reportGenerateSiteAccessName = $adminSiteAccessName;
}

/**
 * Test for generated report
 */
if ( file_exists( $contentTreeContentCsvReportFileNameWithExtensionFullPath ) )
{
    $fileTimeStamp = filemtime( $contentTreeContentCsvReportFileNameWithExtensionFullPath );
    $uniqueContentTreeCsvReportFileNameFullPath = $storageDirectory . '/' . $uniqueContentTreeCsvReportFileName;
    $uniqueContentTreeCsvReportFileName = $contentTreeContentCsvReportFileName . '_-_' . date( "Y_m_d_-_H_i_s", $fileTimeStamp ) . '.csv';
    $tpl->setVariable( 'fileModificationTimestamp', date("F d Y H:i:s", $fileTimeStamp ) );
    $tpl->setVariable( 'status', true );
}
else
{
    $uniqueContentTreeCsvReportFileNameFullPath = $storageDirectory . '/' . $uniqueContentTreeCsvReportFileName;
    $uniqueContentTreeCsvReportFileName = $contentTreeContentCsvReportFileName . '_-_' . date( "Y_m_d_-_H_i_s" ) . '.csv';
    $tpl->setVariable( 'status', false );
}

/**
 * Handle download action
 */
if ( $http->hasPostVariable( 'Download' ) )
{
    if ( !eZFile::download( $contentTreeContentCsvReportFileNameWithExtensionFullPath, true, $uniqueContentTreeCsvReportFileName ) )
       $module->redirectTo( 'contenttreereport/report' );
}

/**
 * Handle generate actions
 */
if ( $http->hasPostVariable( 'Generate' ) )
{
    // General script options
    $phpBin = '/usr/bin/php';
    $generatorWorkerScript = 'extension/ezpcontenttreereport/bin/php/ezpcontenttreereport.php';
    $options = '-s ' . $reportGenerateSiteAccessName . ' --hostname=' . $siteNodeUrlHostname . ' --report-filename=' . $contentTreeContentCsvReportFileNameWithExtension;
    $result = false;
    $output = false;

    exec( "$phpBin ./$generatorWorkerScript $options;", $output, $result );
}


/**
 * Default template include
 */
$Result = array();
$Result['content'] = $tpl->fetch( "design:contenttreereport/report.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr('design/standard/contenttreereport', 'Content Tree') ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr('design/standard/contenttreereport', 'Report') )
                        );

$Result['left_menu'] = 'design:contenttreereport/menu.tpl';

?>