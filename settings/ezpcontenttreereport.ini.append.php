<?php /* #?ini charset="utf8"?

[eZPContentTreeReportSettings]
# Required Setting: Iteration content tree object fetch limit. This is the number of object to fetch at a time (not total).
# This setting can be used to reduce the performance cost (execution time) when running the database query to fetch iteration content objects.
# The lower this setting is the longer the script may take to execute as more iterations may be required on larger databases with more content tree objects.
# For database servers with more memory, cpu, etc feel free to increase this setting value to a larger number as desired which will fetch more content object per iteration and thus the script will execute in less time.
IterationFetchLimit=750

# Here are examples less costly limit setting values which *could* be used. Please use lower setting values *only* if absolutely required.
# IterationFetchLimit=350
# IterationFetchLimit=500

# Required Setting: Admin Siteccess name
AdminSiteAccessName=site_admin

# Required Setting: User Siteccess name
UserSiteAccessName=site

# Required Setting: Node Url Protocol Prefix. This can be changed to say https:// as needed.
UrlProtocolPrefix=http://

# Required Setting: Default Admin UserID. This is required to allow report generation script to run will full admin role privileges.
DefaultAdminUserID=14

# Required Setting: CSV Header of fields to include in the report
# This setting may be customized as needed (IE: Rename or Reorder fields but not remove fields (without altering the commandline script)).
# *But* if you wish to add additional fields to the report, then you need not alter this setting. Instead you should only need to customize the 'ContentObjectAttributes[]' setting instead.
CsvHeader[]
CsvHeader[]=NodeID
CsvHeader[]=ContentObjectID
CsvHeader[]=Version
CsvHeader[]=Visibility
CsvHeader[]=Content Class
CsvHeader[]=SectionID
CsvHeader[]=Section Name
CsvHeader[]=Published Date
CsvHeader[]=Modified Date
CsvHeader[]=Node Children Count
CsvHeader[]=Node Name
CsvHeader[]=Content Tree Path
CsvHeader[]=Node Url
CsvHeader[]=Site Url

# Required Setting: Content Tree Root NodeIDs to include in report
ContentTreeNodeIDs[]
ContentTreeNodeIDs[]=2
ContentTreeNodeIDs[]=5
ContentTreeNodeIDs[]=43

# Optional Setting: Parent NodeIDs to exclude from report
ExcludedParentNodeIDs[]
# ExcludedParentNodeIDs[]=5

# Optional Setting: include or exclude Content Class Idenfiers in the ClassFilterArray setting (below) from report
ClassFilterType=exclude

# Optional Setting: Content Class Idenfiers to include or exclude from report
ClassFilterArray[]
# ClassFilterArray[]=user
# ClassFilterArray[]=file
# ClassFilterArray[]=image

# Optional Setting: Exclude Hidden nodes from report.
ExcludeHiddenNodes=disabled

# Optional Setting: Custom Content Object Attributes to include in report. Disabled by default
# Here is a simple example of the format expected / required for use within the `ContentObjectAttributes[]` setting array content:
# FORMAT EXAMPLE ONLY : ContentObjectAttributes[]=class_identifier;class_attribute_identifier;datatype_attribute_identifier;CSV Header Text Description

ContentObjectAttributes[]

# Here are a few example usages of the `ContentObjectAttributes[]` setting array content
# This first example is to include the file class, file attribute, original_filename attribute content aka 'Uploaded filename'.
# ContentObjectAttributes[]=file;file;original_filename;File Filename
# This second example is to include the image class, image attribute, original_filename attribute content aka 'Uploaded image filename'.
# ContentObjectAttributes[]=image;image;original_filename;Image Filename
# This third example is to include the folder class, forward attribute, content attribute content aka 'Checkbox content value (checked or not checked; one or zero)'.
# ContentObjectAttributes[]=folder;forward;content;Forward to CustomResource
# This fourth example is to include the folder class, forwarding_path attribute, content attribute content aka 'Textline (ezstring datatype content value string)'.
# ContentObjectAttributes[]=folder;forwarding_path;content;CustomResource Forwarding Path

*/ ?>