<body>
<?php
	if($this->html){
		require_once 'main_menu.php';
		require_once 'menu.php';
		require_once 'contacts.php';
		require_once 'content.php';
		require_once 'bottom.php';
		require_once 'scripts.php';
	}

	if($this->service){
		require_once 'service.php';
	}
?>
</body>