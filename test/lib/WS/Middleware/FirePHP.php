<?php
/// <module name="Test.WS.Middleware.FirePHP" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.FirePHP', 'Test.WS');

/// <class name="Test.WS.Middleware.FirePHP" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_FirePHP implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.FirePHP.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.FirePHP.LogApp">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_Middleware_FirePHP_LogApp implements WS_ServiceInterface {
///   <protocol name="processing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" brief="объект окружения" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    Log::logger()->debug('Test');
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.FirePHP.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_FirePHP_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_Middleware_FirePHP_LogApp();
    $this->service = WS_Middleware_FirePHP::Service($this->app);
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->run($this->service);
    $this->assert_equal(
      $this->adapter->response->headers['X-WF-1-1-1-1'],
      '23|[{"Type":"LOG"},"Test"]|'
    );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>