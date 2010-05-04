<?php
/// <module name="Test.DB" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit.DB');

/// <class name="Test.DB" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_DB implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.DB.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.DB.Prototype">
class Test_DB_Prototype {
  public $body;
  public $title;
  public $date_time;
}
/// </class>

/// <class name="Test.DB.Listener">
///   <implements interface="DB.QueryExecutionListener" />
class Test_DB_Listener implements DB_QueryExecutionListener {
  public $sql;

///   <protocol name="execute">
///   <method name="on_execute">
///     <args>
///       <arg name="cursor" type="DB.Cursor" />
///     </args>
///     <body>
  public function on_execute(DB_Cursor $cursor) {
    $this->sql = $cursor->sql;
  }
///     </body>
///   </method>
///   </protocol>
}
/// </class>

/// <class name="Test.DB.Case" extends="Dev.Unit.DB">
class Test_DB_Case extends Dev_Unit_DB_TestCase {
  protected $cursor;
  protected $sql;

///   <protocol name="testing">
///   <method name="setup_operation" accessing="protected">
///     <body>
  protected function setup() {
    $this->cursor = $this->connection->
      prepare($this->sql = "SELECT body,title,date_time FROM `db_table` ORDER BY id;");
  }
///     </body>
///   </method>

///   <method name="teardown" accessing="protected">
///     <body>
  protected function teardown() {
    $this->cursor->close();
  }
///     </body>
///   </method>

///   <method name="test_dsn">
///     <body>
  public function test_dsn() {
    $dsn = clone $this->connection->dsn;
    $this->asserts->stringifying->
      assert_string(
        $dsn,
        'mysql://www:www@mysql.rd1.techart.intranet/test'
      );

    $this->asserts->accessing->
      assert_read($dsn, $r = array(
        'type'     => 'mysql',
        'user'     => 'www',
        'password' => 'www',
        'host'     => 'mysql.rd1.techart.intranet',
        'port'     => '',
        'database' => 'test',
        'scheme'   => '',
        'parms'    => array(),
        'pdo_string' => 'mysql:host=mysql.rd1.techart.intranet;dbname=test'
      ))->
      assert_undestroyable($dsn, array_keys($r))->
      assert_write($dsn, array(
        'type'     => 'mssql',
        'user'     => 'guest',
        'password' => '123',
        'host'     => 'mssql.rd1.techart.intranet',
        'port'     => '9988',
        'database' => 'test',
        'scheme'   => '',
        'parms'    => array('param' => 'value'),
      ))->
      assert_equal(
        $dsn->as_string(),
        'mssql://guest:123@mssql.rd1.techart.intranet:9988/test?param=value'
      );
  }
///     </body>
///   </method>

///   <method name="test_last_insert_id">
///     <body>
  public function test_last_insert_id() {
    $this->connection->
      execute(
        "INSERT INTO db_table (id, body, title, date_time)".
        " VALUES (3, 'test3', 'title3', '2005-01-25 01:34:19');"
    );
    $this->assert_equal($this->connection->last_insert_id(), '3');
  }
///     </body>
///   </method>

///   <method name="test_iteration">
///     <body>
  public function test_iteration() {
    $this->asserts->iterating->
      assert_read($this->connection->prepare(
          "SELECT body,title,date_time FROM db_table ORDER BY id;")->execute(),
        $this->data['db_table']
      );
  }
///     </body>
///   </method>

///   <method name="test_fetch_all">
///     <body>
  public function test_fetch_all() {
    $this->assert_equal(
      $this->cursor->execute()->fetch_all(),
      $this->data['db_table']
    );
  }
///     </body>
///   </method>

///   <method name="test_fetch">
///     <body>
  public function test_fetch() {
    $cursor = $this->connection->prepare("SELECT body, title, date_time
       FROM db_table WHERE date_time = :mytime OR title = :mytitle ORDER BY id")->
      bind(Time_DateTime::parse('2008-01-16 22:30:35'), 'Another db test data')->
      execute();

    $this->assert_equal($this->data['db_table'][0], $cursor->fetch());
    $this->assert_equal($this->data['db_table'][1], $cursor->fetch());
    $cursor->close();
  }
///     </body>
///   </method>

///   <method name="test_listener">
///     <body>
  public function test_listener() {
    $sql = "SELECT id FROM db_table";
    $this->connection->listener($listener = new Test_DB_Listener());
    $this->connection->execute($sql);
    $this->assert_equal($listener->sql, $sql);
  }
///     </body>
///   </method>

///   <method name="test_row_instance">
///     <body>
  public function test_row_instance() {
    foreach (array('Test_DB_Prototype', new Test_DB_Prototype()) as $instance) {
      $row = $this->cursor->as_object($instance)->execute()->fetch();
      $this->asserts->accessing->
        assert_true($row instanceof Test_DB_Prototype)->
        assert_read($row, $this->data['db_table'][0]);
    }
  }
///     </body>
///   </method>

///   <method name="test_cursor_accessing">
///     <body>
  public function test_cursor_accessing() {
    $this->cursor->execute();
    $this->asserts->accessing->
      assert_read($this->cursor, array(
        'is_successful' => true,
        'sql' => $this->sql,
        'row' => null
      ))->
      assert_equal(
        $this->cursor->fetch(),
        $this->data['db_table'][0]
      )->
      assert_read($this->cursor, array(
        'num_of_rows' => 2,
        'num_of_fetched' => 1,
        'num_of_columns' => 3,
      ))->
      assert_true($this->cursor->execution_time > 0);

    $this->asserts->indexing->
      assert_read($this->cursor->metadata, array(
        'body' => new DB_ColumnMeta('body', 'blob', 196605, 0),
      ));

    $cursor = $this->connection->
      prepare("SELECT body, title, date_time FROM db_test WHERE date_time = :mydate")->
      bind(Time_DateTime::parse('2005-01-25 01:34:19'));
    $this->assert_equal($cursor->binds, array('2005-01-25 01:34:19'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>