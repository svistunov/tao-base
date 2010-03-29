<?php
/// <module name="Test.Config.DSL" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Config.DSL');

/// <class name="Test.Config.DSL" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Config_DSL implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Config.DSL.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Config.DSL.Case" extends="Dev.Unit.TestCase">
class Test_Config_DSL_Case extends Dev_Unit_TestCase {
  protected $config;
  protected $config_from_file;
///   <protocol name="testing">


///   <method name="test_all">
///     <body>
  public function test_accessing() {
    $config = Config_DSL::Builder()->
      begin('option1')->
        value1_for_option1('1_1')->
        begin('option1_1')->
          value1_for_option1_1('1_1_1')->
        end->
        value2_for_option1('1_2')->
      end->
    end;

    $this->
      assert_equal($config->option1->value1_for_option1, '1_1')->
      assert_equal($config->option1->option1_1->value1_for_option1_1, '1_1_1')->
      assert_equal($config->option1->value2_for_option1, '1_2');
  }
///     </body>
///   </method>

///   <method name="test_from_file">
///     <body>
  public function test_loade_file() {
    $c = Config_DSL::load('test/data/Config/DSL/test.php');
    $this->
      assert_equal($c->db->dsn, 'mysql://user:pw@localhost/db')->
      assert_equal($c->cache->dsn, 'fs://../var/cache/app')->
      assert_equal($c->cache->timeout, 5*60)->
      assert_equal($c->templates->templates_root, '../app/views/')->
      assert_equal($c->curl->proxy, 'http://192.168.5.21:3128');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>