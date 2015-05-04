<?php
	include('database.php');

	if (!isset($_GET['user_id']) {
		http_response_code(400);
		exit;
	}
    
	$user_id = @$_GET['user_id'];

	$db = new Database();

	// find all followed users
	$users = "select * from USERS where user_id in (select followed_user_id from FOLLOWERS where following_user_id = {$user_id});";
	$result = $db->query($users);
	$json_array = array();

	// this is copy/pasted from leaderboard.php
	while($row = mysqli_fetch_array($result)) {
		$followed_user_id = $row['user_id'];
		
		// get wins
		$wins = "select count(*) from GAMES where (player1_user_id = {$followed_user_id} and player1_spins > player2_spins) or (player2_user_id = {$followed_user_id} and player2_spins > player1_spins);";
		$wins_result = $db->query($wins);
		$wins = mysqli_fetch_array($wins_result)[0];
		$row['games_won'] = $wins;

		// get losses
		$losses = "select count(*) from GAMES where (player1_user_id = {$followed_user_id} and player1_spins < player2_spins) or (player2_user_id = {$followed_user_id} and player2_spins < player1_spins);";
		$losses_result = $db->query($losses);
		$losses = mysqli_fetch_array($losses_result)[0];
		$row['games_lost'] = $losses;

		// ties
		$ties = "select count(*) from GAMES where (player1_user_id = {$followed_user_id} or player2_user_id = {$followed_user_id}) and player2_spins = player1_spins;";
		$ties_result = $db->query($ties);
		$ties = mysqli_fetch_array($ties_result)[0];
		$row['games_tied'] = $ties;

		$json_array[] = $row;
	}

	echo json_encode($json_array);
?>
