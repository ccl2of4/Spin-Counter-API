<?php
	require('database.php');

	/**
	* Gets relevant information for a user
	*
	* @param string $username the username of the user to retrieve
	* @return associative array with user's information, or null if no such user exists
	*/
	function getUserByUsername ($username)
	{
		$db = new Database();
		$select = "select user_id from USERS where username = '{$username}';";
		$result = $db->query($select);
		if ($row = mysqli_fetch_assoc($result)) {
			return getUser($row['user_id']);
		}
		return null;
	}

	/**
	* Gets relevant information for a user
	*
	* @param string $mac_address the mac address of the user to retrieve
	* @return associative array with user's information, or null if no such user exists
	*/
	function getUserByMacAddress ($mac_address)
	{
		$db = new Database();
		$select = "select user_id from USERS where mac_address = '{$mac_address}';";
		$result = $db->query($select);
		if ($row = mysqli_fetch_assoc($result)) {
			return getUser($row['user_id']);
		}
		return null;
	}

	/**
	* Gets relevant information for a user
	*
	* @param int $user_id the id of the user to retrieve
	* @return associative array with user's information, or null if no such user exists
	*/
	function getUser ($user_id)
	{
		$db = new Database();
		$select = "select * from USERS where user_id = {$user_id}";
		$result = $db->query($select);

		// the user table doesn't have win/loss/tie data, so that has to be obtained dynamically
		if($row = mysqli_fetch_assoc($result)) {	
			
			$wins = getWinsForUser($user_id);
			$row['games_won'] = $wins;

			$losses = getLossesForUser($user_id);
			$row['games_lost'] = $losses;

			$ties = getTiesForUser($user_id);
			$row['games_tied'] = $ties;

		}
		return $row;
	}

	/**
	* Gets the number of wins for the given user
	*
	* @param int $user_id the id of the user
	* @return int, or null if no data for the user could be found
	*/
	function getWinsForUser ($user_id)
	{
		$db = new Database();
		$select = "select count(*) from GAMES where (player1_user_id = {$user_id} and player1_spins > player2_spins) or (player2_user_id = {$user_id} and player2_spins > player1_spins);";
		$result = $db->query($select);
		if ($wins =  mysqli_fetch_array($result)) {
			return $wins[0];
		}
		return null;
	}

	/**
	* Gets the number of losses for the given user
	*
	* @param int $user_id the id of the user
	* @return int, or null if no data for the user could be found
	*/
	function getLossesForUser ($user_id)
	{
		$db = new Database();
		$select = "select count(*) from GAMES where (player1_user_id = {$user_id} and player1_spins < player2_spins) or (player2_user_id = {$user_id} and player2_spins < player1_spins);";
		$result = $db->query($select);
		if ($losses =  mysqli_fetch_array($result)) {
			return $losses[0];
		}
		return null;
	}

	/**
	* Gets the number of ties for the given user
	*
	* @param int $user_id the id of the user
	* @return int, or null if no data for the user could be found
	*/
	function getTiesForUser ($user_id)
	{
		$db = new Database();
		$select = "select count(*) from GAMES where (player1_user_id = {$user_id} or player2_user_id = {$user_id}) and player2_spins = player1_spins;";
		$result = $db->query($select);
		if ($ties =  mysqli_fetch_array($result)) {
			return $ties[0];
		}
		return null;
	}

	/**
	* Search for users that match the given query
	* 
	* Example:
	* given these users exist:
	*  joe, john, johnny
	*
	* the query "jo" will yield [joe, john, johnny]
	* the query "joe" will yield [joe]
	* the query "john" will yeild [john, johnny]
	* the query "johhny" will yield [johhny]
	* the query "joo" will yield []
	*
	* @param string $query the query to search against
	* @return array of associative arrays each equivalent to that return by getUser
	*/
	function searchUsers ($query)
	{
		$db = new Database();
		$select = "select user_id from USERS where username like '{$query}%';";
		$result = $db->query($select);

		$array = array();
		while($row = mysqli_fetch_assoc($result)) {
			$user_id = $row['user_id'];
			$array[] = getUser($user_id);
		}
		return $array;
	}

	/**
	* Gets the leaderboard (top 100 users ordered by max_spins)
	* 
	* @return array of associative arrays each equivalent to that returned by getUser
	*/
	function getLeaderboard ()
	{
		$db = new Database();
		$select = "select user_id from USERS order by max_spins desc limit 100;";
		$result = $db->query($select);

		$array = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$user_id = $row['user_id'];
			$array[] = getUser($user_id);
		}

		return $array;
	}

	/**
	* Gets the users that the given user is following
	*
	* @param $user_id the user whose followers to search for
	* @return array of associative arrays each equivalent to that returned by getUser
	*/
	function getFollowedUsers ($user_id)
	{
		$db = new Database();
		$select = "select user_id from USERS where user_id in (select followed_user_id from FOLLOWERS where following_user_id = {$user_id});";
		$result = $db->query($select);

		$array = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$user_id = $row['user_id'];
			$array[] = getUser($user_id);
		}
		
		return $array;
	}

	/**
	* Try to create a user with the given credentials. If the mac_address is already
	* being used by another user, or if the username is taken, the creation will fail
	*
	* @param string $mac_address the mac_address to be associated with the user
	* @param $username the username wanted
	* @return the user_id of the created user if the user was created succesfully,
	* null otherwise
	*/
	function createUser ($mac_address, $username)
	{
		$db = new Database();

		$insert = "insert into USERS(mac_address,username) values('{$mac_address}','{$username}');";

		// if the mac address or username are taken, the query will return false
		if(!$db->query($insert)) {
			return null;
		}

		$select = "select user_id from USERS where username = '{$username}'";
		return mysqli_fetch_assoc($db->query($select))['user_id'];
	}

	/**
	* Changes the username for the given user
	*
	* @param int $user_id the user id of the user
	* @param string $new_username the new username for the user
	* @return true if the username can be changed, false otherwise
	*/
	function changeUsername ($user_id, $new_username)
	{
		$db = new Database();
		$update = "update USERS set username = '{$new_username}' where user_id = {$user_id};";

		// if the username is taken then this will return false
		return $db->query($update);
	}

	/**
	* Create a following relationship between two users
	*
	* @param int $following_user_id id of the user doing the following
	* @param int $followed_user_id id of the user being followed
	*/
	function followUser ($following_user_id, $followed_user_id)
	{
		$db = new Database();
		$insert = "insert into FOLLOWERS(following_user_id, followed_user_id) values({$following_user_id},{$followed_user_id});";
		$result = $db->query($insert);
	}

	/**
	* Destroy the existing following relationship between two users
	*
	* @param int $unfollowing_user_id
	* @param int $unfollowed_user_id
	*/
	function unfollowUser ($unfollowing_user_id, $unfollowed_user_id)
	{
		$db = new Database();
		$delete = "delete from FOLLOWERS where following_user_id = {$unfollowing_user_id} and followed_user_id = {$unfollowed_user_id};";
		$result = $db->query($delete);
	}

	/**
	* Checks if $spins is greater than the user's max spins. If so,
	* updates accordingly
	*
	* @param int $user_id the id of the user who spun
	* @param int $spins the number of spins to report
	*/
	function reportSpins ($user_id, $spins)
	{
		$db = new Database();
		$user = getUser($user_id);
		if ($user['max_spins'] < $spins) {
			$update = "update USERS set max_spins = {$spins} where user_id = {$user_id};";
			$result = $db->query($update);
		}
	}

	/**
	* Adds an entry in the DB for the game. Calls reportSpins
	* for both users using the provided spins
	*
	* @param int $player1_user_id
	* @param int $player2_user_id
	* @param int $player1_spins
	* @param int $player2_spins
	*/
	function reportGame (
		$player1_user_id,
		$player2_user_id,
		$player1_spins,
		$player2_spins)
	{
		$db = new Database();
		$insert = "insert into GAMES(player1_user_id, player2_user_id, player1_spins, player2_spins) values({$player1_user_id},{$player2_user_id},{$player1_spins},{$player2_spins});";
		$result = $db->query($insert);
	}
?>
