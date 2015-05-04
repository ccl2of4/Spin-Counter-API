test_utils:
	phpunit test_utils.php > test_utils.out 2> test_utils.err

test_api:
	phpunit test_api.php > test_api.out 2> test_api.err

clean:
	rm -f test_utils.err test_utils.out
	rm -f test_api.err test_api.out