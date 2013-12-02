{nocache}
<div class="welcome-wrapper alert alert-info">
	<span>Welcome <strong>{$firstName} {$lastName}</strong></span>
	<span class="pull-right">{$deptName} Department</span>
</div>
{/nocache}
<div id="main-wrapper">
	<div id="employee-table-wrapper">
		<div id="control-panel">
			<div id="table-panel" class="col-md-4 table-panel"></div>
			<div id="pagination-wrapper" class="col-md-8 pagination-wrapper"></div>
		</div>
		<div id="employee-table"></div>
	</div>
	<div class="modal fade" id="user-details-modal" tabindex="-1" role="dialog" aria-labelledby="model-title" aria-hidden="true"></div>
</div>