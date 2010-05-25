<?php
/// <module name="Test.Mail.List" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Mail.List', 'IO.FS');

/// <class name="Test.Mail.List" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Mail_List implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="creating">

  static public function initialize() {
  	IO_FS::mkdir('./test/data/Mail/List');
    Mail_List::options(array(
      'root' => 'test/data/Mail/List',
      'headers' => array_merge(
                     Mail_List::option('headers'),
                     array('Message-ID'))
    ));
  }

///   </protocol>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Mail.List.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.List.Case" extends="Dev.Unit.TestCase">
class Test_Mail_List_Case extends Dev_Unit_TestCase {
  protected $spawner;
  protected $message;
  protected $list;
  protected $boundary;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->spawner = Mail_List::Spawner(
      $this->message = Mail_Message::Message()->
        multipart_alternative($this->boundary = '=--test--')->
        from('test@techart.ru')->
        text_part('test text message  {:test_param}')->
        html_part('test <b>html</b> part {:test_param}'),
      $this->list = array(
        array(
          'To' => 'test1@techart.ru',
          'test_param' => 'value1',
          'Message-ID' => '1'
        ),
        array(
          'To' => 'test2@techart.ru',
          'test_param' => 'value2',
          'Message-ID' => '2'
        ),
        array(
          'To' => 'test3@techart.ru',
          'test_param' => 'value3',
          'Message-ID' => '3'
        )
      )
    )->
    id('100');
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    foreach (IO_FS::Dir('test/data/Mail/List')->query(IO_FS::Query()->recursive()) as $f)
      IO_FS::rm($f);
    foreach (IO_FS::Dir('test/data/Mail/List') as $d)
      IO_FS::rm($d->path);
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->assert_equal(
      $this->spawner->spawn(),
      '100'
    )->
    assert_equal(
      $this->message->as_string(),
      IO_FS::File('test/data/Mail/List/messages/'.$this->spawner->id().'.body')->load()
    );

    $names = array('100.000000', '100.000001', '100.000002');
    foreach (IO_FS::Dir($p ='test/data/Mail/List/recipients') as  $f) {
      if (($n = array_search($f->name, $names)) === false) {
        $this->assert_true(false, "To many files in $p");
      }
      $k = $n + 1;
      $this->assert_equal(
            $f->load(),
            "To: test$k@techart.ru\n".
            "Message-ID: $k\n".
            "-test_param: value$k\n"
      );
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>