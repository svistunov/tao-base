<?php
/// <module name="Test.Log" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Log', 'Log.FirePHP', 'Time', 'Net.HTTP');

/// <class name="Test.Log" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Log implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix(
      'Test.Log.', 'LevelCase', 'ContextCase', 'HandlerBuilderCase',
      'DispatcherCase', 'HandlerBaseCase', 'StreamHandlerCase',
      'FileHandlerCase', 'SyslogHandlerCase', 'FirePHPHandlerCase'
    );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.LevelCase" extends="Dev.Unit.TestCase">
class Test_Log_LevelCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->
      assert_equal(Log_Level::normalize(Log_Level::INFO), 20)->
      assert_equal(Log_Level::normalize(16), 20)->
      assert_equal(Log_Level::normalize(55), 0)->
      assert_equal(Log_Level::normalize(5), 10)->
      assert_equal(Log_Level::normalize(-20), 10)->
      assert_equal(Log_Level::as_string(Log_Level::CRITICAL), 'C')->
      assert_equal(Log_Level::as_string(Log_Level::ERROR), 'E')->
      assert_equal(Log_Level::as_string(Log_Level::WARNING), 'W')->
      assert_equal(Log_Level::as_string(Log_Level::INFO), 'I')->
      assert_equal(Log_Level::as_string(Log_Level::DEBUG), 'D')->
      assert_equal(Log_Level::as_string(60), '?')->
      assert_equal(Log_Level::as_string(33), 'E');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.HandlerBuilderCase" extends="Dev.Unit.TestCase">
