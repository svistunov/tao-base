-- :STAGE 4 IRREVERSIBLE
-- :COMMENT Blog tables create

CREATE TABLE IF NOT EXISTS blog_users (
id               BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'user id',
name             VARCHAR(255)      NOT NULL                            COMMENT 'user name',
email            VARCHAR(255)      NOT NULL                            COMMENT 'user email',
info             TEXT                                                  COMMENT 'othe information abount user',
UNIQUE KEY name (name),
KEY name_email (name, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'Users';

CREATE TABLE IF NOT EXISTS blog_postings (
id               BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'posting id',
user_id          BIGINT UNSIGNED     NOT NULL                            COMMENT 'user id', 
title            VARCHAR(255)        NOT NULL                            COMMENT 'posting title',
published_at     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP           COMMENT 'published date',
body             TEXT                                                    COMMENT 'posting text',
CONSTRAINT fk_postings_users 
  FOREIGN KEY (user_id) REFERENCES blog_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Postings';

CREATE TABLE IF NOT EXISTS blog_tags (
id                BIGINT UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY   COMMENT 'tag id',
name              VARCHAR(255)       NOT NULL                              COMMENT 'name', 
KEY name (name) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags';

CREATE TABLE IF NOT EXISTS blog_tags_postings (
tag_id            BIGINT UNSIGNED    NOT NULL COMMENT 'tag id',
posting_id        BIGINT UNSIGNED    NOT NULL COMMENT 'posting id',
PRIMARY KEY (tag_id, posting_id),
CONSTRAINT fk_postings 
  FOREIGN KEY (posting_id) REFERENCES blog_postings(id),
CONSTRAINT fk_tags 
  FOREIGN KEY (tag_id) REFERENCES blog_tags(id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags';

-- :REVERSE
DROP TABLE IF EXISTS blog_tags_postings;
DROP TABLE IF EXISTS blog_tags;
DROP TABLE IF EXISTS blog_postings;
DROP TABLE IF EXISTS blog_users;
-- :END

