<?php

	require ('api/credentials.php');

	/**
	*
	* Functional tests for all api calls
	*
	*/
	class APITest extends PHPUnit_Framework_TestCase
	{
		public function cleanUp ()
		{
            		global $db_username;
            		global $db_password;

			system("mysql -u {$db_username} -p{$db_password} < database/schema.sql");
		}

		public function setUp () { $this->cleanUp(); }
		public function tearDown () { $this->cleanUp(); }

		public function testPostUser ()
		{
			// user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(201, $http_code);
			$this->assertEquals('connor', $obj['username']);
			$this->assertEquals('12', $obj['mac_address']);
		}

		public function testPostUserUsernameTaken ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// user 2
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=15&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(409, $http_code);
			$this->assertNull($obj);
		}

		public function testPostUsername ()
		{
			// user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// change username
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/username.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(200, $http_code);
			$this->assertNull($obj);
		}

		public function testPostUsernameUsernameTaken ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// user 2
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=15&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// change username
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/username.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(409, $http_code);
			$this->assertNull($obj);
		}

		public function testPostFollow ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_1 = json_decode($json, true)['user_id'];

			// user 2
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=15&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_2 = json_decode($json, true)['user_id'];

			// follow
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/follow.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "following_user_id={$user_id_1}&followed_user_id={$user_id_2}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(201, $http_code);
			$this->assertNull($obj);
		}

		public function testGetFollowers ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_1 = json_decode($json, true)['user_id'];

			// user 2
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=15&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_2 = json_decode($json, true)['user_id'];

			// user 3
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=27&username=jack");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_3 = json_decode($json, true)['user_id'];

			// follow
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/follow.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "following_user_id={$user_id_1}&followed_user_id={$user_id_2}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// follow
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/follow.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "following_user_id={$user_id_1}&followed_user_id={$user_id_3}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);

			// get followed users
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/followedusers.php?user_id={$user_id_1}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(200, $http_code);
			$this->assertEquals(2, count($obj));
		}

		public function testGetLeaderboard ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_1 = json_decode($json, true)['user_id'];

			// user 2
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=15&username=charles");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_2 = json_decode($json, true)['user_id'];

			// user 3
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=27&username=jack");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_3 = json_decode($json, true)['user_id'];

			// get leaderboard
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/leaderboard.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(200, $http_code);
			$this->assertEquals(3, count($obj));
		}

		public function testPostSpin ()
		{
			// user 1
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/signup.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&username=connor");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$user_id_1 = json_decode($json, true)['user_id'];

			// spins
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/spin.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "mac_address=12&spins=15");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$this->assertEquals(200, $http_code);

			// get user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost/api/user.php?mac_address=12");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$obj = json_decode($json, true);

			$this->assertEquals(200, $http_code);
			$this->assertEquals(15, $obj['max_spins']);
		}
	}
?>
