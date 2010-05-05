<?php
$data = array(
    "users" => Core::hash(array(
      'sss' => Test_DB_ORM_Blog::User(array(
        'id' => 1,
        'name' => 'sss',
        'email' => 'sss@techart.ru'
      )),
      'max' => Test_DB_ORM_Blog::User(array(
        'id' => 2,
        'name' => 'max',
        'email' => 'max@techart.ru'
      )),
      'rd' => Test_DB_ORM_Blog::User(array(
        'id' => 3,
        'name' => 'rd',
        'email' => 'rd@techart.ru'
      ))
      )),

    "postings" => Core::hash(array(
      1 => Test_DB_ORM_Blog::Posting(array(
        'id' => 1,
        'user_id' => 1,
        'title' => 'Ubuntu 8.10',
        'body' => 'Вышла новая версия Ubuntu',
        'published_at' => Time::parse('2008-9-30 9:15:56')
      )),
      2 => Test_DB_ORM_Blog::Posting(array(
        'id' => 2,
        'user_id' => 2,
        'title' => 'Основные команды Linux',
        'body' => 'На сегоднешний день ...',
        'published_at' => Time::parse('1971-1-20 6:00:00')
      )),
      3 => Test_DB_ORM_Blog::Posting(array(
        'id' => 3,
        'user_id' => 2,
        'title' => 'Передача по ссылке в PHP',
        'body' => 'Как выяснилось эксперементально ссылки лучше использовать когда ...',
        'published_at' => Time::parse('2007-1-1 10:00:00')
      )),
      4 => Test_DB_ORM_Blog::Posting(array(
        'id' => 4,
        'user_id' => 3,
        'title' => 'Кризис охватил планету',
        'body' => 'Кризис как всемирное потепление ...',
        'published_at' => Time::parse('2008-9-10 10:11:12')
      )),
      5 => Test_DB_ORM_Blog::Posting(array(
        'id' => 5,
        'user_id' => 2,
        'title' => 'Клинтон сново президент США :-)',
        'body' => 'Собственно сабж http://xxx.xxx.com',
        'published_at' => Time::parse('2008-11-4 9:15:16')
      )),
      6 => Test_DB_ORM_Blog::Posting(array(
        'id' => 6,
        'user_id' => 1,
        'title' => 'Блочная верстка',
        'body' => 'Есть несколько подходов к верстке сайта ...',
        'published_at' => Time::parse('2008-9-10 15:10:35')
      )),
      )),

    "tags" => Core::hash(array(
      'linux' => Test_DB_ORM_Blog::Tag(array(
        'id' => 1,
        'name' => 'linux'
      )),
      'php' => Test_DB_ORM_Blog::Tag(array(
        'id' => 2,
        'name' => 'php'
      )),
      'верстка' => Test_DB_ORM_Blog::Tag(array(
        'id' => 3,
        'name' => 'верстка'
      )),
      'юмор' => Test_DB_ORM_Blog::Tag(array(
        'id' => 4,
        'name' => 'юмор'
      )),
      'новости' => Test_DB_ORM_Blog::Tag(array(
        'id' => 5,
        'name' => 'новости'
      )),
      )),

    "tags_postings" => Core::hash(array(
        array("tag_id" => 1, "posting_id" => 1),
        array("tag_id" => 1, "posting_id" => 2),
        array("tag_id" => 2, "posting_id" => 2),
        array("tag_id" => 2, "posting_id" => 6),
        array("tag_id" => 3, "posting_id" => 6),
        array("tag_id" => 4, "posting_id" => 5),
        array("tag_id" => 4, "posting_id" => 3),
        array("tag_id" => 5, "posting_id" => 4),
        array("tag_id" => 5, "posting_id" => 5),
        array("tag_id" => 2, "posting_id" => 3)))
);
return $data;
?>