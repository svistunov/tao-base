<?php
/// <module name="Test.WS.Middleware.Config" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.Config', 'Test.WS');

/// <class name="Test.WS.Middleware.Config" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_Config implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.Config.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.Config.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_Config_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_SaveEnvApp();
    $this->service = WS_Middleware_Config::Service(
      $this->app, 'test/data/Config/DSL/test.php');
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->run($this->service);
    $c = $this->app->env->config;
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