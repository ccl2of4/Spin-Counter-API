<?php
	include('utils.php');

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

	reportGame($player1_user_id, $player2_user_id, $player1_spins, $player2_spins);
	http_response_code(201);
?>
