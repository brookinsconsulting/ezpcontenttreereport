#!/usr/bin/env php
<?php
/**
 * File containing the ezpcontenttreereport.php bin script
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.4
 * @package ezpcontenttreereport
 */

/**
 * Add a starting timing point tracking script execution time
 */
$srcStartTime = microtime( true );

/**
 * Require eZ Publish autload system
 */
require 'autoload.php';

/**
 * Disable memory and time limit
 */

set_time_limit( 0 );

ini_set( "memory_limit", -1 );

/** Script startup and initialization **/

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "eZ Publish Content Tree CSV Report Script\n" .
                                                        "\n" .
                                                        "ezpcontenttreereport.php --siteaccess=admin_siteaccess_name --hostname=www.example.com --report-filename=ezpcontenttreereport_-_2015-06-02--1642.csv --storage-dir=var/contentTreeReportCsv --exclude-node-ids=43,1999,2001" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true,
                                     'user' => true ) );

$script->startup();

$options = $script->getOptions( "[script-verbose;][script-verbose-level;][hostname;][report-filename;][exclude-node-ids;][storage-dir;]",
                                "[node]",
                                array( 'storage-dir' => 'Use this parameter to customize the path to the directory to store generated report csv file within. Example: ' . "'--storage-dir=var/contentTreeReportCsv'" . ' is an optional parameter which defaults to var/(siteaccessname)/cache',
                                       'hostname' => 'Use this parameter to customize the absolute user siteaccess content object urls hostname. Example: ' . "'--hostname=example.com'" . ' is an optional parameter which defaults to admin siteacess SiteUrl value',
                                       'report-filename' => 'Use this parameter to customize the report output csv filename. Example: ' . "'--report-filename=ezpcontenttreereport_-_2015-06-02--1642.csv'" . ' is an optional parameter which defaults to the following file name pattern (date time is dynamic): ' . "'ezpcontenttreereport_-_2015-06-02--1642.csv'",
                                       'exclude-node-ids' => 'Use this parameter to customize the nodeIDs to be excluded from the report. Parameter input is a comma separted list of nodeIDs. This parameter overrides default ini settings when used. Example: ' . "'--exclude-node-ids=42,75,101'" . ' is an optional parameter which defaults to ini settings values when not used.',
                                       'script-verbose' => 'Use this parameter to display verbose script output without disabling script iteration counting of images created or removed. Example: ' . "'--script-verbose'" . ' is an optional parameter which defaults to false',
                                       'script-verbose-level' => 'Use only with ' . "'--script-verbose'" . ' parameter to see more of execution internals. Example: ' . "'--script-verbose-level=3'" . ' is an optional parameter which defaults to 1 and works till 5' ),
                                false,
                                array( 'user' => true ) );
$script->initialize();

/** Display of execution time **/

function executionTimeDisplay( $srcStartTime, $cli )
{
    /** Add a stoping timing point tracking and calculating total script execution time **/

    $srcStopTime = microtime( true );
    $startTimeCalc = $srcStartTime;
    $stopTimeCalc = $srcStopTime;
    $executionTime = round( $srcStopTime - $srcStartTime, 2 );

    /** Alert the user to how long the script execution took place **/

    $cli->output( "This script execution completed in " . $executionTime . " seconds" . ".\n" );
}

/** Access ini variables **/

$ini = eZINI::instance();
$iniContentTreeReport = eZINI::instance( 'ezpcontenttreereport.ini' );

$adminSiteAccessName = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'AdminSiteAccessName' ) == 'ezwebin_site_admin' ? 'ezwebin_site_admin' : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'AdminSiteAccessName' );
$userSiteAccessName = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'UserSiteAccessName' ) == 'ezwebin_site_user' ? 'ezwebin_site_user' : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'UserSiteAccessName' );

$iniContent = eZINI::getSiteAccessIni( $userSiteAccessName, 'content.ini' );
$iniSiteAdmin = eZINI::getSiteAccessIni( $adminSiteAccessName, 'site.ini' );

/** Script default settings values **/

