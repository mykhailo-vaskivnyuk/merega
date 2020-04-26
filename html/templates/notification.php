<?php
if($this->response['content']['data']['notifications']){
	foreach($this->response['content']['data']['notifications'] as $notification){
?>
		<div class='notification'>
			<div style='padding: 10px'>
				<?php echo $notification['text'] ?>
			</div>
			<div style='padding: 0 20px 10px'>
				<form method='post' class='' style='' action = '<?php echo $notification['action'] ?>'>
					<input type='hidden' name='notification_id' value='<?php echo $notification['id'] ?>'>
					<input type='hidden' name='operation' value='view'>
					<button type='submit'>ОК</button>
				</form>
<?php
				if($notification['close']){
?>
					<form method='post' class='' style='' action = '<?php echo $notification['action'] ?>'>
						<input type='hidden' name='notification_id' value='<?php echo $notification['id'] ?>'>
						<input type='hidden' name='operation' value='close'>
						<button type='submit'>X</button>
					</form>
<?php
				}
?>
				<div style='clear: right'></div>			
			</div>
		</div>
<?php
	}
}
?>