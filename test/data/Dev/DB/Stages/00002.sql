-- :STAGE 2 IRREVERSIBLE
-- :COMMENT News service table creation
SET NAMES UTF8;

CREATE TABLE IF NOT EXISTS news_categories (
  id               SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'news category id',
  name             VARCHAR(128)      NOT NULL                            COMMENT 'category name',
  section_id       TINYINT UNSIGNED  NOT NULL                            COMMENT 'section id',
  ord              SMALLINT UNSIGNED NOT NULL DEFAULT 0                  COMMENT 'sequence number within a section',
  title            VARCHAR(255)      NOT NULL                            COMMENT 'printable category name',
  created_at       TIMESTAMP         DEFAULT CURRENT_TIMESTAMP           COMMENT 'creation date',
  update_at        TIMESTAMP NULL    DEFAULT NULL                        COMMENT 'last update date',
  has_actual_cache TINYINT UNSIGNED  NOT NULL DEFAULT 0                  COMMENT 'are cache tables for category actual?',
  UNIQUE KEY name (name),
  KEY section_ord (section_id, ord),
  KEY has_actial_cache(has_actual_cache)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'News categories';

INSERT INTO news_categories (id, name, section_id, ord, title) VALUES
  ( 1, 'workday/daily',          1, 1, 'События дня'),
  ( 2, 'workday/business',       1, 2, 'Бизнес-новости'),
  ( 3, 'workday/author',         1, 3, 'От автора'),
  
  ( 4, 'pragmatics/investments', 2, 1, 'Личные деньги'),
  ( 5, 'pragmatics/realty',      2, 2, 'Квартирный вопрос'),
  ( 6, 'pragmatics/educasion',   2, 3, 'Образование'),
  ( 7, 'pragmatics/career' ,     2, 4, 'Карьера'),
  
  ( 8, 'coffeebreak/active',     3, 1 ,'Нескучная жизнь'),
  ( 9, 'coffeebreak/science',    3, 2, 'Глас науки'),
  (10, 'coffeebreak/health',     3, 3, 'Тело и дух'),
  (11, 'coffeebreak/hitech',     3, 4, 'Хай-тек'),
  (12, 'coffeebreak/automotive', 3, 5, 'Автомобили'),
  (13, 'coffeebreak/style',      3, 6, 'Стиль');

CREATE TABLE IF NOT EXISTS news_stories (
  id               BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'story id',
  category_id      SMALLINT UNSIGNED   NOT NULL                            COMMENT 'category id', 
  status           TINYINT  UNSIGNED   NOT NULL DEFAULT 0                  COMMENT 'status: 0-draft, 1-published, 2-archive',
  pub_date         DATETIME            NOT NULL                            COMMENT 'publication date',
  importance       TINYINT UNSIGNED    NOT NULL DEFAULT 0                  COMMENT 'story importance',
  num_of_comments  MEDIUMINT UNSIGNED  NOT NULL DEFAULT 0                  COMMENT 'number of comments',
  num_of_votes     MEDIUMINT UNSIGNED  NOT NULL DEFAULT 0                  COMMENT 'number of votes for the story',
  num_of_views     BIGINT UNSIGNED     NOT NULL DEFAULT 0                  COMMENT 'number of user views',
  title            VARCHAR(255)        NOT NULL                            COMMENT 'story title',
  created_at       TIMESTAMP           DEFAULT CURRENT_TIMESTAMP           COMMENT 'creation date',
  updated_at       TIMESTAMP NULL      DEFAULT NULL                        COMMENT 'last update date',
  locked_by        BIGINT UNSIGNED                                         COMMENT 'user id for current lock (if any)',
  brief            TEXT                DEFAULT ''                          COMMENT 'story brief',
  files            TEXT                DEFAULT ''                          COMMENT 'list of files attached',
  body             TEXT                DEFAULT ''                          COMMENT 'story text',
  CONSTRAINT fk_news_stories_categories 
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE RESTRICT,
  KEY status(status),
  KEY importance_pub_date (importance, pub_date),
  KEY num_of_comments_pub_date (num_of_comments, pub_date),
  KEY num_of_votes_pub_date (num_of_votes, pub_date),
  KEY num_of_views_pub_date (num_of_views, pub_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='News stories';


-- Таблица топовых новостей рубрик.
CREATE TABLE IF NOT EXISTS news_top_stories (
  category_id       SMALLINT UNSIGNED  NOT NULL    COMMENT 'category_id',
  story_id          BIGINT UNSIGNED    NOT NULL    COMMENT 'story_id',
  pub_date          DATETIME           NOT NULL    COMMENT 'publication date',
  importance        TINYINT UNSIGNED   NOT NULL    COMMENT 'story importance',
  title             VARCHAR(255)       NOT NULL    COMMENT 'story title',
  num_of_comments   MEDIUMINT UNSIGNED NOT NULL    COMMENT 'number of comments',
  num_of_votes      MEDIUMINT UNSIGNED NOT NULL    COMMENT 'number of votes for the story',
  num_of_views      MEDIUMINT UNSIGNED NOT NULL    COMMENT 'number of user views',
  brief             TEXT               DEFAULT  '' COMMENT 'story brief',
  files             TEXT               DEFAULT  '' COMMENT 'list of files attached',
  PRIMARY KEY (category_id, story_id),
  KEY importance_pub_date (importance, pub_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Top stories by importance';


-- Таблица соответствия тегов статьям.
CREATE TABLE IF NOT EXISTS news_tag_refs (
  tag_id   BIGINT UNSIGNED NOT NULL COMMENT 'tag id',
  story_id BIGINT UNSIGNED NOT NULL COMMENT 'story id',
  PRIMARY KEY (tag_id, story_id),
  CONSTRAINT fk_news_tag_refs_tags
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
  CONSTRAINT fk_news_tag_refs_stories
    FOREIGN KEY (story_id) REFERENCES news_stories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Story tags references';


-- :REVERSE
DROP TABLE IF EXISTS news_tag_refs;
DROP TABLE IF EXISTS news_top_stories;
DROP TABLE IF EXISTS news_stories;
DROP TABLE IF EXISTS news_categories;
-- :END
