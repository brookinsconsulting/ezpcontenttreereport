<?php
/**
 * File containing the ezpcontenttreereportgenerate.php cronjob.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.4
 * @package ezpcontenttreereport
 */

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
$adminSiteAccessName = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'AdminUserSiteAccessName' );
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

// General cronjob part options
$phpBin = '/usr/bin/php';
$generatorWorkerScript = 'extension/ezpcontenttreereport/bin/php/ezpcontenttreereport.php';
$options = '-s ' . $reportGenerateSiteAccessName . ' --hostname=' . $siteNodeUrlHostname . ' --report-filename=' . $contentTreeContentCsvReportFileNameWithExtension;
$options = '';
$result = false;

passthru( "$phpBin ./$generatorWorkerScript $options;", $result );

print_r( $result ); echo "\n";

?>