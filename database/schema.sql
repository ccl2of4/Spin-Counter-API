drop database if exists SPINCOUNTER;
create database SPINCOUNTER;
use SPINCOUNTER;

create table USERS (
	user_id int AUTO_INCREMENT NOT NULL,
	mac_address varchar(255) NOT NULL UNIQUE,
	username varchar(255) NOT NULL UNIQUE,
	max_spins int DEFAULT 0 NOT NULL,

	PRIMARY KEY (user_id)
);

create table GAMES (
	game_id int AUTO_INCREMENT NOT NULL,
	player1_user_id int NOT NULL,
	player2_user_id int NOT NULL,

	player1_spins int NOT NULL,
	player2_spins int NOT NULL,

	PRIMARY KEY (game_id),
	FOREIGN KEY (player1_user_id) REFERENCES USERS (user_id),
	FOREIGN KEY (player2_user_id) REFERENCES USERS (user_id)
);

create table FOLLOWERS (
	following_user_id int NOT NULL,
	followed_user_id int NOT NULL,

	PRIMARY KEY (following_user_id, followed_user_id),
	FOREIGN KEY (following_user_id) REFERENCES USERS (user_id),
	FOREIGN KEY (followed_user_id) REFERENCES USERS (user_id)
);

drop trigger if exists max_spins_trigger;

DELIMITER //
create trigger max_spins_trigger
	after insert
	on GAMES
	for each row

	BEGIN
		IF (select max_spins from USERS where user_id = NEW.player1_user_id) < NEW.player1_spins THEN
			update USERS set max_spins = NEW.player1_spins where user_id = NEW.player1_user_id;
		END IF;
		IF (select max_spins from USERS where user_id = NEW.player2_user_id) < NEW.player2_spins THEN
			update USERS set max_spins = NEW.player2_spins where user_id = NEW.player2_user_id;
		END IF;
	END;
//
DELIMITER ;

-- show tables;
-- describe USERS;
-- describe GAMES;
-- describe FOLLOWERS;