$siteContentTreeRootNode = $iniContent->variable( 'NodeSettings', 'RootNode' );

$iniSiteAdminSiteAccessHostname = $iniSiteAdmin->variable( 'SiteSettings', 'SiteURL' );

$defaultCsvHeader = array( 'NodeID', 'ContentObjectID', 'Version', 'Visibility', 'Content Class', 'SectionID', 'Section Name', 'Published Date', 'Modified Date', 'Node Children Count', 'Node Name', 'Content Tree Path', 'Node Url', 'Site Url' );

$csvHeader = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'CsvHeader' ) == $defaultCsvHeader ? $defaultCsvHeader : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'CsvHeader' );

$limit = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'IterationFetchLimit' ) == 350 ? 350 : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'IterationFetchLimit' );

$defaultAdminUserID = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'DefaultAdminUserID' ) == 14 ? 14 : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'DefaultAdminUserID' );

$siteNodeUrlPrefix = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'UrlProtocolPrefix' ) == 'http://' ? 'http://' : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'UrlProtocolPrefix' );

$contentTreeNodeIDs = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ContentTreeNodeIDs' ) == false ? array( 2 ) : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ContentTreeNodeIDs' );

$excludeParentNodeIDs = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ExcludedParentNodeIDs' ) == false ? array() : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ExcludedParentNodeIDs' );

$classFilterType = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ClassFilterType' ) == 'exclude' ? 'exclude' : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ClassFilterType' );

$classFilterClasses = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ClassFilterArray' ) == false ? array() : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ClassFilterArray' );

$ignoreVisibility = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ExcludeHiddenNodes' ) == 'enabled' ? false : true;

$customReportContentObjectAttributes = $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ContentObjectAttributes' ) == false ? array() : $iniContentTreeReport->variable( 'eZPContentTreeReportSettings', 'ContentObjectAttributes' );


/** Login as default admin system user for full database access **/

$currentuser = eZUser::currentUser();
$currentuser->logoutCurrent();
$user = eZUser::fetch( $defaultAdminUserID );
$user->loginCurrent();

/** Test for required script arguments **/

$verbose = isset( $options['script-verbose'] ) ? true : false;

$scriptVerboseLevel = isset( $options['script-verbose-level'] ) ? $options['script-verbose-level'] : 1;

$troubleshoot = ( isset( $options['script-verbose-level'] ) && $options['script-verbose-level'] > 0 ) ? true : false;

$scriptSiteAccess = ( isset( $options['siteaccess'] ) && strlen( $options['siteaccess'] ) >= 1 ) ? $options['siteaccess'] : false;

/** Test for required siteaccess argument **/

if ( !$scriptSiteAccess )
{
    $cli->warning( "To run this script you must provide the the required argument. --siteaccess=admin_siteaccess_name OR -s admin_siteaccess_name to use this extension script" );
    $cli->output();
    // Shutdown the script and exit eZ
    $script->shutdown( 1 );
}
elseif ( $scriptSiteAccess != $adminSiteAccessName )
{
    $cli->warning( "The --siteaccess=$scriptSiteAccess parameter input does not match the settings file settings value within ezpcontenttreereport.ini [eZPContentTreeReportSettings] AdminSiteAccessName=$adminSiteAccessName" );
    $cli->warning( "Please override the ezpcontenttreereport.ini setting file and customize the setting variable [eZPContentTreeReportSettings] AdminSiteAccessName to contain your actual admin_siteaccess_name text string, clear ini caches and re-run this script" );
    $cli->error( "This is a required setup step and required script argument." );
    $cli->output();
    // Shutdown the script and exit eZ
    $script->shutdown( 1 );
}

/** Test for optional report storage directory path argument **/

if ( $options['storage-dir'] )
{
    $storageDirectory = $options['storage-dir'];
}
else
{
    $storageDirectory = eZSys::cacheDirectory();
}

/** Test for optional report user siteaccess hostname argument **/

if ( $options['hostname'] )
{
    $siteNodeUrlHostname = $options['hostname'];
}
else
{
    $siteNodeUrlHostname = $iniSiteAdminSiteAccessHostname;
}

