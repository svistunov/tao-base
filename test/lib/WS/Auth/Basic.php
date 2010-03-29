<?php
/// <module name="Test.WS.Auth.Basic" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Test.WS', 'Test.WS.Auth', 'WS.Auth.Basic');

/// <class name="Test.WS.Auth.Basic" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Auth_Basic implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Auth.Basic.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Auth.Basic.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Auth_Basic_ServiceCase extends Dev_Unit_TestCase {

  protected $adapter;
  protected $service;
  protected $app;
  protected $auth_module;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = new Test_WS_Adapter();
    $this->service = WS_Auth_Basic::Service(
      $this->app = new Test_WS_Auth_App(),
      $this->auth_module = new Test_WS_Auth_AuthModule());
    $this->auth_module->user(1, array('login' => 'Ivan', 'password' => '123'));
  }
///     </body>
///   </method>

///   <method name="test_unauthenticated">
///     <body>
 public function test_unauthenticated() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    $this->app->exception(new WS_Auth_UnauthenticatedException($realm = 'test realm'));
    WS::Runner($this->adapter)->run($this->service);

    $this->assert_equal(
      $this->adapter->response,
      Net_HTTP::Response(Net_HTTP::UNAUTHORIZED)->
        header('www_authenticate', 'Basic realm="'.$realm.'"')
      );
  }
///     </body>
///   </method>

///   <method name="test_login">
///     <body>
 public function test_login() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/')->
      header('authorization','Basic '.Core_Strings::encode64('Ivan:123'));
    WS::Runner($this->adapter)->run($this->service);

    $this->assert_equal(
        $this->adapter->response,
        Net_HTTP::Response('ok')
      )->
      assert_equal(
        $this->app->env->auth->user,
        array('login' => 'Ivan', 'password' => '123'));
  }
///     </body>
///   </method>

///   <method name="test_forbidden">
///     <body>
 public function test_forbidden() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/')->
      header('authorization','Basic '.Core_Strings::encode64('Ivan:123'));
    $this->app->exception(new WS_Auth_ForbiddenException());
    WS::Runner($this->adapter)->run($this->service);

    $this->
      assert_equal(
        $this->adapter->response,
        Net_HTTP::Response(Net_HTTP::FORBIDDEN)
      )->
      assert_equal(
        $this->app->env->auth->user,
        array('login' => 'Ivan', 'password' => '123'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>