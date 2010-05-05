<?php
/// <module name="Test.DB.ORM" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit.DB', 'DB.ORM');

/// <interface name="Test.DB.ORM.ModuleInterface" extends="Core.ModuleInterface">
interface Test_DB_ORM_ModuleInterface extends Core_ModuleInterface {}
/// </interface>

/// <class name="Test.DB.ORM" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_DB_ORM implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.DB.ORM.', 'Mappers');
  }
///     </body>
///   </method>

  static public function initialize(array $options = array()) {
    Core::load('Test.DB.ORM.Blog');
  }

///   </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Session" extends="DB.ORM.Session">
class Test_DB_ORM_Session extends DB_ORM_ConnectionMapper {
///   <protocol name="mapping">

///   <method name="map_blog" accessing="protected">
///     <body>
  protected function map_blog() { return $this->load_mappers_from('Test.DB.ORM.Blog');}
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Entity" extends="DB_ORM_Entity">
///   <implements interface="Core.EqualityInterface" />
///   <implements interface="IteratorAggregate" />
class Test_DB_ORM_Entity extends DB_ORM_Entity implements Core_EqualityInterface, IteratorAggregate{

  static protected $db;

///   <protocol name="configuring">

///   <method name="use_db">
///     <args>
///       <arg name="use_db" type="Test.DB.ORM.Session" />
///     </args>
///     <body>
  static function use_db(Test_DB_ORM_Session $db) { self::$db = $db;}
///     </body>
///   </method>

///   <method name="db" returns="Test.DB.ORM.Session">
///     <body>
  static function db() { return self::$db; }
///     </body>
///   </method>

/// </protocol>

/// <protocol name="equality">

///   <method name="equals" returns="boolean">
///     <args>
///       <arg name="to"/>
///     </args>
///     <body>
  public function equals($to) {
    return ($to instanceof Test_DB_ORM_Entity) &&
      Core::equals($this->attrs['id'], $to->attrs['id']);
  }
///     </body>
///   </method>

/// </protocol>

/// <protocol name="iterating">

///   <method name="db" returns="Iterator">
///     <body>
  public function getIterator() {
    return new ArrayIterator($this->attrs);
  }
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Mappers" extends="Dev.Unit.DB">
class Test_DB_ORM_Mappers extends Dev_Unit_DB_TestCase {
  protected $db;

///   <protocol name="testing">
///   <method name="setup_operation" accessing="protected">
///     <body>
  protected function setup() {
    $this->db = new Test_DB_ORM_Session();
    $this->db->connect($this->connection);
    Test_DB_ORM_Entity::use_db($this->db);
  }
///     </body>
///   </method>

///   <method name="test_make_tags">
///     <body>
  public function test_make_tags() {
    $t = $this->db->blog->postings()->
      where('title = :title', 'Основные команды Linux')->
      select_first()->
        tags->select();

    $this->
      assert_equal($this->tags['linux'], $t[0])->
      assert_equal($this->tags['php'], $t[1])->
      assert_equal(count($t), 2);
  }
///     </body>
///   </method>

///   <method name="test_make_postings">
///     <body>
  public function test_make_postings() {
    $post_entities = $this->tags['linux']->postings->select();
    $this->
      assert_equal(
        Core::hash(array($this->postings[1], $this->postings[2])),
        $post_entities
      );

     $post_mapper = $this->db->blog->postings->no_body();
     $this->
       assert_equal($post_mapper[1]['body'], null)->
       assert_equal($post_mapper[1]['title'], $this->postings[1]['title']);
  }
///     </body>
///   </method>

///   <method name="test_make_users">
///     <body>
  public function test_make_users() {
    $p = $this->db->blog->users->count_postings();
    $pos_count = $p->select();

    $this->
      assert_equal(count($p->stat_all()) ,3)->
      assert_equal($pos_count[0]['name'], $this->users['sss']['name'])->
      assert_equal($pos_count[1]['name'], $this->users['max']['name'])->
      assert_equal($pos_count[2]['name'], $this->users['rd']['name'])->
      assert_equal($pos_count[0]['count_post'], 2)->
      assert_equal($pos_count[1]['count_post'], 3)->
      assert_equal($pos_count[2]['count_post'], 1);

    $pos_count_having = $p->having('count_post > 1')->select();

    $this->
       assert_equal(count($p->stat_all()) ,2)->
       assert_equal($pos_count_having[0]['name'], $this->users['sss']['name'])->
       assert_equal($pos_count_having[1]['name'], $this->users['max']['name'])->
       assert_equal($pos_count_having[0]['count_post'], 2)->
       assert_equal($pos_count_having[1]['count_post'], 3);
  }
///     </body>
///   </method>

///   <method name="test_sql_select">
///     <body>
  public function test_sql_select() {
    $u = $this->db->blog->users()->where('name = :name', 'sss')->select_first();
    $this->
      assert_true($u instanceof Test_DB_ORM_Blog_User)->
      assert_equal($u, $this->users['sss']);

    $p = $u->postings->select();
    $this->
      assert_equal($p[0], $this->postings[1])->
      assert_equal($p[1], $this->postings[6])->
      assert_equal(count($p), 2);

    $u = $this->db->blog->users()->select_first_for('name = :name', 'sss');
    $this->assert_equal($this->users['sss'], $u);
  }
///     </body>
///   </method>

///   <method name="test_sql_count_range">
///     <body>
  public function test_sql_count_range() {
    //count
    $c = $this->db->blog->users()->count();
    $this->
      assert_equal($c, 3);

    //range
    $postings_range = $this->db->blog->postings->range(3, 2)->select();
    $this->
      assert_equal(
        $postings_range,
        Core::hash(array(
          $this->postings[3],
          $this->postings[4],
          $this->postings[5]))
    );

  }
///     </body>
///   </method>

///   <method name="test_sql_find">
///     <body>
  public function test_sql_find() {
    $find_user = $this->db->blog->users()->find(1);
    $this->assert_equal($find_user, $this->users['sss']);
  }
///     </body>
///   </method>

///   <method name="test_sql_insert_delete">
///     <body>
  public function test_sql_insert_delete() {
    //insert
    $insert_user = Test_DB_ORM_Blog::User(array(
      'id'=> 4,
      'name' => 'velod',
      'email' => 'velod@techart.ru'
   ));
    $this->db->blog->users()->insert($insert_user);

    $this->assert_equal(
      $this->db->blog->users()->where('name = :name', 'velod')->select_first(),
      $insert_user
    );
    //delete
    $this->db->blog->users()->delete($insert_user);
    $c = $this->db->blog->users()->count();
    $this->
      assert_equal($c, 3)->
      assert_equal(
        $this->db->blog->users()->where('name = :name', 'velod')->select_first(),
        null
    );

  }
///     </body>
///   </method>

///   <method name="test_sql_update">
///     <body>
  public function test_sql_update() {
    $update_user = $this->users['sss'];
    $update_user['email'] = 'new@techart.ru';
    $this->db->blog->users()->update($update_user);

    $this->assert_equal(
      $this->db->blog->users()->where('name = :name', $update_user)->select_first(),
      $update_user
    );

    $this->db->blog->users()->where('id > 1')->
      update_all(array('email' => 'xxx@techart.ru'));

    $this->
      assert_equal($this->db->blog->users[2]['email'], 'xxx@techart.ru')->
      assert_equal($this->db->blog->users[3]['email'], 'xxx@techart.ru')->
      assert_equal($this->db->blog->users[1]['email'], 'new@techart.ru');
  }
///     </body>
///   </method>

///   <method name="test_validator">
///     <body>
  public function test_validator() {
    $this->db->blog->users->
      validator(
        Validation::Validator()->validate_presence_of('email', 'Empty Email')
      );
    $user = Test_DB_ORM_Blog::User(array('name' => 'Li'));
    $this->asserts->accessing->
      assert_false($this->db->blog->users->insert($user))->
      assert_null($user->id)->
      assert_false($this->db->blog->users->options['validator']->is_valid())->
      assert_read($this->db->blog->users->options['validator'], array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array('email' => 'Empty Email'))
      ));

