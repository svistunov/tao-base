-- :STAGE 1 IRREVERSIBLE
-- :COMMENT User tables and common content tables

SET NAMES UTF8;

CREATE TABLE IF NOT EXISTS users (
  id             BIGINT UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'user id',
  email          VARCHAR(128)       NOT NULL                            COMMENT 'email',
  is_active      BOOLEAN            NOT NULL DEFAULT 0                  COMMENT 'user activity flag',
  password_md5   CHAR(32)           NOT NULL                            COMMENT 'md5 password hash',
  full_name      VARCHAR(255)       NOT NULL                            COMMENT 'full name',
  registered_at  TIMESTAMP          DEFAULT CURRENT_TIMESTAMP           COMMENT 'registration date',
  last_logged_at TIMESTAMP NULL     DEFAULT NULL                        COMMENT 'last login date',
  roles          MEDIUMINT UNSIGNED NOT NULL DEFAULT 0                  COMMENT 'user roles bitmask',
  UNIQUE KEY email_password_active (email, password_md5, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Site users';

CREATE TABLE IF NOT EXISTS users_visits (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'surrogate key',
  user_id          BIGINT UNSIGNED NOT NULL                            COMMENT 'user id',
  logged_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP           COMMENT 'visit date',
  connection_parms VARCHAR(255)    NOT NULL                            COMMENT 'user client connection parameters',
  CONSTRAINT fk_users_visits_users 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY logged_at (logged_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User visits log';

CREATE TABLE IF NOT EXISTS sessions (
  id               CHAR(32)         NOT NULL PRIMARY KEY      COMMENT 'session id',
  user_id          BIGINT UNSIGNED                            COMMENT 'user id',
  created_at       TIMESTAMP        DEFAULT CURRENT_TIMESTAMP COMMENT 'session opening date',
  connection_parms VARCHAR(255)     NOT NULL                  COMMENT 'user client connection parameters',
  KEY user_id(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session log';

CREATE TABLE IF NOT EXISTS content_log (
  id           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'surrogate key',
  user_id      BIGINT UNSIGNED  NOT NULL                            COMMENT 'user id',
  content_type TINYINT UNSIGNED NOT NULL                            COMMENT 'content type: 1-news, 2-photo, 3-video',
  action       TINYINT UNSIGNED NOT NULL                            COMMENT 'user action: 1-create, 2-update',
  content_id   BIGINT UNSIGNED  NOT NULL                            COMMENT 'content unit id',
  CONSTRAINT fk_user_action_log_users 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY user_content_type (user_id, content_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content changing log';

CREATE TABLE IF NOT EXISTS content_hits (
  session_id   CHAR(32)         NOT NULL                  COMMENT 'session id',
  content_type TINYINT UNSIGNED NOT NULL                  COMMENT 'content type: 1-news, 2-photo, 3-video',
  content_id   BIGINT UNSIGNED  NOT NULL                  COMMENT 'content unit id',
  hit_at       TIMESTAMP        DEFAULT CURRENT_TIMESTAMP COMMENT 'hit date',
  PRIMARY KEY (session_id, content_type, content_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content hits log';

CREATE TABLE IF NOT EXISTS tags (
  id            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'tag id',
  name          VARCHAR(255)     NOT NULL                            COMMENT 'tag name',
  title         VARCHAR(255)     NOT NULL                            COMMENT 'printable tag name',
  num_of_refs   BIGINT UNSIGNED  NOT NULL DEFAULT 0                  COMMENT 'number of tag references',
  semantic_type TINYINT UNSIGNED NOT NULL DEFAULT 0                  COMMENT 'semantic type for tag',
  semantic_data TEXT                                                 COMMENT 'semantic data for tag',
  UNIQUE KEY name (name),
  UNIQUE KEY title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content tags';

-- :REVERSE
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS content_hits;
DROP TABLE IF EXISTS content_log;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS users_visits;
DROP TABLE IF EXISTS users;
-- :END
