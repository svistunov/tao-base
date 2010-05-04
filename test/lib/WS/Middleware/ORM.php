<?php
/// <module name="Test.WS.Middleware.ORM" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.ORM', 'Test.WS', 'Dev.Unit.DB');

/// <class name="Test.WS.Middleware.ORM" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_ORM implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.ORM.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.Session" extends="DB.ORM.ConnectionMapper">
class Test_WS_Middleware_Session extends DB_ORM_ConnectionMapper {

}
/// </class>

/// <class name="Test.WS.Middleware.ORM.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_ORM_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_SaveEnvApp();
    $this->service = WS_Middleware_ORM::Service(
      $this->app, new Test_WS_Middleware_Session(), Dev_Unit_DB::option('dsn'));
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->run($this->service);
    $db = $this->app->env->db;
    $this->
      assert_class('Test.WS.Middleware.Session', $db)->
      assert_class('DB.Connection', $db->connection);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>