    $user = $this->db->blog->users->select_first();
    $user->email = '';

    $this->
      assert_false($this->db->blog->users->update($user))->
      assert_false($this->db->blog->users->options['validator']->is_valid());
  }
///     </body>
///   </method>

///   <method name="test_only">
///     <body>
  public function test_only() {
    $p = $this->db->blog->postings->only('title')->select_first();
    $this->
      assert_equal($p->title, $this->postings[1]->title)->
      assert_equal($p->body, null)->
      assert_equal($p->user_id, null)->
      assert_equal($p->published_at, null);

  }
///     </body>
///   </method>

///   <method name="test_lookup">
///     <body>
  public function test_lookup() {
    $t1 = $this->db->blog->tags['новости'];
    $t2 = $this->db->blog->tags->lookup('новости');
    $this->
      assert_equal($this->tags['новости'], $t1)->
      assert_equal($this->tags['новости'], $t2);
  }
///     </body>
///   </method>

///   <method name="test_index">
///     <body>
  public function test_index() {
    $this->
      assert_match(
        "{USE\s+INDEX\s+\(name\)}",
        $this->db->blog->users->index('name')->sql()->select()->as_string()
      );
  }
///     </body>
///   </method>

///   <method name="explicit_key">
///     <body>
  public function explicit_key() {
    $insert_user = Test_DB_ORM_Blog::User(array(
      'id'=> 100,
      'name' => 'test',
      'email' => 'test@techart.ru'
    ));

    $this->
     assert_true($this->db->blog->users->insert($insert_user))->
     assert_equal($insert_user->id, 4)->
     assert_true($this->db->blog->users->delete($insert_user));

    $insert_user->id = 100;
    $this->db->blog->users->explicit_key('id');

    $this->
      assert_true($this->db->blog->users->insert($insert_user))->
      assert_equal($insert_user->id, 100)->
      assert_true($this->db->blog->users->delete($insert_user));
  }
///     </body>
///   </method>

///   <method name="test_options">
///     <body>
  public function test_options() {
    $this->asserts->indexing->
      assert_read_only(
        $this->db->blog->users->
          validator($v = Validation::Validator()->validate_presence_of('name', 'Error'))->
          order_by('name')->
          group_by('id')->
          range(10, 20)->
          index('name')->
          lookup_by('email')->
          only('name', 'id')->
          defaults(array('id' => 0))->
          join('cross', 'some_table s', 's.user_id = users.id AND s.id > 2')->
          where('id > 2')->
          spawn()->
            defaults(array('id' => -1, 'name' => ''))->
            where('name LIKE %a%')->
            exclude('email')->
        options,
        array(
        'table' => array('users'),
        'classname' => 'Test.DB.ORM.Blog.User',
        'validator' => $v,
        'order_by' => 'name',
        'group_by' => 'id',
        'key' => array('id'),
        'explicit_key' => null,
        'range' => array(10, 20),
        'index' => 'name',
        'lookup_by' => 'email',
        'only' => array('name', 'id'),
        'aliased_table' => 'users',
        'table_prefix' => 'users',
        'defaults' => array('id' => -1, 'name' => ''),
        'columns' => array ('id', 'name', 'email', 'info'),
        'join' => array (array ('cross', 'some_table s', array ('s.user_id = users.id AND s.id > 2'))),
        'where' =>  array ('id > 2', 'name LIKE %a%'),
        'exclude' => array('email'),
        'result' => array ('id' => 'users.id', 'name' => 'users.name')
      ));
  }
///     </body>
///   </method>

///   <method name="test_downto">
///     <body>
  public function test_downto() {
    $this->
      assert_equal(
        $this->db->downto('blog/postings')->select_first(),
        $this->postings[1]
      )->
      assert_equal(
        $this->db->downto('blog/postings/2'),
        $this->postings[2]
      );
  }
///     </body>
///   </method>

///   <method name="test_mode">
///     <body>
  public function test_mode() {
    $this->
      assert_match(
        '{SELECT\s+DISTINCT}',
        $this->db->blog->postings->
          where('name LIKE :n', "%a%")->mode('DISTINCT')->query(false)->sql
      );
  }
///     </body>
///   </method>

///   <method name="test_immutable">
///     <body>
  public function test_immutable() {
    $this->
      assert_equal($this->db->blog->users->count(),3)->
      assert_equal(
        $this->db->blog->users->where('id > :id', 2)->count(),
        1)->
      assert_equal($this->db->blog->users->count(), 1);

    $this->db->blog->postings->immutable();
    $this->
      assert_equal($this->db->blog->postings->count(), 6)->
      assert_equal(
        $this->db->blog->postings->where('id > :id', 2)->count(),
        4)->
      assert_equal($this->db->blog->postings->count(), 6);

    $this->
      assert_equal($this->db->blog->tags->count(),5)->
      assert_equal(
        $this->db->blog->tags()->where('id > :id', 2)->count(),
        3)->
      assert_equal($this->db->blog->tags->count(), 5);

  }
///     </body>
///   </method>

///   <method name="test_preload">
///     <body>
  public function test_preload() {
    $this->
      assert_equal($this->db->blog->postings->preload()->cache,
      $this->postings);
  }
///     </body>
///   </method>

///   <method name="test_make_entity">
///     <body>
  public function test_make_entity() {
    $this->db->blog->users->defaults(array('name' => 'test', 'id' => 0));
    $e = $this->db->blog->users->
      make_entity(array(
        'email' => 'test@techart.ru',
        'name' => 'my name'));
    $this->
      assert_equal(
        $e,
        Test_DB_ORM_Blog::User(array(
          'id' => 0,
          'email' => 'test@techart.ru',
          'name' => 'my name'
        ))
      );
  }
///     </body>
///   </method>

///   <method name="test_paginate">
///     <body>
  public function test_paginate() {
    $pager = Data_Pagination::Pager(100, 2, 10);
    $mapper = $this->db->blog->postings->paginate_with($pager);
    $this->
      assert_equal(
        $mapper->options['range'],
        array(10, 10)
      );
  }
///     </body>
///   </method>

///   <method name="test_iteration">
///     <body>
  public function test_iteration() {
    foreach ($this->db->blog->postings as $k => $v)
    $this->assert_equal($v, $this->postings[$k+1]);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>