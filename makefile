test_utils:
	phpunit test_utils.php > test_utils.out 2> test_utils.err

test_api: deploy
	phpunit test_api.php > test_api.out 2> test_api.err

deploy: clean_deploy
	sudo mkdir /var/www/html/api/
	sudo cp -r api/* /var/www/html/api/
	sudo cp -r html/* /var/www/html/

clean_deploy:
	sudo rm -rf /var/www/html/*

clean:
	rm -f test_utils.err test_utils.out
	rm -f test_api.err test_api.out

phpdoc:
	rm -rf docs/
	mkdir docs/
	phpdoc -d api/ -t docs/
