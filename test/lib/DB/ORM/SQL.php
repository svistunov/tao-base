<?php
/// <module name="Test.DB.ORM.SQL" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'DB.ORM.SQL');

/// <class name="Test.DB.ORM.SQL" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_DB_ORM_SQL implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.DB.ORM.SQL.', 'Update', 'Insert', 'Delete', 'Select');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.ORM.SQL.Update" extends="Dev.Unit.TestCase">
class Test_DB_ORM_SQL_Update extends Dev_Unit_TestCase {
  protected $update;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->update = DB_ORM_SQL::Update('news');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_same(
      $this->update->set('id', 'title', 'body')->where('id = :wid', 'catagory_id = :wc_id'),
      "UPDATE news SET id = :id, title = :title, body = :body WHERE (id = :wid) AND (catagory_id = :wc_id)");
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>
/// <class name="Test.ORM.SQL.Select" extends="Dev.Unit.TestCase">
class Test_DB_ORM_SQL_Select extends Dev_Unit_TestCase {
  protected $select;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->select = DB_ORM_SQL::Select();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
      $res = (string) $this->select->
        columns(array('n' => array('title', 'body'), 'c.careate_at', 'id', 'name' => 'c.caption'))->
        from(array('n' => 'news', 'c' => 'category'))->
        where('id = :wid')->
        order_by('c.create_at')->
        join('left','dates', 'dates.date = c.create_at')->
        group_by('n.id')->
        having('n.id > 10')->
        range(5)->
        index('index_name');

      $to_equal = "SELECT n.title title, n.body body, c.careate_at careate_at, id, c.caption name\n".
        "FROM news n, category c USE INDEX (index_name) LEFT JOIN dates ON (dates.date = c.create_at) \n".
        "WHERE (id = :wid)\n".
        "GROUP BY n.id\n".
        "HAVING (n.id > 10)\n".
        "ORDER BY c.create_at\n".
        "LIMIT 5\n";

      $this->assert_equal($res, $to_equal);

  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.ORM.SQL.Delete" extends="Dev.Unit.TestCase">
class Test_DB_ORM_SQL_Delete extends Dev_Unit_TestCase {
  protected $delete;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->delete = DB_ORM_SQL::Delete('news');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_same(
        $this->delete->where('id = :wid')->as_string(),
        "DELETE FROM news WHERE (id = :wid)");
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.ORM.SQL.Insert" extends="Dev.Unit.TestCase">
class Test_DB_ORM_SQL_Insert extends Dev_Unit_TestCase {
  protected $insert;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->insert = DB_ORM_SQL::Insert('title', 'body');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_same(
      'INSERT IGNORE INTO news (title, body) VALUES(:title, :body)',
      $this->insert->into('news')->mode('IGNORE'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>