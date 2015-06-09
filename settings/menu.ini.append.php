<?php /* #?ini charset="utf8"?

# Note: The content of this menu.ini extension settings override
#       is dissabled by default as not required. This is because
#       the report module view provided by this extension is linked
#       to using the 'ezpcollectivereportsadmin' extension (which does
#       provide a top menu item for -all- our report extensions.
#       Which would make the individual menu items for each report extension
#       undesired as unessisary.
#
#       The following settings are provided as a referecne to users who
#       need a admin top menu item but do not also use the 'ezpcollectivereportsadmin'
#       extension (which would provide the admin top menu item for them). 
#

#[NavigationPart]
#Part[ezcontenttreereportnavigationpart]=Content Report

#[TopAdminMenu]
#Tabs[]=contenttreereport

#[Topmenu_contenttreereport]
#NavigationPartIdentifier=ezcontenttreereportnavigationpart
#Name=Content Report
#Tooltip=Content Report
#URL[]
#URL[default]=contenttreereport/report
#Enabled[]
#Enabled[default]=true
#Enabled[browse]=false
#Enabled[edit]=false
#Shown[]
#Shown[default]=true
#Shown[edit]=false
#Shown[navigation]=true
#Shown[browse]=true

*/ ?>