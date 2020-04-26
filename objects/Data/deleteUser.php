<?php
//public function deleteUser()
//{
		$sql = 'DELETE FROM users WHERE user_id = ' . $this->user['id'];
		$sql = $this->DB->getWork($sql);
		$sql = 'DELETE FROM users_notifications WHERE user_id = ' . $this->user['id'];
		$sql = $this->DB->getWork($sql);
//}
?>