<?php
	include('database.php');

	if (!isset($_POST['player1_user_id']) ||
		!isset($_POST['player2_user_id']) ||
		!isset($_POST['player1_spins']) ||
		!isset($_POST['player2_spins'])) {
		http_response_code(400);
		exit;
	}
    
	$player1_user_id = @$_POST['player1_user_id'];
	$player2_user_id = @$_POST['player2_user_id'];
	$player1_spins = @$_POST['player1_spins'];
	$player2_spins = @$_POST['player2_spins'];

	$db = new Database();

	// insert a game
	$insert = "insert into GAMES(player1_user_id,player2_user_id,player1_spins,player2_spins) values({$player1_user_id},{$player2_user_id},{$player1_spins},{$player2_spins});";

	$result = $db->query($insert);
?>
