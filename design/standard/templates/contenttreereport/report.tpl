<form name="eZPContentTreeReport" method="post" action={'contenttreereport/report'|ezurl}>

<div class="context-block">

    {* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

    <h1 class="context-title">{'Content Tree Report'|i18n('design/standard/contenttreereport')}</h1>

    {* DESIGN: Mainline *}<div class="header-mainline"></div>

    {* DESIGN: Header END *}</div></div></div></div></div></div>

    {* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

    {* DESIGN: Content END *}</div></div></div>

    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
        <div class="block">
            {if $status}
                <p><span style="color: green">Report Generated!</span> Created: <span style="color: red">{$fileModificationTimestamp}</span></p>{/if}
                <input class="button" name="Generate" type="submit" value="{'Regenerate Report'|i18n('design/standard/contenttreereport')}" />
            {if $status}
                <input class="button" name="Download" type="submit" value="{'Download Report'|i18n('design/standard/contenttreereport')}" />
            {/if}
        </div>
    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>

<span class="emptyContentTreeReportDownloadInstructionsBrief">
<style>
{literal}
.emptyContentTreeReportDownloadOfficeUsageInstructions
{
    display:none;
    margin-bottom:20px;
}
{/literal}
</style>
<script type="text/javascript">
{literal}
$(document).ready(function() {
    $('.showEmptyContentTreeReportDownloadOfficeUsageInstructions').click(function() {
            $('.emptyContentTreeReportDownloadOfficeUsageInstructions').slideToggle("fast");
    });
});
{/literal}
</script>

<p>For step by step instructions on how to import the exported Content Tree Report csv file(s) into Microsoft Office Excel. Click to <a class="showEmptyContentTreeReportDownloadOfficeUsageInstructions" href="javascript:void(0)">Show / Hide</a> instructions.

<div class="emptyContentTreeReportDownloadOfficeUsageInstructions">

<p>Step 1: Save the Content Tree Report to your computer.</p>

<p>Step 2: Launch Excel, open a blank workbook, click the Data menu, and select From Text (3rd button from the left).</p>

<p>Step 3: In the Import Text File window, find and select the CSV file and click the Import button. This should display the Import Wizard.</p>

<p>Step 4: The columns in the CSV file have been separated using semicolons. Select the Delimited option for "Choose the file type that best describes your data." </p>

<p>Step 5: Click the Next button.</p>

<p>Step 6: In Step 2 of Import Wizard, choose appropriate delimiter (semicolon) under Delimiters section. You can preview the selection using Data preview section.</p>

<p>Step 7: Click the Finish button in the Import Wizard.</p>

<p>Step 8: In the Import Data dialog box, select Existing worksheet and click OK.</p>

</div>

</span>

</div>

</form>