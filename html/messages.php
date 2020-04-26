<div class='messages'>
<?php
	foreach($this->response['content']['messages'] as $message){
?>
		<div class='message_<?php echo $message['type']?>'>
			<?php echo $message['text']?>
		</div>
<?php 	
	}
?>
</div>