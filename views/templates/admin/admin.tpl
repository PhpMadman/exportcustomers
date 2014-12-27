<h2>{$module_name} {$version}</h2>
{$debug}
<div class="panel">
	<ul id="tabExportCustomers" class="nav nav-tabs">
		<li class="active"><a href="#generell">Generell</a></li>
		<li class=""><a href="#customer-fields">Customer Fields</a></li>
		<li class=""><a href="#address-fields">Address Fields</a></li>
		<li class=""><a href="#positions">Positions</a></li>
	</ul>
	<div class="tab-content panel">
		<div id="generell" class="tab-pane active">{$generell_content}</div>
		<div id="customer-fields" class="tab-pane">{$customer_fields_content}</div>
		<div id="address-fields" class="tab-pane">{$address_fields_content}</div>
		<div id="positions" class="tab-pane">{$positions_content}</div>
	</div>
	<script>
		$('#tabExportCustomers a').click(function (e) {
			e.preventDefault()
			$(this).tab('show')
		})
	</script>
</div>