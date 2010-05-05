 CREATE TABLE IF NOT EXISTS `db_table` (
   id int unsigned NOT NULL auto_increment,
   body TEXT NOT NULL,
   title varchar(255) NOT NULL DEFAULT '',
   date_time DATETIME ,
   __class varchar(255) NOT NULL DEFAULT 'Test_DB_Prototype',
   PRIMARY KEY (id));