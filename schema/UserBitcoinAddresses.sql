CREATE TABLE IF NOT EXISTS /*_*/user_bitcoin_addresses (
	userbtcaddr_id             BIGINT unsigned       NOT NULL PRIMARY KEY AUTO_INCREMENT,
	userbtcaddr_user           INT unsigned          NOT NULL, -- points to user.user_id
	userbtcaddr_address        VARCHAR(34) binary    NOT NULL, -- BTC addresses are 25 to 34 in length
	userbtcaddr_added_through  VARCHAR(20) binary    DEFAULT NULL,
	userbtcaddr_added_on       DATETIME              NOT NULL,
	userbtcaddr_exposed_on     DATETIME NULL         DEFAULT NULL,
	userbtcaddr_purpose        VARCHAR(20) binary    DEFAULT NULL
) /*$wgDBTableOptions*/;