/**
 * Test for user site siteaccess SiteURL Hostname === admin siteaccess SiteURL Hostname
 * If the hostnames match then append the admin siteaccess name (from ini settings)
 * TODO: Refactor the admin siteaccess url generation process to be more dynamic.
         This aspect was hard coded due to poor settings configuration in inital target installation.
         This makes the report admin urls more or less hard coded to include siteacess name in urls which was required for the initial use case but not required ( or supported for all other possible use cases ).
 */

if ( $siteNodeUrlHostname === $iniSiteAdminSiteAccessHostname )
{
    $siteNodeUrlHostname .= '/' . $adminSiteAccessName;
}

/** Test for optional report filename argument **/

if ( $options['report-filename'] )
{
    $contentTreeReportCsvFileName = $options['report-filename'];
    $contentTreeReportCsvFilePath = $storageDirectory . '/' . $contentTreeReportCsvFileName;
}
else
{
    $contentTreeContentCsvReportName = 'ezpcontenttreereport';
    $contentTreeContentCsvReportFileName = $contentTreeContentCsvReportName;
    $contentTreeReportCsvFileName = $contentTreeContentCsvReportFileName . '.csv';
    $uniqueContentTreeReportCsvFileName = $contentTreeContentCsvReportFileName . '_-_' . date( "Y_m_d_-_H_i_s" ) . '.csv';
    $uniqueContentTreeReportCsvFileNameFullPath = $storageDirectory . '/' . $uniqueContentTreeReportCsvFileName;

    $contentTreeReportCsvFileName = $uniqueContentTreeReportCsvFileName;
    $contentTreeReportCsvFilePath = $uniqueContentTreeReportCsvFileNameFullPath;
}

/** Test for optional report excluded nodeIDs argument **/

if ( isset( $options['exclude-node-ids'] ) )
{
    $excludeParentNodeIDs = explode( ',', $options['exclude-node-ids'] );
}

/** Alert user of report generation process starting **/

if ( $troubleshoot && $scriptVerboseLevel >= 2 )
{
    $cli->output( "Fetching report content ..." );
}

/** Script default values **/

$openedFPs = array();
$subTree = array();
$countContentTreeNodeIDs = $contentTreeNodeIDs;
$fetchContentTreeNodeIDs = $contentTreeNodeIDs;
$subTreeCountIterationCount = array();
$subTreeCount = 0;
$subTreeTotalCount = 0;
$subtreeOffset = 0;
$offset = 0;

/** Optional debug output **/

if ( $troubleshoot && $scriptVerboseLevel >= 5 )
{
    $cli->output( var_dump( $countContentTreeNodeIDs) );
}

/** Iterate over content tree root nodes and subtree results **/

while ( list( $contentTreeNodeKey, $contentTreeNodeID ) = each( $countContentTreeNodeIDs ) )
{
    /** Fetch starting node from content tree **/

    $contentTreeNode = eZContentObjectTreeNode::fetch( $contentTreeNodeID );

    if ( !$contentTreeNode )
    {
        $cli->error( "No node with ID: $contentTreeNodeID" );
        $script->shutdown( 3 );
    }
    else
    {
        $subTreeCountIterationCount[ $contentTreeNodeKey ] = array( 'total_count' => 1, 'count' => 0 );
    }

    /** Fetch content subtree tree count **/

    $subTreeCountParams = array( 'ClassFilterType' => $classFilterType,
                                 'ClassFilterArray' => $classFilterClasses,
                                 'Depth' => 10,
                                 'IgnoreVisibility' => $ignoreVisibility );

    $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] += $contentTreeNode->subTreeCount( $subTreeCountParams );
    $subTreeCountIterationCount[ $contentTreeNodeKey ]['total_count'] += $contentTreeNode->subTreeCount( $subTreeCountParams );

    $subTreeCount += $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'];
    $subTreeTotalCount += $subTreeCountIterationCount[ $contentTreeNodeKey ]['total_count'];
}

/** Debug verbose output **/

