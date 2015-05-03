<?php
	include('database.php');

	if (!isset($_POST['following_user_id']) ||
		!isset($_POST['followed_user_id']) {
		http_response_code(400);
		exit;
	}
    
	$following_user_id = @$_POST['following_user_id'];
	$followed_user_id = @$_POST['followed_user_id'];

	$db = Database::singleton();

	// create the relationship
	$insert = "insert into FOLLOWERS(following_user_id,followed_user_id) values({$following_user_id},{$followed_user_id});";

	$result = $db->query($insert);
?>
