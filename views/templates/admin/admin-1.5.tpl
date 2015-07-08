<h2>{$module_name} {$version}</h2>
{$debug}
{if isset($download_link)}
    <div class="conf confirm alert alert-success">
        {l s='Export completed. Download link: ' mod='exportcustomers'}{$download_link}
    </div>
{/if}
<style type="text/css">
    .nav-tabs li {
        float:left;
        display: block;
        background-color: gray;
        border: 1px solid black;
        padding: 5px;
        margin: 10px;
    }
    .topButton {
        display: block;
        background-color: gray;
        border: 1px solid black;
        padding: 5px;
        margin: 10px;
        width: 60px;
    }
</style>
<div class="panel">
    <ul id="tabExportCustomers" class="nav nav-tabs">
        <li class=""><a href="#generell">Generell</a></li>
        <li class=""><a href="#customer-fields">Customer Fields</a></li>
        <li class=""><a href="#address-fields">Address Fields</a></li>
        <li class=""><a href="#special-fields">Special Fields</a></li>
        <li class=""><a href="#positions">Positions</a></li>
    </ul>
    <div style="clear:both;"></div>
    <div class="tab-content panel">
        <div id="generell" class="tab-pane active">{$generell_content}</div>
        <a href="#tabExportCustomers" class="topButton">To Top</a>
        <div id="customer-fields" class="tab-pane">{$customer_fields_content}</div>
        <a href="#tabExportCustomers" class="topButton">To Top</a>
        <div id="address-fields" class="tab-pane">{$address_fields_content}</div>
        <a href="#tabExportCustomers" class="topButton">To Top</a>
        <div id="special-fields" class="tab-pane">{$special_fields_content}</div>
        <a href="#tabExportCustomers" class="topButton">To Top</a>
        <div id="positions" class="tab-pane">{$positions_content}</div>
        <a href="#tabExportCustomers" class="topButton">To Top</a>
    </div>
</div>