if ( !$subTreeCount )
{
    $cli->error( "No content tree objects found" );

    /** Call for display of execution time **/
    executionTimeDisplay( $srcStartTime, $cli );

    $script->shutdown( 3 );
}
elseif ( $verbose && $subTreeCount > 0 )
{
    $cli->warning( "Total content tree objects estimated to be included in report: " . $subTreeTotalCount );
}

/** Setup script iteration details **/

$script->setIterationData( '.', '.' );
$script->resetIteration( $subTreeTotalCount );

/** Custom Report Content Object Attributes **/

$customReportContentObjectAttributesCsvHeaderNames = array();
$customReportContentObjectAttributesIdentifiers = array();
$customReportContentObjectAttributesValues = array_values( $customReportContentObjectAttributes );

foreach( $customReportContentObjectAttributesValues as $customReportContentObjectAttributesValuesItem )
{
    $customReportContentObjectAttributesValuesArray = explode( ';', $customReportContentObjectAttributesValuesItem );
    $customReportContentObjectAttributesValuesItemAttributeName = $customReportContentObjectAttributesValuesArray[3];

    $customReportContentObjectAttributesIdentifiers[] = $customReportContentObjectAttributesValuesArray;

    if ( !in_array( $customReportContentObjectAttributesValuesItemAttributeName, $customReportContentObjectAttributesCsvHeaderNames ) )
    {
        $customReportContentObjectAttributesCsvHeaderNames[] = $customReportContentObjectAttributesValuesItemAttributeName;
    }
}

$customReportContentObjectAttributesCsvHeaderColumnCount = count( $customReportContentObjectAttributesCsvHeaderNames );

if ( count( $customReportContentObjectAttributesCsvHeaderNames ) > 0 )
{
    $csvHeader = array_merge( $csvHeader, $customReportContentObjectAttributesCsvHeaderNames );
}

/** Open report file, prepare writting and add report header **/

if ( $subTreeCount > 0 )
{
    /** Open report file for writting **/

    if ( !isset( $openedFPs[$contentTreeReportCsvFileName] ) )
    {
        if ( !file_exists( $storageDirectory ) )
        {
            mkdir( $storageDirectory, 0775);
        }

        $tempFP = @fopen( $contentTreeReportCsvFilePath, "w" );

        if ( $tempFP )
        {
            $openedFPs[$contentTreeReportCsvFileName] = $tempFP;
        }
        else
        {
            $cli->error( "Can not open output file for $contentTreeReportCsvFilePath file" );
            $script->shutdown( 4 );
        }
    }
    else
    {
       if ( !$openedFPs[$contentTreeReportCsvFileName] )
       {
            $cli->error( "Can not open output file for $contentTreeReportCsvFilePath file" );
            $script->shutdown( 4 );
       }
    }

    /** Define report file pointer **/

    $fp = $openedFPs[$contentTreeReportCsvFileName];

    /** Write report csv header **/

    if ( !fputcsv( $fp, $csvHeader, ';' ) )
    {
        $cli->error( "Can not write to report file" );
        $script->shutdown( 6 );
    }
}


/** Alert user of report generation process starting **/

$cli->output( "Generating report ...\n" );

/** Iterate over content tree root nodes and subtree results **/

