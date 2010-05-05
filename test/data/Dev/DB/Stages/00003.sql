-- :STAGE 3 IRREVERSIBLE
-- :COMMENT Test table

SET NAMES UTF8;

CREATE TABLE IF NOT EXISTS test_tb (
 id int unsigned NOT NULL auto_increment,
 body TEXT NOT NULL,
 title varchar(255) NOT NULL DEFAULT '',
 date_time DATETIME ,
 type varchar(255) NOT NULL DEFAULT 'Test_DB_Prototype',
 PRIMARY KEY (id));

INSERT INTO test_tb (body, title, date_time) VALUES ('body test', 'title','2008-01-16 22:30:35');
INSERT INTO test_tb (body, title, date_time) VALUES ('test2r', 'title2', '2007-02-15 12:30:35');

-- :REVERSE
DROP TABLE test_tb;
-- :END