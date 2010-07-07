<?php
/// <module name="Test.CLI.Application" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'CLI', 'CLI.Application');

/// <class name="Test.CLI.Application" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
///   <implements interface="CLI.RunInterface" />
class Test_CLI_Application implements Dev_Unit_TestModuleInterface, CLI_RunInterface {

///   <constants>
  const VERSION = '0.2.1';
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
    return ($app instanceof CLI_Application_Base) ?
      self::$app = $app :
      self::$app;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.CLI.Application.App" extends="CLI.Application.Base">
class Test_CLI_Application_App extends CLI_Application_Base {
  protected $e;
  public $calling_methods = array();
  public $config_path = './test/data/CLI/Application/config.php';

///   <protocol name="creating">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->options->
      brief(Core_Strings::format("Test.CLI.Application %s\n", Test_CLI_application::VERSION))->
      string_option('application', '-a', '--application', 'Visualizer application (graphviz)')->
      string_option('format',      '-T', '--format',      'Output format')->
      boolean_option('dump',       '-d', '--dump',        'No output conversion')->
      string_option('output',      '-o', '--output',      'Output file');

    $this->config->application = 'dot';
    $this->config->format = 'png';
    $this->config->output = null;
    $this->config->dump = false;

    $this->calling_methods[] = array('setup' => array());
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="shutdown" access="protected">
///     <body>
  protected function shutdown() {
    $this->calling_methods[] = array('shutdown' => array());
    return $this;
  }
///     </body>
///   </method>

///   <method name="configure" access="protected">
///     <body>
  protected function configure() {
    $this->calling_methods[] = array('configure' => array());
    $this->load_config($this->config_path);
    return $this;
  }
///     </body>
///   </method>

///   <method name="exit_wrapper" access="protected">
///     <args>
///       <arg name="status" type="int" />
///     </args>
///     <body>
///     <body>
  protected function exit_wrapper($status) {
    return $status;
  }
///     </body>
///   </method>

///   <method name="run" returns="int">
///     <args>
///       <arg name="argv" type="array" />
///     </args>
///     <body>
  public function run(array $argv) {
    $this->calling_methods[] = array('run' => array());
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
}
/// </class>

/// <class name="Test.CLI.Application.ApplicationCase" extends="Dev.Unit.TestCase">
class Test_CLI_Application_ApplicationCase extends Dev_Unit_TestCase {
  protected $app;
  protected $argv = array();
  protected $status;
  private $out_log;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->out_log = IO_Stream::NamedResourceStream('php://memory', 'wb');
    $this->app = Test_CLI_Application::app(new Test_CLI_Application_App());
    $this->app->log->dispatcher->
      to_stream($this->out_log)->
        where('module', '=', Core_Types::module_name_for($this));
  }
///     </body>
///   </method>

///   <method name="test_calling">
///     <body>
  public function test_calling() {
    $this->argv = array('tao-run');
    $this->run_cli();
    $this->assert_equal(
      $this->app->calling_methods,
      array(
        array('setup' => array()),
        array('configure' => array()),
        array('run' => array()),
        array('shutdown' => array())
      )
    )->
      assert_equal($this->status, 0);
  }
///     </body>
///   </method>

///   <method name="test_config">
///     <body>
  public function test_config() {
    $this->run_cli();
    $this->assert_equal(
      $this->app->config,
      (object) array(
        'log' => Log::logger(),
        'show_usage' => false,
        'application' => 'dot',
        'format' => 'svg',
        'output' => null,
        'dump' => true
      )
    );
  }
///     </body>
///   </method>

///   <method name="test_log">
///     <body>
  public function test_log() {
    $this->run_cli();
    $this->
	    assert_match(
	      "!{$this->app->config_path}!",
	      $this->out_log->rewind()->load()
	    );
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->app, $ro = array(
        'log' => Log::logger()->context(array(
          'module' => 'Test.CLI.Application'))
      ))->
      assert_exists_only($this->app, $eo = array(
        'config', 'options'
      ))->
      assert_class('CLI_GetOpt_Parser', $this->app->options);
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
      assert_match('{Test\s+exception}', $this->out_log->rewind()->load());
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="run">
///     <body>
  protected function run_cli() {
    $this->status = CLI::run_module(array_merge((array) 'Test.CLI.Application' , $this->argv));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>
