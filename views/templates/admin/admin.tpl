{**
* 2015 Madman
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*  @author Madman
*  @copyright  2015 Madman
*  @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**}

<h2>{$module_name} {$version}</h2>
{$debug}
{if isset($download_link)}
	<div class="conf confirm alert alert-success">
		{l s='Export completed. Download link: ' mod='exportcustomers'}{$download_link}
	</div>
{/if}
<div class="panel">
	<ul id="tabExportCustomers" class="nav nav-tabs">
		<li class="active"><a href="#generell">Generell</a></li>
		<li class=""><a href="#customer-fields">Customer Fields</a></li>
		<li class=""><a href="#address-fields">Address Fields</a></li>
		<li class=""><a href="#special-fields">Special Fields</a></li>
		<li class=""><a href="#positions">Positions</a></li>
	</ul>
	<div class="tab-content panel">
		<div id="generell" class="tab-pane active">{$generell_content}</div>
		<div id="customer-fields" class="tab-pane">{$customer_fields_content}</div>
		<div id="address-fields" class="tab-pane">{$address_fields_content}</div>
		<div id="special-fields" class="tab-pane">{$special_fields_content}</div>
		<div id="positions" class="tab-pane">{$positions_content}</div>
	</div>
	<script>
		$('#tabExportCustomers a').click(function (e) {
			e.preventDefault()
			$(this).tab('show')
		})
	</script>
</div>