class Test_Log_HandlerBuilderCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_all() {
    Core::with(new Log_HandlerBuilder(Log::logger(),
      $h = new Log_StreamHandler(IO_Stream::NamedResourceStream('php://memory', 'wb'))))->
      where('level', '<=', Log_Level::CRITICAL)->
      format('{time}---{body}--({level})')->
    end;
    $this->asserts->accessing->
      assert_read($h, array(
        'format' => '{time}---{body}--({level})',
        'filter' => array(array('level', '<=', 50))
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.Context" extends="Log.Context" >
class Test_Log_Context extends Log_Context {
///   <protocol name="performing" >

///   <method name="emit" returns="Log.Context">
///     <args>
///       <arg name="message" type="object" />
///     </args>
///     <body>
  protected function emit($message) {
    parent::emit($message);
    return $message;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.ContextCase" extends="Dev.Unit.TestCase">
class Test_Log_ContextCase extends Dev_Unit_TestCase {

  protected $context;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->context = new Test_Log_Context(array(
      'number' => 1,
      'string' => 'test'
    ), Log::logger());
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_configure() {
    $this->context->
      with(array('float' => 10.1))->
      parent($p = new Log_Context(array()));
    $this->asserts->accessing->
      assert_read($this->context, array(

      ));
  }
///     </body>
///   </method>

///   <method name="test_building">
///     <body>
  public function test_building() {
    $new = $this->context->context(array());
    $this->assert_equal($new->parent, $this->context);
  }
///     </body>
///   </method>

///   <method name="test_emit">
///     <body>
  public function test_performing() {
    foreach (array(
      array( 'level' => Log_Level::DEBUG, 'method' => 'debug'),
      array( 'level' => Log_Level::CRITICAL, 'method' => 'critical'),
      array( 'level' => Log_Level::WARNING, 'method' => 'warning'),
      array( 'level' => Log_Level::INFO, 'method' => 'info')
    ) as $v) {
    extract($v);
    $message = $this->context->$method('Test1', 'Test2');
    $this->
      assert_true(is_int($message->time))->
      asserts->accessing->assert_read($message, array(
        'body' => array('Test1', 'Test2'),
        'level' => $level,
        'number' => 1,
        'string' => 'test'
      ));
    }
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->context, array(
        'parent' => Log::logger(),
        'values' => array('number' => 1, 'string' => 'test'),
        'dispatcher' => Log::logger()
      ))->
      assert_missing($this->context);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.EmptyHandler" extends="Log.Handler" >
class Test_Log_EmptyHandler extends Log_Handler {

  public $called_methods = array();
  public $format_message;

///   <protocol name="performing">

///   <method name="emit_if_acceptable" returns="Log.Handler">
///     <args>
///       <arg name="message" />
///     </args>
///     <body>
  public function emit_if_acceptable($message) {
    $this->called_methods[] = array('emit_if_acceptable' => array($message));
    return parent::emit_if_acceptable($message);
  }
///     </body>
///   </method>

///   <method name="emit" returns="Log.Handler">
///     <args>
///       <arg name="message" type="object" />
///     </args>
///     <body>
  public function emit($message) {
    $this->called_methods[] = array('emit' => array($message));
    $this->format_message = parent::format_message($message);
  }
///     </body>
///   </method>

///   <method name="init" returns="Log.Handler">
///     <body>
  public function init() {
    $this->called_methods[] = array('init' => array());
  }
///     </body>
///   </method>

///   <method name="close">
///     <body>
  public function close() {
    $this->called_methods[] = array('close' => array());
  }
///     </body>
///   </method>

///   <method name="test_call">
///     <body>
  public function test_call() {
    $this->called_methods[] = array('test_call' => array());
  }
///     </body>
///   </method>
///   </protocol>

}
/// </class>

/// <class name="Test.Log.DispatcherCase" extends="Dev.Unit.TestCase">
class Test_Log_DispatcherCase extends Dev_Unit_TestCase {

  protected $dispatcher;
  protected $handler1;
  protected $handler2;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->dispatcher =Core::with(new Log_Dispatcher())->with(array('time' => 0));
    $this->handler1 = new Test_Log_EmptyHandler();
    $this->handler2 = new Test_Log_EmptyHandler();
    Log::map('empty', 'Test.Log.EmptyHandler');
  }
///     </body>
///   </method>

///   <method name="test_configure">
///     <body>
 public function test_all() {
    $this->dispatcher->
      handler($this->handler1)->
      to_empty()->
        test_call();
    $this->dispatcher->
      init()->
      debug('Debug message')->
      close();

    $message = Core::object(array(
      'time' => 0,
      'body' => 'Debug message',
      'level' => Log_Level::DEBUG
    ));
    $this->assert_equal($this->handler1->called_methods, array(
      array('init' => array()),
      array('emit_if_acceptable' => array($message)),
      array('emit' => array($message)),
      array('close' => array())
    ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.HandlerBaseCase" extends="Dev.Unit.TestCase">
class Test_Log_HandlerBaseCase extends Dev_Unit_TestCase {

  protected $handler;
  protected $good_message;
  protected $bad_message;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->handler = new Test_Log_EmptyHandler();
    $this->handler->
      format('{level}--{time}::{body}')->
      time_format('%d/%m|%Y')->
      where('eq', '=', 10)->
      where('low', '<', 10)->
      where('hight', '>', 10)->
      where('low_eq', '<=', 10)->
      where('hight_eq', '>=', 10)->
      where('regexp', '~', '!1234!')->
      where('in', 'in', array(1, 2, 3, 4))->
      where('not_in', '!in', array(1, 2, 3, 4));

    $this->good_message = Core::object(array(
      'eq' => 10,
      'low' => 9,
      'hight' => 13,
      'low_eq' => 10,
      'hight_eq' => 20,
      'regexp' => '0123456789',
      'in' => 3,
      'not_in' => 0,
      'body' => 'Debug message',
      'time' => Time::DateTime('2010-01-01')->timestamp,
      'level' => Log_Level::DEBUG
    ));

    $this->bad_message = clone $this->good_message;
    $this->bad_message->regexp = '0123';

  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->handler->emit_if_acceptable($this->good_message);
    $this->
      assert_equal(
        $this->handler->format_message,
        'D--01/01|2010::Debug message'
      )->
      assert_equal(
        $this->handler->called_methods,
        array(
          array('emit_if_acceptable' => array($this->good_message)),
          array('emit' => array($this->good_message))
        )
      );

    $this->handler->format_message = null;
    $this->handler->called_methods = array();
    $this->handler->emit_if_acceptable($this->bad_message);
    $this->
      assert_equal(
        $this->handler->called_methods,
        array(
          array('emit_if_acceptable' => array($this->bad_message))
        )
      )->
      assert_equal(
        $this->handler->format_message,
        null
      );
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->handler, array(
        'format' => '{level}--{time}::{body}',
        'time_format' => '%d/%m|%Y'
      ))->
      assert_read_only($this->handler, array(
        'filter' => $f = array(
          array('eq', '=', 10),
          array('low', '<', 10),
          array('hight', '>', 10),
          array('low_eq', '<=', 10),
          array('hight_eq', '>=', 10),
          array('regexp', '~', '!1234!'),
          array('in', 'in', array(1, 2, 3, 4)),
          array('not_in', '!in', array(1, 2, 3, 4))
        )
      ))->
      assert_write($this->handler, array(
        'format' => '{level}',
        'time_format' => '%m'
      ))->
      assert_missing($this->handler);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.StreamHandlerCase" extends="Dev.Unit.TestCase">
class Test_Log_StreamHandlerCase extends Dev_Unit_TestCase {

  protected $handler;
  protected $stream;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->handler = new Log_StreamHandler(
      $this->stream = IO_Stream::NamedResourceStream('php://memory', 'wb')
    );
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->handler->emit(Core::object(array(
      'body' => 'Test',
      'time' => 0,
      'level' => Log_Level::DEBUG
    )));
    $this->assert_same(
      $this->stream->rewind()->load(),
      '1970-01-01 03:00:00:D:Test'
    );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.FileHandlerCase" extends="Dev.Unit.TestCase">
class Test_Log_FileHandlerCase extends Dev_Unit_TestCase {
  protected $handler;
  protected $path;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $dir = '';
    $this->path = './test/data/Log/test.log';
    if (!IO_FS::exists('./test/data/Log')) IO_FS::mkdir($dir);
    $this->handler = Core::with(new Log_FileHandler(
      $this->path
    ))->init();
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  protected function teardown() {
    $this->handler->close();
    if (IO_FS::exists($this->path)) IO_FS::rm($this->path);
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->handler->emit(Core::object(array(
      'body' => 'Test',
      'time' => 0,
      'level' => Log_Level::DEBUG
    )));
    $this->assert_same(
      IO_FS::File($this->path)->load(),
      '1970-01-01 03:00:00:D:Test'
    );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.SyslogHandlerCase" extends="Dev.Unit.TestCase">
class Test_Log_SyslogHandlerCase extends Dev_Unit_TestCase {

  protected $handler;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->handler = new Log_SyslogHandler();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->asserts->accessing->
      assert_read_only($this->handler, $ro = array(
        'facility' => LOG_USER,
        'options' => LOG_PID,
        'id' => false
      ))->
      assert_undestroyable(
        $this->handler,
        array_keys($ro)
      );

    $this->handler->
      identified_as(101)->
      options(LOG_PERROR)->
      facility(LOG_KERN);

    $this->asserts->accessing->
      assert_read_only($this->handler, array(
        'facility' => LOG_KERN,
        'options' => LOG_PERROR,
        'id' => 101
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Log.FirePHPHandlerCase" extends="Dev.Unit.TestCase">
class Test_Log_FirePHPHandlerCase extends Dev_Unit_TestCase {

  protected $handler;
  protected $messages;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->handler = Log_FirePHP::Handler();
    $this->messages = array(
      Core::object(array('level' => Log_Level::DEBUG, 'body' => 'Debug1')),
      Core::object(array('level' => Log_Level::DEBUG, 'body' => 'Debug2')),
      Core::object(array('level' => Log_Level::DEBUG, 'body' => 'Debug3')),
      Core::object(array('level' => Log_Level::INFO, 'body' => 'Info')),
      Core::object(array('level' => Log_Level::CRITICAL, 'body' => 'Critical')),
      Core::object(array('level' => Log_Level::ERROR, 'body' => 'Warn')),
    );
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $response = Net_HTTP::Response();
    foreach ($this->messages as $m)
      $this->handler->emit($m);
    $this->handler->dump($response);
    $this->
      assert_equal(
        $response->headers,
        new Net_HTTP_Head(array(
          'X-WF-Protocol-1' => 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2',
          'X-WF-1-Plugin-1' => 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3',
          'X-WF-1-Structure-1' => 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1',
          'X-WF-1-1-1-1' => '25|[{"Type":"LOG"},"Debug1"]|',
          'X-WF-1-1-1-2' => '25|[{"Type":"LOG"},"Debug2"]|',
          'X-WF-1-1-1-3' => '25|[{"Type":"LOG"},"Debug3"]|',
          'X-WF-1-1-1-4' => '24|[{"Type":"INFO"},"Info"]|',
          'X-WF-1-1-1-5' => '29|[{"Type":"ERROR"},"Critical"]|',
          'X-WF-1-1-1-6' => '25|[{"Type":"ERROR"},"Warn"]|'
        ))
      );
  }
///     </body>
///   </method>

///   <method name="test_chunked">
///     <body>
  public function test_chunked() {
    $save_len = Log_FirePHP_Handler::max_len();
    Log_FirePHP_Handler::max_len(10);

    $this->handler->emit(Core::object(array(
      'level' => Log_Level::DEBUG,
      'body' => '01234567890123456789012345')));
    $response = Net_HTTP::Response();
    $this->handler->dump($response);
    $this->
      assert_equal(
      $response->headers,
      new Net_HTTP_Head(array(
        'X-WF-Protocol-1' => 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2',
          'X-WF-1-Plugin-1' => 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3',
          'X-WF-1-Structure-1' => 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1',
          'X-WF-1-1-1-1' => '45|[{"Type":"|\\',
          'X-WF-1-1-1-2' => '|LOG"},"012|\\',
          'X-WF-1-1-1-3' => '|3456789012|\\',
          'X-WF-1-1-1-4' => '|3456789012|\\',
          'X-WF-1-1-1-5' => '|345"]|'
      ))
      );

    Log_FirePHP_Handler::max_len($save_len);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>