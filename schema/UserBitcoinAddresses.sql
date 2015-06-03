-- Table definitions for "User Bitcion Addresses" MediaWiki extension.
--
-- @licence MIT License
-- @author Daniel A. R. Werner
--
-- NOTE: We stick to MW's date format [varchar(14)] instead of DATETIME
--  for sake of MW's DB abstraction.

CREATE TABLE IF NOT EXISTS /*_*/user_bitcoin_addresses (
	userbtcaddr_id             BIGINT unsigned       NOT NULL PRIMARY KEY AUTO_INCREMENT,
	userbtcaddr_user_id        INT unsigned          NOT NULL, -- points to user.user_id
	userbtcaddr_address        VARCHAR(34) binary    NOT NULL, -- BTC addresses are 25 to 34 in length
	userbtcaddr_added_through  VARCHAR(20) binary    DEFAULT NULL,
	userbtcaddr_added_on       varchar(14)           NOT NULL,
	userbtcaddr_exposed_on     varchar(14) NULL      DEFAULT NULL,
	userbtcaddr_purpose        VARCHAR(20) binary    DEFAULT NULL
) /*$wgDBTableOptions*/;
