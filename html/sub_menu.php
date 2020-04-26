		<div class='sub_menu'>
<?php		
			if($this->response['content']['data']['sub_menu']){
?>				
				<a class='active' href='<?php echo  $this->response['content']['data']['sub_menu']['link']?>'>
					<?php echo  $this->response['content']['data']['sub_menu']['text']?>
				</a>
<?php
			}
			else echo '&nbsp;'
?>
		</div>