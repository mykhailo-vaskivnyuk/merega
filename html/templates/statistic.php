<?php
$forms = $this->response['content']['data']; ?>
	<div class='notification'>
			<div style='padding: 10px'>
				<?php echo $forms['statistic']['text']?>
			</div>
<!--
			<div style='padding: 0 20px 10px'>
				<form class='' style='' action = '<?php echo $forms['statistic']['action']?>'>
					<button type='submit'>ОК</button>
				</form>
				<div style='clear: right'></div>			
			</div>	
-->
	</div>
		<form method='post' style='' action='<?php echo $forms['statistic']['action']?>'>
			<button type='submit'>ОК</button>
		</form>
<?php
	if($forms['dislike']){ ?>
		<form method='post' style='margin-top: 0' action='<?php echo $forms['dislike']['action']?>'>
			<input name='operation' type='hidden' value='dislike'/>
			<input name='dislike' type='hidden' value='<?php echo $forms['dislike']['dislike']?>'/>
			<button type='submit'><?php echo $forms['dislike']['button_text']?></button>
		</form>	
<?php
	} ?>