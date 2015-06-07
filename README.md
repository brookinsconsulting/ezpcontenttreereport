eZp Content Tree Report
===================

This extension which provides a flexible solution which provides a quick and simple report of content tree content objects content in csv format.


Version
=======

* The current version of eZp Content Tree Report is 0.1.4

* Last Major update: June 06, 2015


Copyright
=========

* eZp Content Tree Report is copyright 1999 - 2016 Brookins Consulting and 2013 - 2016 Think Creative

* See: [COPYRIGHT.md](COPYRIGHT.md) for more information on the terms of the copyright and license


License
=======

eZp Content Tree Report is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE) file.

eZp Content Tree Report is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

eZp Content Tree Report is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
eZp Content Tree Report under certain conditions. The GNU GPL license
is distributed with the software, see the file doc/LICENSE.

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with eZp Content Tree Report in doc/LICENSE.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using eZp Content Tree Report under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


Requirements
============

The following requirements exists for using eZp Content Tree Report extension:


### eZ Publish version

* Make sure you use eZ Publish version 5.x (required) or higher.

* Designed and tested with eZ Publish Platform 5.4


### PHP version

* Make sure you have PHP 5.x or higher.


Features
========

This solution provides the following features:

* Command line script

* Cronjob

* Module view


Dependencies
============

This solution depends on eZ Publish Legacy only


Installation
============

### Bundle Installation via Composer

Run the following command from your project root to install the bundle:

    bash$ composer require brookinsconsulting/ezpcontenttreereport dev-master;


### Extension Activation

Activate this extension by adding the following to your `settings/override/site.ini.append.php`:

    [ExtensionSettings]
    # <snip existing active extensions list />
    ActiveExtensions[]=ezpcontenttreereport


### Clear the caches

Clear eZ Publish Platform / eZ Publish Legacy caches (Required).

    php ./bin/php/ezcache.php --clear-all;


Settings Customization
===================================

This extension provides a number of settings which affect the report generation process.

First create a settings override (global, siteaccess or extension) of file `ezpcontenttreereport.ini.append.php`.

Then customize the settings as required.

## Required settings

This solution only requires one setting be customized, `AdminUserSiteAccessName`.

You are required to set your admin siteaccess name within the `AdminUserSiteAccessName` setting variable.

This is required because the solution uses this content to run the report generation using the admin siteaccess scope which again is required for the entire solution to work correctly.

## Optional settings

You can customize the Content Tree Nodes to include in the report by configuring the `ContentTreeNodeIDs[]` setting array content.

You can exclude content tree subtrees from the report by configuring the `ExcludedParentNodeIDs[]` setting array content.

You can include or exclude content classes by configuring the `ClassFilterType` setting variable and `ClassFilterArray[]` setting array content.

You can exclude hidden nodes from the report by configuring the `ExcludeHiddenNodes` setting variable.

You can customize the report generated to include additional custom configured content object attribute content by configuring the `ContentObjectAttributes[]` setting array content.

Here is a simple example of the format expected / required for use within the `ContentObjectAttributes[]` setting array content:

    ContentObjectAttributes[]=class_identifier;class_attribute_identifier;datatype_attribute_identifier;CSV Header Text Description

Here are a few example usages of the `ContentObjectAttributes[]` setting array content:

    ContentObjectAttributes[]=folder;forward;content;Forward to Legacy / DocStudio
    ContentObjectAttributes[]=folder;forwarding_path;content;DocStudio Forwarding Path
    ContentObjectAttributes[]=file;file;original_filename;File Filename
    ContentObjectAttributes[]=image;image;original_filename;Image Filename

Note: Content class, Content class attribute and Datatype attribute identifiers must only be lower case but the CSV Header Text Description can be in mixed case (and probably should be since the rest of the csv header fields also used First char upper case mixed case text strings).

### `ContentObjectAttributes[]` Attribute usage reference documentation

You can use any attribute / datatype possible using this settings.

Here is some documentation related to the usage of the above settings values within the content objects and datatypes (re: identifiers).

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobjecttreenode](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobjecttreenode)

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobject](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobject)

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobjectattribute](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezcontentobjectattribute)

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezbinaryfile](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezbinaryfile)

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezimagealiashandler](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects/ezimagealiashandler)

* [https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/Reference/Objects)


Usage
=====

The solution is configured to work virtually by default once properly installed.


Usage - Command line script
============

Note: This script must be run using **only** the admin siteaccess!

Change directory into eZ Publish website document root:

    cd path/to/ezpublish/ezpublish_legacy/;

Run the script to generate the report

    php ./extension/ezpcontenttreereport/bin/php/ezpcontenttreereport.php -s site_admin;

Review generated report in LibreOffice as a spreadsheet:

    less var/site/cache/ezpcontenttreereport_-_2015_06_06_-_09_02_43.csv;


Usage - Cronjob
============

Change directory into eZ Publish website document root:

    cd path/to/ezpublish/ezpublish_legacy/;

Run the cronjob manually to generate the report

    php ./runcronjobs.php ezpcontenttreereportgenerate;

Review generated report in LibreOffice as a spreadsheet:

    less var/site/cache/ezpcontenttreereport_-_2015_06_06_-_09_02_43.csv;


Usage - Module
============

The module view is optional but often the default way content editor admins use this solution

The module view can be used for simple regeneration of report and downloading of report

Access the module view using the following uri

http://admin.example.com/contenttreereport/report


Troubleshooting
===============

### Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md)


### Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)

