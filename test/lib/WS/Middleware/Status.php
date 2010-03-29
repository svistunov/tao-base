<?php
/// <module name="Test.WS.Middleware.Status" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.Status', 'Test.WS');

/// <class name="Test.WS.Middleware.Status" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_Status implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.Status.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

class Test_WS_Middleware_Status_App implements WS_ServiceInterface {
  protected $status;

///   <protocol name="processing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" brief="объект окружения" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    return Net_HTTP::Response()->status($this->status);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="changing">

///   <method name="status">
///     <args>
///       <arg name="status" type="int" />
///     </args>
///     <body>
  public function status($status) {
    $this->status = $status;
  }
///     </body>
///   </method>

///   </protocol>

}

/// <class name="Test.WS.Middleware.Status.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_Status_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_Middleware_Status_App();
    $this->service = WS_Middleware_Status::Service(
      $this->app,
      array(
        500,
        404 => 'http/404'
      ),
      'http/status'
      );
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_status">
///     <body>
  public function test_status() {
    foreach (array(
      Net_HTTP::INTERNAL_SERVER_ERROR => './http/status.phtml',
      Net_HTTP::NOT_FOUND => './http/404.phtml'
    ) as $status => $path) {
      $this->app->status($status);
      $this->adapter->request = Net_HTTP::Request('http://localhost/');
      WS::Runner($this->adapter)->run($this->service);
      $tempate = $this->adapter->response->body;
      $this->
        assert_class('Templates.HTML.Template', $tempate)->
        assert_equal($tempate->path, $path,
        "wrong template path for status: $status: $path != {$tempate->path}");
    }
  }
///     </body>
///   </method>

///   <method name="test_disbale">
///     <body>
  public function test_disbale() {
    $this->service->disable();
    $this->app->status(Net_HTTP::OK);
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->run($this->service);
    $this->assert_equal($this->adapter->response, Net_HTTP::Response(Net_HTTP::OK));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>