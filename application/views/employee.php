{nocache}
<div class="welcome-wrapper alert alert-info">
	<span>Welcome <strong>{$firstName} {$lastName}</strong></span>
	<a href="/logout" class="logout">Logout</a>
	<span class="pull-right">Department of {$deptName}</span>
</div>
<script>var user_id = '{$userId}'</script>
{/nocache}

<div id="error-message"></div>
<div id="user-details-wrapper"></div>