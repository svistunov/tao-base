<?php
/// <module name="Test.WS.DSL" version="0.2.1" maintainer="timokhin@techart.ru">
Core::configure(
  'WS.DSL', 
  array('middleware' => array('custom_middleware' => 'Test.WS.DSL..CustomMiddleware'),
        'handlers'   => array('custom_handler' => 'Test.WS.DSL..Service')));
Core::load('Dev.Unit', 'WS.DSL', 'WS', 'DB.ORM', 'WS.Auth.OpenSocial');

/// <class name="Test.WS.DSL" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_DSL implements Dev_Unit_TestModuleInterface {
///   <constants>
  const VERSION = '0.2.1';
///   </constants>

///   <protocol name="building">

///   <method name="suite" scope="class" returns="Dev.Unit.TestSuite">
///     <body>
  static public function suite() { return Dev_Unit::load_with_prefix('Test.WS.DSL.', 'Builder'); }
///     </body>
///   </method>
  
///   </protocol>
}
/// </class>

/// <class name="Test.WS.DSL..AuthModule">
///   <implements interface="WS.Auth.OpenSocial.AuthModuleInterface" />
class Test_WS_DSL__AuthModule implements WS_Auth_OpenSocial_AuthModuleInterface {

///   <protocol name="performing">
  
///   <method name="authenticate_remote_user">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function authenticate_remote_user(WS_Environment $env) { throw new Core_NotImplementedException(); } 
///     </body>
///   </method>

///   <method name="find_remote_user">
///     <args>
///       <arg name="id" type="int" />
///     </args>
///     <body>
  public function find_remote_user($id) { throw new Core_NotImplementedException(); }
///     </body>
///   </method>
  
///   <method name="authenticate" returns="mixed">
///     <args>
///       <arg name="login"    type="string" />
///       <arg name="password" type="string" />
///     </args>
///     <body>
  public function authenticate($login, $password) { throw new Core_NotImplementedException(); }
///     </body>
///   </method>

///   <method name="find_user" returns="mixed">
///     <args>
///       <arg name="id" type="int" />
///     </args>
///     <body>
  public function find_user($id) { throw new Core_NotImplementedException(); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.WS.DSL..Service">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_DSL__Service implements WS_ServiceInterface {
  
  public $env;
  
///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct() {}
///     </body>
///   </method>

///   </protocol>
  
///   <protocol name="performing">

///   <method name="run">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) { $this->env = $env; }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.WS.DSL..CustomMiddleware">
class Test_WS_DSL__CustomMiddleware extends WS_MiddlewareService {

///   <protocol name="performing">

///   <method name="__construct">
///     <args>
///       <arg name="application" type="WS.ServiceInterface" />
///       <arg name="value" type="string" />
///     </args>
///     <body>
  public function __construct(WS_ServiceInterface $application, $value) {
    parent::__construct($application);
    $this->value = (string) $value;
  }
///     </body>
///   </method>
  
///   <method name="run">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $env->custom_middleware = $this->value;
    $this->application->run($env);
  }
///     </body>
///   </method>
  
///   </protocol>
}
/// </class>

/// <class name="Test.WS.DSL.ORMSession" extends="DB.ORM.ConnectionMapper">
class Test_WS_DSL__ORMSession extends DB_ORM_ConnectionMapper {}
/// </class>


/// <class name="Test.WS.DSL.Builder" extends="Dev.Unit.TestCase">
class Test_WS_DSL_Builder extends Dev_Unit_TestCase {
  
  protected $builder;
  protected $handler;
  
///   <protocol name="testing">
  
///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->builder = WS_DSL::application();
    $this->handler = new Test_WS_DSL__Service();
  }
///     </body>
///   </method>
  
  
///   <method name="test_all">
///     <body>
  public function test_builder() {
    $this->
      assert_class('WS.DSL.Builder', $this->builder)->
      assert_class('WS.DSL.Builder', WS_DSL::Builder())->
      assert_class('Test.WS.DSL..Service', $this->builder->handler($this->handler));
  }
///     </body>
///   </method>

///   <method name="test_environment">
///     <body>
  public function test_chain() {
    $this->assert_class(
      'WS.Middleware.Environment.Service', 
      $s = $this->builder->
        environment(array('test_value' => 'test'))->
        cache('dummy://')->
        handler($this->handler));
     $s->run($env = new WS_Environment());
     
     $this->
      assert($env->test_value == 'test')->
      assert_class('Cache.Dummy.Backend', $env->cache);
  }
///     </body>
///   </method>
  
///   <method name="test_middleware">
///     <body>
  public function test_middleware() {
    $this->
      assert_class(
        'WS.Middleware.Environment.Service', 
        $this->builder->environment(array())->handler($this->handler))->
      assert_class(
        'WS.Middleware.Config.Service', 
        $this->builder->config('config.php')->handler($this->handler))->
      assert_class(
        'WS.Middleware.DB.Service',
        $this->builder->db('test@localhost/test')->handler($this->handler))->
      assert_class(
        'WS.Middleware.ORM.Service',
        $this->builder->orm(new Test_WS_DSL__ORMSession(), 'test@localhost/test')->handler($this->handler))->
      assert_class(
        'WS.Middleware.Cache.Service',
        $this->builder->cache('dummy://')->handler($this->handler))->
      assert_class(
        'WS.Middleware.Status.Service', 
        $this->builder->status(array(404 => 'http/404'))->handler($this->handler))->
      assert_class(
        'WS.Middleware.Template.Service',
        $this->builder->template()->handler($this->handler))->
      assert_class(
        'WS.Session.Service',
        $this->builder->session()->handler($this->handler))->
      assert_class(
        'WS.Auth.Session.Service',
        $this->builder->auth_session(new Test_WS_DSL__AuthModule())->handler($this->handler))->
      assert_class(
        'WS.Auth.Basic.Service',
        $this->builder->auth_basic(new Test_WS_DSL__AuthModule())->handler($this->handler))->
      assert_class(
        'WS.Auth.OpenSocial.Service',
        $this->builder->auth_opensocial(new Test_WS_DSL__AuthModule())->handler($this->handler))->
      assert_class(
        'WS.REST.Dispatcher',
        $this->builder->application_dispatcher(array()));
  }
///     </body>
///   </method>
  
///   <method name="test_custom_methods">
///     <body>
  public function test_custom_methods() {
    $this->
      assert_class(
        'Test.WS.DSL..CustomMiddleware', 
        $app = $this->builder->
          custom_middleware('custom')->
          custom_handler());

     $app->run($env = new WS_Environment());
     $this->assert($env->custom_middleware == 'custom');
  }
///     </body>
///   </method>
  
///   </protocol>
}
/// </class>

/// </module>
?>