{nocache}
<div class="welcome-wrapper alert alert-info">
	<span>Welcome <strong>{$firstName} {$lastName}</strong></span>
	<a href="/logout" class="logout">Logout</a>
	<span class="pull-right">Department of {$deptName}</span>
	<span class="profile-me"><img class="img-circle" src="/images/photo.png" alt="User Pic">My Info</span>
</div>
<script>var user_id = '{$userId}'</script>
{/nocache}
<div id="error-message"></div>
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