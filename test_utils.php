<?php

	include('api/utils.php');

	class UtilsTest extends PHPUnit_Framework_TestCase
	{
		public function cleanUp ()
		{
			// drop all DB entries
			$db = new Database();
			$db->query('delete from GAMES where 1;');
			$db->query('delete from FOLLOWERS where 1;');
			$db->query('delete from USERS where 1;');

			// remove data folder
			global $data_folder_path;
			system('rm -rf ' . $data_folder_path);
		}
		
		public function setUp () { $this->cleanUp(); }
		public function tearDown () { $this->cleanUp(); }

		public function testGetUserDoesntExists ()
		{
			$result = getUser('1234');
			
			$this->assertEquals(null, $result);
		}

		public function testGetUserExists ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$result = getUser($user_id_1);

			$this->assertEquals('connor', $result['username']);
			$this->assertEquals('12:34', $result['mac_address']);
			$this->assertEquals(0, $result['max_spins']);
			$this->assertEquals(0, $result['games_won']);
			$this->assertEquals(0, $result['games_lost']);
			$this->assertEquals(0, $result['games_tied']);
		}

		public function testFollowingNoOne ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('56:78', 'milou');

			$followed_users = getFollowedUsers ($user_id_1);
			$this->assertEquals(0, count($followed_users));
		}

		public function testFollowOneUser ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('56:78', 'milou');

			followUser ($user_id_1, $user_id_2);

			$followed_users = getFollowedUsers ($user_id_1);
			$this->assertEquals('milou', $followed_users[0]['username']);
		}

		public function testFollowUnfollowUser ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('56:78', 'milou');

			followUser ($user_id_1, $user_id_2);
			unfollowUser ($user_id_1, $user_id_2);

			$followed_users = getFollowedUsers ($user_id_1);
			$this->assertEquals(0, count($followed_users));
		}

		public function testFollowManyUsers ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('56:78', 'milou');
			$user_id_3 = createUser('54:78', 'milou1');
			$user_id_4 = createUser('56:88', 'milou2');
			$user_id_5 = createUser('56:70', 'milou3');
			$user_id_6 = createUser('50:78', 'milou4');

			followUser ($user_id_1, $user_id_2);
			followUser ($user_id_1, $user_id_3);
			followUser ($user_id_1, $user_id_4);
			followUser ($user_id_1, $user_id_5);
			followUser ($user_id_1, $user_id_6);

			$followed_users = getFollowedUsers ($user_id_1);
			$this->assertEquals(5, count($followed_users));
		}

		public function testManyFollowMany ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('56:78', 'milou');
			$user_id_3 = createUser('54:78', 'milou1');
			$user_id_4 = createUser('56:88', 'milou2');
			$user_id_5 = createUser('56:70', 'milou3');
			$user_id_6 = createUser('50:78', 'milou4');

			followUser ($user_id_1, $user_id_2);
			followUser ($user_id_1, $user_id_3);
			followUser ($user_id_1, $user_id_4);

			followUser ($user_id_2, $user_id_1);
			followUser ($user_id_2, $user_id_3);

			followUser ($user_id_6, $user_id_5);
			followUser ($user_id_6, $user_id_4);
			followUser ($user_id_6, $user_id_3);
			followUser ($user_id_6, $user_id_2);

			$followed_users = getFollowedUsers ($user_id_3);
			$this->assertEquals(0, count($followed_users));

			$followed_users = getFollowedUsers ($user_id_1);
			$this->assertEquals(3, count($followed_users));

			$followed_users = getFollowedUsers ($user_id_2);
			$this->assertEquals(2, count($followed_users));

			$followed_users = getFollowedUsers ($user_id_6);
			$this->assertEquals(4, count($followed_users));
		}

		public function testReportSpins ()
		{
			$user_id_1 = createUser('12:34', 'connor');

			reportSpins($user_id_1, 10);

			$user = getUser($user_id_1);
			$this->assertEquals(10, $user['max_spins']);
		}

		public function testReportGame ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('12:35', 'milou');

			reportGame($user_id_1, $user_id_2, 12, 7);

			$user_1 = getUser($user_id_1);
			$user_2 = getUser($user_id_2);

			$this->assertEquals(1, $user_1['games_won']);
			$this->assertEquals(0, $user_2['games_won']);

			$this->assertEquals(0, $user_1['games_lost']);
			$this->assertEquals (1, $user_2['games_lost']);

			$this->assertEquals(0, $user_1['games_tied']);
			$this->assertEquals(0, $user_1['games_tied']);

			$this->assertEquals(12, $user_1['max_spins']);
			$this->assertEquals(7, $user_2['max_spins']);
		}

		public function testReportGameTied ()
		{
			$user_id_1 = createUser('12:34', 'connor');
			$user_id_2 = createUser('12:35', 'milou');

			reportGame($user_id_1, $user_id_2, 12, 12);

			$user_1 = getUser($user_id_1);
			$user_2 = getUser($user_id_2);

			$this->assertEquals(1, $user_1['games_tied']);
			$this->assertEquals(1, $user_1['games_tied']);

			$this->assertEquals(12, $user_1['max_spins']);
			$this->assertEquals(12, $user_2['max_spins']);
		}
	}
?>
