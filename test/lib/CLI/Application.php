<?php
/// <module name="Test.CLI.Application" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'CLI', 'CLI.Application');

/// <class name="Test.CLI.Application" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
///   <implements interface="CLI.RunInterface" />
class Test_CLI_Application implements Dev_Unit_TestModuleInterface, CLI_RunInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

  static protected $app;

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.CLI.Application.', 'ApplicationCase');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="main" scope="class">
///     <brief>Точка входа</brief>
///     <args>
///       <arg name="argv" type="array" brief="массив аргументов командной строки" />
///     </args>
///     <body>
  static public function main(array $argv) {
    return self::$app->main($argv);
}
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="app">
///     <body>
  static public function app($app = null) {
    return ($app instanceof CLI_Application_AbstractApplication) ?
      self::$app = $app :
      self::$app;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.CLI.Application.App" extends="CLI.Application.AbstractApplication">
class Test_CLI_Application_App extends CLI_Application_AbstractApplication implements Core_PropertyAccessInterface {
  protected $e;

///   <protocol name="creating">

///   <method name="setup">
///     <body>
  protected function setup() {
    return parent::setup()->
      usage_text(Core_Strings::format("Test.CLI.Application %s\n", Test_CLI_application::VERSION))->
      //from Dev.DB.Diagram
      options(
        array(
          array('application', '-a', '--application', 'string',  null, 'Visualizer application (graphviz)'),
          array('format',      '-T', '--format',      'string',  null, 'Output format'),
          array('dump',        '-d', '--dump',        'boolean', true, 'No output conversion'),
          array('output',      '-o', '--output',      'string',  null,  'Output file')),
        array(
          'application' => 'dot',
          'format'      => 'png',
          'output'      => null,
          'dump'   => false ));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run" returns="int">
///     <args>
///       <arg name="argv" type="array" />
///     </args>
///     <body>
  public function run(array $argv) {
    if ($this->e) throw $this->e;
    return 0;
  }
///     </body>
///   </method>

///   <method name="exception">
///     <body>
  public function exception(Exception $exception) {
    $this->e = $exception;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol>

///   <method name="shutdown">
///     <brief>Завершает выполнение</brief>
///     <args>
///       <arg name="status" type="int" />
///     </args>
///     <body>
  protected function shutdown($status) {
    return $status;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'options':
      case 'getopt':
        return $this->$property;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function __set($property, $value) {
    throw new Core_ReadOnlyObjectException($this);
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'options':
      case 'getopt':
        return isset($this->$property);
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) {
    throw new Core_ReadOnlyObjectException($this);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.CLI.Application.ApplicationCase" extends="Dev.Unit.TestCase">
class Test_CLI_Application_ApplicationCase extends Dev_Unit_TestCase {
  protected $app;
  protected $argv = array();
  protected $status;
  private $__stdout__;
  private $__stderr__;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->__stdout__ = IO::stdout();
    $this->__stderr__ = IO::stderr();
    IO::stdout(IO_Stream::NamedResourceStream('php://memory', 'wb'));
    IO::stderr(IO_Stream::NamedResourceStream('php://memory', 'wb'));
    $this->app = Test_CLI_Application::app(new Test_CLI_Application_App());
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  protected function teardown() {
    IO::stdout($this->__stdout__);
    IO::stderr($this->__stderr__);
  }
///     </body>
///   </method>

///   <method name="test_usage">
///     <body>
  public function test_usage() {
    $this->argv = array('-h');
    $this->run_cli();
    $this->
      assert_equal($this->status, 0)->
      assert_match(
          '{Test.CLI.Application\s+'.Test_CLI_Application::VERSION.'\s+'.
          '-h,\s+--help\s+Shows help message\s+'.
          '-a,\s+--application\s+Visualizer application \(graphviz\)\s+'.
          '-T,\s+--format\s+Output format\s+'.
          '-d,\s+--dump\s+No output conversion\s+'.
          '-o,\s+--output\s+Output file\s+}',
        IO::stdout()->load());
  }
///     </body>
///   </method>

///   <method name="test_error">
///     <body>
  public function test_error() {
    $this->app->exception(new Core_Exception('Test exception'));
    $this->run_cli();
    $this->
      assert_equal($this->status, -1)->
      assert_match('{\s*Error:\s+Test\s+exception\s*}', IO::stderr()->load());
  }
///     </body>
///   </method>

///   <method name="test_options">
///     <body>
  public function test_options() {
    $this->argv = array('-Tdot', '-otmp/a.dot', 'DB.ORM');
    $this->run_cli();
    $this->assert_equal($this->status, 0);
    $this->asserts->iterating->
      assert_read($this->app->getopt->options, array(
        0 => (object) array(
          'name' => 'show_help', 'short' => '-h', 'long' => '--help', 'type' => 'boolean',
          'value' => true, 'comment' => 'Shows help message'
          ),
        1 => (object) array(
          'name' => 'application', 'short' => '-a', 'long' => '--application', 'type' => 'string',
          'value' => null, 'comment' => 'Visualizer application (graphviz)'
          ),
        2 => (object) array(
          'name' => 'format', 'short' => '-T', 'long' => '--format', 'type' => 'string',
          'value' => null, 'comment' => 'Output format'
          ),
        3 => (object) array(
          'name' => 'dump', 'short' => '-d', 'long' => '--dump', 'type' => 'boolean',
          'value' => true, 'comment' => 'No output conversion'
          ),
        4 => (object) array(
          'name' => 'output', 'short' => '-o', 'long' => '--output', 'type' => 'string',
          'value' => null, 'comment' => 'Output file'
          ),
      ));
    $this->assert_equal(
      $this->app->options, array(
        'show_help' => false,
        'application' => 'dot',
        'format' => 'dot',
        'output' => 'tmp/a.dot',
        'dump' => false
      ));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="run">
///     <body>
  protected function run_cli() {
    $this->status = CLI::run_module(array_merge((array) 'Test.CLI.Application' , $this->argv));
    IO::stdout()->rewind();
    IO::stderr()->rewind();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>