while ( list( $contentTreeNodeKey, $contentTreeNodeID ) = each( $fetchContentTreeNodeIDs ) )
{
    $offset = 0;

    /** Fetch starting node from content tree **/

    $contentTreeNode = eZContentObjectTreeNode::fetch( $contentTreeNodeID );

    if ( !$contentTreeNode )
    {
        $cli->error( "No node with ID: $contentTreeNodeID" );
        $script->shutdown( 3 );
    }

    /** Optional debug output **/

    if ( $troubleshoot && $scriptVerboseLevel >= 4 )
    {
        $cli->output( "* Start of Content Tree Iteration: RootNodeID: $contentTreeNodeID, SubTreeCount: " . $subTreeCount .", SubtreeOffset: $subtreeOffset, subTreeCountIterationCount: " . $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] . "\n");
    }

    while ( $offset < $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] )
    {
        $subTree = array();

        /** Fetch content tree nodes in content tree **/

        $subTreeParams = array( 'ClassFilterType' => $classFilterType,
                                'ClassFilterArray' => $classFilterClasses,
                                'Offset' => $offset,
                                'Limit' => $limit,
                                'Depth' => 10,
                                'IgnoreVisibility' => $ignoreVisibility );

        $subTreeIteration = $contentTreeNode->subTree( $subTreeParams );

        if ( $offset === 0 )
        {
            $subTree[] = $contentTreeNode;
        }

        $subTree = array_merge( $subTree, $subTreeIteration );
        $subTreeFetchIterationCount = count( $subTreeIteration );

        /** Optional debug output **/

        if ( $troubleshoot && $scriptVerboseLevel >= 4 )
        {
            $cli->output( "Start of Iteration Count(s): SubTreeCountIterationCount: " . $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] .", Offset: $offset, Limit: $limit, SubTreeFetchIterationCount: $subTreeFetchIterationCount \n");
        }

        if ( $troubleshoot && $scriptVerboseLevel >= 5 )
        {
            $cli->output( var_dump( $subTreeParams) );
        }

        /** Iterate over nodes **/

        while ( list( $key, $contentObject ) = each( $subTree ) )
        {
            $objectData = array();
            $excludeObjectMainNode = false;
            $status = true;

            /** Fetch object details **/
            $contentObjectID = $contentObject->attribute( 'contentobject_id' );

            $object = eZContentObject::fetch( $contentObjectID );
            $objectName = $object->name();
            $objectClassName = $object->attribute( 'class_name' );

            $objectModifiedDate = $object->attribute( 'modified' );
            $objectModifiedDateFormated = date( "m/d/Y H:i:s", $objectModifiedDate );
            $objectPublishedDate = $object->attribute( 'published' );
            $objectPublishedDateFormated = date( "m/d/Y H:i:s", $objectPublishedDate );
            $contentObjectVersion = $object->attribute( 'current_version' );
            $objectSectionID = $object->attribute( 'section_id' );
            $objectMainNodeSectionName = eZSection::fetch( $objectSectionID )->attribute( 'name' );

            $objectDataMap = $object->dataMap();
            $objectMainNode = $object->mainNode();

            if ( is_object( $objectMainNode ) )
            {
                $objectMainNodeSubtreeCount = $objectMainNode->subTreeCount();
                $objectMainNodeVisibility = $objectMainNode->attribute( 'is_hidden' );
                $objectMainNodeParentVisibility = $objectMainNode->attribute( 'is_invisible' );

                /** Test if content object tree node id exists within excluded parent node ids content tree node id path **/
                foreach( $excludeParentNodeIDs as $excludeParentNodeID )
                {
                    if ( strpos( $objectMainNode->attribute( 'path_string' ), '/' . $excludeParentNodeID . '/' ) !== false )
                    {
                        $excludeObjectMainNode = true;
                    }
                }

                /** Exclude matches from the report **/
                if ( $excludeObjectMainNode == true )
                {
                    continue;
                }

                $objectMainNodeID = $objectMainNode->attribute( 'node_id' );
                $objectMainNodeClassName = $objectMainNode->attribute( 'class_name' );
                $objectMainNodeClassIdentifier = $objectMainNode->attribute( 'class_identifier' );
                $objectMainNodePathString = $objectMainNode->attribute( 'path_string' );
                $objectMainNodeUri = $objectMainNode->attribute( 'url' );

                /** Customize content object url(s) output to be user friendly **/
                if ( $objectMainNodeUri == '' )
                {
                    $objectMainNodeAdminSiteAccessUrl = $siteNodeUrlPrefix . $siteNodeUrlHostname . '/' . 'content/view/full/' . $objectMainNodeID;
                    $objectMainNodeUrl = 'N/A';
                }
                else
                {
                    $rootContentTreeNode = eZContentObjectTreeNode::fetch( $siteContentTreeRootNode );
                    $rootContentTreeNodeUri = $rootContentTreeNode->attribute( 'url' ) . '/';
                    $objectMainNodePathStringArray = explode( '/', $objectMainNodePathString );

                    $objectMainNodeAdminSiteAccessUrl = $siteNodeUrlPrefix . $siteNodeUrlHostname . '/' . $objectMainNodeUri;

                    if ( in_array( $siteContentTreeRootNode, $objectMainNodePathStringArray )
                        && strpos( $objectMainNodeUri, $rootContentTreeNodeUri ) !== false )
                    {
                        $objectMainNodeUri = str_replace( $rootContentTreeNodeUri, '', $objectMainNodeUri );
                    }

                    eZURI::transformURI( $objectMainNodeUri, true, 'full' );
                    $objectMainNodeUrl = $objectMainNodeUri;
                }

                if ( $objectMainNode->attribute( 'depth' ) == 1 )
                {
                    $objectMainNodePath = '/';
                }
                else
                {
                    $objectMainNodePath = '/' . $objectMainNode->attribute( 'parent' )->attribute( 'url' );
                }

                /**
                    Iterate over custom report content object attributes idenfiers,
                    If object class identifier matches custom report content object attribute identifiers class idenfier
                    And if object contains the custom report content object attribute identifiers attribute identifier,
                    Then add the object attribute content to array of custom report content object attribute values to include in report
                **/

                $objectMainNodeCustomAttributeValue = array();

                foreach( $customReportContentObjectAttributesIdentifiers as $customReportContentObjectAttributesIdentifiersKey => $customReportContentObjectAttributesIdentifiersItem )
                {
                    $customReportContentObjectAttributesIdentifiersItemClassIdentifier = $customReportContentObjectAttributesIdentifiersItem[0];
                    $customReportContentObjectAttributesIdentifiersItemClassAttributeIdentifier = $customReportContentObjectAttributesIdentifiersItem[1];
                    $customReportContentObjectAttributesIdentifiersItemClassAttributeDatatypeAttributeIdentifier = $customReportContentObjectAttributesIdentifiersItem[2];

                    if ( $objectMainNodeClassIdentifier == $customReportContentObjectAttributesIdentifiersItemClassIdentifier )
                    {
                        if ( isset( $objectDataMap[ $customReportContentObjectAttributesIdentifiersItemClassAttributeIdentifier ] ) )
                        {
                            $objectDataMapAttribute = $objectDataMap[ $customReportContentObjectAttributesIdentifiersItemClassAttributeIdentifier ];
                            $objectDataMapAttributeContent = $objectDataMapAttribute->attribute( 'content' );

                            if ( $customReportContentObjectAttributesIdentifiersItemClassAttributeDatatypeAttributeIdentifier != 'content' )
                            {
                                $objectMainNodeCustomAttributeValueContent = $objectDataMapAttributeContent->attribute( $customReportContentObjectAttributesIdentifiersItemClassAttributeDatatypeAttributeIdentifier );
                            }
                            else
                            {
                                $objectMainNodeCustomAttributeValueContent = $objectDataMapAttributeContent;
                            }

                            $objectMainNodeCustomAttributeValue[ $customReportContentObjectAttributesIdentifiersKey ] = $objectMainNodeCustomAttributeValueContent;
                        }
                    }
                }

                /** Build report for object **/

                $objectData[] = $objectMainNodeID;

                $objectData[] = $contentObjectID;

                $objectData[] = $contentObjectVersion;

                if ( $objectMainNodeVisibility == 1 )
                {
                    $objectData[] = 'Hidden';
                }
                elseif ( $objectMainNodeParentVisibility == 1 )
                {
                    $objectData[] = 'Hidden By Parent';
                }
                else
                {
                    $objectData[] = 'Visible';
                }

                $objectData[] = $objectMainNodeClassName;

                $objectData[] = $objectSectionID;

                $objectData[] = $objectMainNodeSectionName;

                $objectData[] = $objectPublishedDateFormated;

                $objectData[] = $objectModifiedDateFormated;

                $objectData[] = $objectMainNodeSubtreeCount;

                $objectData[] = $objectName;

                $objectData[] = $objectMainNodePath;

                $objectData[] = $objectMainNodeAdminSiteAccessUrl;

                $objectData[] = $objectMainNodeUrl;

                /**
                    Iterate over custom report content object attribute values and include each one in report
                **/

                if ( count( $objectMainNodeCustomAttributeValue ) > 0 )
                {
                    $customReportContentObjectAttributesCsvHeaderNamesTemp = $customReportContentObjectAttributesCsvHeaderNames;

                    foreach( $objectMainNodeCustomAttributeValue as $objectMainNodeCustomAttributeValueKey => $objectMainNodeCustomAttributeValueItem )
                    {
                        unset( $customReportContentObjectAttributesCsvHeaderNamesTemp[ $objectMainNodeCustomAttributeValueKey ] );

                        $objectMainNodeCustomAttributeValueKeyNext = $objectMainNodeCustomAttributeValueKey + 1;
                        $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZero = $customReportContentObjectAttributesCsvHeaderColumnCount -1;
                        $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZeroMinusPrevious = $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZero - $objectMainNodeCustomAttributeValueKey;

                        if ( ( $objectMainNodeCustomAttributeValueKey > 1 ) )
                        {
                            for( $i = 0; $i < $objectMainNodeCustomAttributeValueKey; $i++)
                            {
                                $objectData[] = '';
                            }
                        }

                        if ( $objectMainNodeCustomAttributeValueItem != '' )
                        {
                            $objectData[] = $objectMainNodeCustomAttributeValueItem;
                        }

                        if ( $objectMainNodeCustomAttributeValueKey >= 1
                            && $objectMainNodeCustomAttributeValueKey <= $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZero
                            && $objectMainNodeCustomAttributeValueKeyNext <= $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZero
                            && !isset( $objectMainNodeCustomAttributeValue[ $objectMainNodeCustomAttributeValueKeyNext ] ) )
                        {
                            for( $i = $objectMainNodeCustomAttributeValueKey; $i < $customReportContentObjectAttributesCsvHeaderColumnCountStartingAtZero; $i++)
                            {
                                $objectData[] = '';
                            }
                        }
                    }
                }

                /** Test if report file is opened **/

                if ( !$fp )
                {
                    $cli->error( "Can not open report output file" );
                    $script->shutdown( 5 );
                }

                /** Write iteration report data to file **/

                if ( !fputcsv( $fp, $objectData, ';' ) )
                {
                    $cli->error( "Can not write to report output file" );
                    $script->shutdown( 6 );
                }
            }

            $script->iterate( $cli, $status );
        }

        /** Iterate fetch function offset and continue **/
        $offset = $offset + $subTreeFetchIterationCount;

        /** Optional debug output **/

        if ( $troubleshoot && $scriptVerboseLevel >= 4 )
        {
            $cli->output( "\n" . "End of Iteration Count(s): SubTreeCountIterationCount: " . $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] .", Offset: $offset, SubTreeFetchIterationCount: $subTreeFetchIterationCount \n");
        }
    }

    /** Iterate fetch function offset and continue **/
    $subtreeOffset = $subtreeOffset + $offset;

    /** Optional debug output **/

    if ( $troubleshoot && $scriptVerboseLevel >= 4 )
    {
        $cli->output( "* End of Content Tree Iteration: RootNodeID: $contentTreeNodeID, SubTreeCount: " . $subTreeCount .", SubtreeOffset: $subtreeOffset, subTreeCountIterationCount: " . $subTreeCountIterationCount[ $contentTreeNodeKey ]['count'] . "\n");
    }
}

/** Close report file **/

while ( $fp = each( $openedFPs ) )
{
    fclose( $fp['value'] );
}

/** Assign permissions to report file **/

chmod( $contentTreeReportCsvFilePath, 0777 );

/** Alert the user to the completion of the report generation **/

$cli->output( "\nReport generation complete! Please review the report content written to disk: $contentTreeReportCsvFilePath\n" );

/** Call for display of execution time **/

executionTimeDisplay( $srcStartTime, $cli );

/** Shutdown script **/

$script->shutdown();

?>