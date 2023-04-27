<?php
script('advanceddashboard', 'script');
style('advanceddashboard', 'style');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<div>
				<?php print_unescaped($this->inc('content/show',['active_users'=>$active_users])); ?>			
				<br>
				<div class="daterangeinputs">
					<form method="POST" action="/apps/advanceddashboard/usersactive/active">
						<span>
							<label for="start_date">Start date:</label>
							<input type="datetime-local" id="start_date" name="start_date">
						</span>
						<span>
							<label for="end_date">End date:</label>
							<input type="datetime-local" id="end_date" name="end_date">
						</span>
						<button type="submit">Submit</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>