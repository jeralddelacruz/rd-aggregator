<div class="setting-menu js-right-sidebar d-none d-lg-block">
	<div class="account-dropdown__body">
	<?php foreach($USER_MENU_ARR as $key => $arr) : ?>
		<?php if (!(in_array($key,array_keys($USER_MENU)) || in_array($key,$TAG_ARR))){ continue; } ?>

		<?php if ($arr[3] == 'false'){ continue; } ?>
			<div class="account-dropdown__item">
				<a href="index.php?cmd=<?=$key;?>">
					<i class="<?=$arr[1];?>"></i>
					<?=$arr[0];?>
				</a>
			</div>
		<?php endforeach; ?>
		
		<div class="account-dropdown__item">
			<a href="./">
				<i class="fa fa-sign-out"></i> Sign Out
			</a>
		</div>
	</div>
</div>