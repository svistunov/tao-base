<?php
/// <module name="Test.WS.Middleware.Environment" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.Environment', 'Test.WS');

/// <class name="Test.WS.Middleware.Environment" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_Environment implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.Environment.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.Environment.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_Environment_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_SaveEnvApp();
    $this->service = WS_Middleware_Environment::Service(
      $this->app, array('key1' => 'value1', 'key2' => 'value2'), true);
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->run($this->service);
    $env = $this->app->env;
    $this->
      assert_equal($env['key1'], 'value1')->
      assert_equal($env['key2'], 'value2');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>