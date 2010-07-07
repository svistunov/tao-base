<?php
/// <module name="Test.CLI.GetOpt" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'CLI.GetOpt');

/// <class name="Test.CLI.GetOpt" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_CLI_GetOpt implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.CLI.GetOpt.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.CLI.GetOpt.Case" extends="Dev.Unit.TestCase">
class Test_CLI_GetOpt_Case extends Dev_Unit_TestCase {
  protected $opt;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->opt = CLI_GetOpt::Parser()->
      string_option('string', '-s', '--string', 'Test comment for string option')->
      int_option('int', '-i', '--int', 'Test comment for int option')->
      float_option('float', '-f', '--float', 'Test comment for float option')->
      boolean_option('boolean', '-b', '--boolean', 'Test comment for boolean option', false)->
      brief('Test brief');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_usage() {
    $this->
      assert_same(
        $this->opt->usage_text(),
        'Test brief'.
        '-s, --string              Test comment for string option'.
        '-i, --int                 Test comment for int option'.
        '-f, --float               Test comment for float option'.
        '-b, --boolean             Test comment for boolean option'
      );
  }
///     </body>
///   </method>

///   <method name="test_parse">
///     <body>
  public function test_parse() {
    $config = (object) array('int' => 25, 'boolean' => true);
    $argv = array('script_name', '--string=test value', '-i  11  ', '-f10.5', '--boolean');

    $this->opt->parse($argv, $config);

    $this->
      assert_equal($config, (object) array(
        'string' => 'test value',
        'int' => 11,
        'float' => 10.5,
        'boolean' => false
      ))->
      assert_equal(
        $this->opt->script,
        'script_name'
      );
  }
///     </body>
///   </method>

///   <method name="test_iterating">
///     <body>
  public function test_iterating() {
    $this->
      asserts->iterating->assert_read($this->opt, array(
        (object) array(
          'name' => 'string',
          'short' => '-s',
          'long' => '--string',
          'type' => CLI_GetOpt::STRING,
          'comment' => 'Test comment for string option',
          'value' => null
        ),
        (object) array(
          'name' => 'int',
          'short' => '-i',
          'long' => '--int',
          'type' => CLI_GetOpt::INT,
          'comment' => 'Test comment for int option',
          'value' => null
        ),
        (object) array(
          'name' => 'float',
          'short' => '-f',
          'long' => '--float',
          'type' => CLI_GetOpt::FLOAT,
          'comment' => 'Test comment for float option',
          'value' => null
        ),
        (object) array(
          'name' => 'boolean',
          'short' => '-b',
          'long' => '--boolean',
          'type' => CLI_GetOpt::BOOL,
          'comment' => 'Test comment for boolean option',
          'value' => false
        )
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>