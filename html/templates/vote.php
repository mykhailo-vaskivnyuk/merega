<?php
$content_data = $this->response['content']['data'];
$form_data = $content_data['vote'];
?>
		<div class='form with_cboxes'>
<?php
		foreach($form_data['members'] as $member){ ?>
			<form method='post' class='with_cboxes' action='<?php echo $member['action']?>'>
				<div style='float: right; padding: 7px 0 0 5px'>
					<input name='voice' type='checkbox' title='' style='' <?php echo $member['voice']?> />
				</div>
				<label><?php echo $member['voices']?></label>
				<input name='' type='text' class='disabled' readonly
					value='<?php echo  $member['name']?>'/>
				<div style='clear: right'></div>
				<input type='hidden' name='operation' value='voice_set'>
			</form>
<?php
		} ?>
		</div>