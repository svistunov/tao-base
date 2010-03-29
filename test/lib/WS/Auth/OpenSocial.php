<?php
/// <module name="Test.WS.Auth.OpenSocial" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Test.WS', 'Test.WS.Auth', 'Test.WS.Auth.Session', 'WS.Auth.OpenSocial', 'WS.Session');

/// <class name="Test.WS.Auth.OpenSocial" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Auth_OpenSocial implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Auth.OpenSocial.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Auth.OpenSocial.AuthModule" extends="Test.WS.Auth.AuthModule">
///   <implements interface="WS.Auth.OpenSocial.AuthModuleInterface" />
class Test_WS_Auth_OpenSocial_AuthModule extends Test_WS_Auth_AuthModule
  implements WS_Auth_OpenSocial_AuthModuleInterface {

///   <protocol name="performing">

///   <method name="authenticate_remote_user">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function authenticate_remote_user(WS_Environment $env) {
    //do OpenSocial request ...
    return first($this->users);
  }
///     </body>
///   </method>

///   <method name="find_remote_user">
///     <args>
///       <arg name="id" type="int" />
///     </args>
///     <body>
  public function find_remote_user($id) {
    return $this->users[$id];
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Auth.OpenSocial.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Auth_OpenSocial_ServiceCase extends Test_WS_Auth_Session_ServiceCase {

  protected $adapter;
  protected $service;
  protected $app;
  protected $auth_module;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = new Test_WS_Adapter();
    $this->service = WS_Auth_OpenSocial::Service(
      $this->app = new Test_WS_Auth_App(),
      $this->auth_module = new Test_WS_Auth_OpenSocial_AuthModule(),
      '/auth/?url={url}');
    $this->auth_module->users(array(
      1 => array('login' => 'Ivan', 'password' => '123'),
      2 => array('login' => 'Remote', 'password' => '123')
    ));
  }
///     </body>
///   </method>

///   <method name="test_remote_login">
///     <body>
 public function test_remote_login() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    $session = WS_Session::Store();
    unset($session['user_id']);
    $session['remote_user_id'] = 2;
    $this->adapter->request->session($session);
    WS::Runner($this->adapter)->run($this->service);

    $this->assert_equal(
        $this->adapter->response,
        Net_HTTP::Response('ok')
      )->
      assert_equal(
        $this->app->env->auth->user,
        array('login' => 'Remote', 'password' => '123'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>