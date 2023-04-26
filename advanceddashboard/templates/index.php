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
			<?php print_unescaped($this->inc('content/index',[
				'data'=>$data ,
				'topUsersByFilesCount' => $topUsersByFilesCount,
				'topUsersByUsedSpace' => $topUsersByUsedSpace,
				'totalSahredFiles' => $totalSahredFiles,
				'totalInternalShares'=>$totalInternalShares
				])); ?>
			
		</div>
	</div>
</div>

