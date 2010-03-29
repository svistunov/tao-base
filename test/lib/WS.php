<?php
/// <module name="Test.WS" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS');

/// <class name="Test.WS" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.', 'EnvironmentCase', 'RunnerCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.EnvironmentCase" extends="Dev.Unit.TestCase">
class Test_WS_EnvironmentCase extends Dev_Unit_TestCase {
  protected $env;
  protected $parent;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->env = WS::Environment(
      $this->parent = WS::Environment()->
      a('parent_a')->
      b('parent_b')->
      c('parent_c')
    )->
      a('a')->
      b('b');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
 public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->env, array(
        'a' => 'a',
        'b' => 'b',
        'c' => 'parent_c'
      ))->
      assert_write($this->env, array(
        'd' => 'value d',
        'e' => 'value e'
      ))->
      assert_nullable($this->env, array('c'));
    unset($this->env->a);
    unset($this->env->b);
    $this->asserts->accessing->
      assert_read($this->env, array(
        'a' => 'parent_a',
        'b' => 'parent_b'
      ));
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
 public function test_indexing() {
    $this->asserts->indexing->
      assert_read($this->env, array(
        'a' => 'a',
        'b' => 'b',
        'c' => 'parent_c'
      ))->
      assert_write($this->env, array(
        'd' => 'value d',
        'e' => 'value e'
      ))->
      assert_nullable($this->env, array('c'));
    unset($this->env['a']);
    unset($this->env['b']);
    $this->asserts->indexing->
      assert_read($this->env, array(
        'a' => 'parent_a',
        'b' => 'parent_b'
      ));
  }
///     </body>
///   </method>

///   <method name="test_aprent_accessing">
///     <body>
  public function test_parent_accessing() {
    $this->asserts->accessing->
      assert_read( $this->parent, array(
        'a' => 'parent_a',
        'b' => 'parent_b',
        'c' => 'parent_c'
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.SaveEnvApp">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_SaveEnvApp implements WS_ServiceInterface {
  public $env;
///   <protocol name="processing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" brief="объект окружения" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->env = $env;
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Adapter">
///   <implements interface="WS.AdapterInterface" />
///   <implements interface="Core.PropertyAccessInterface" />
class Test_WS_Adapter implements WS_AdapterInterface, Core_PropertyAccessInterface {

  private $request;
  private $response;

///   <protocol name="performing">

///   <method name="make_request" returns="Net.HTTP.Request">
///     <body>
  public function make_request() {
    return $this->request;
  }
///     </body>
///   </method>

///   <method name="process_response">
///     <args>
///       <arg name="response" type="Net.HTTP.Response" />
///     </args>
///     <body>
  public function process_response(Net_HTTP_Response $response) {
    $this->response = $response;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'request': case 'response':
        return $this->$property;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'request':
        return $this->request = $value;
      case 'response':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'request': case 'response':
        return $this->$property != null;
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) {
    switch ($property) {
      case 'request': case 'response':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Tes.tWS.App1" extends="WS.MiddlewareService">
class Test_WS_App1 extends WS_MiddlewareService {
  public $env;
///   <protocol name="performing">

///   <method name="run">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->env = $env;
    $env->app1_values = array('app1_value1', 'app1_value2');
    $env->common = 'value from app1';
    $res = $this->application->run($env);
    $res->body = $res->body."\nadded by app1";
    return $res;
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.WS.App2">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_App2 implements WS_ServiceInterface {
  public $env;
///   <protocol name="performing">

///   <method name="run">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->env = WS::Environment($env);

    $this->env->app2_values = array('app2_value1', 'app2_value2');
    $this->env->common = 'value from app2';

    $response = Net_HTTP::Response('from app2');

    return $response;
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.WS.RunnerCase" extends="Dev.Unit.TestCase">
class Test_WS_RunnerCase extends Dev_Unit_TestCase {
  protected $adapter;
  protected $app1;
  protected $app2;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = new Test_WS_Adapter();
    $this->adapter->request = Net_HTTP::Request(
      'http://username:password@hostname/path?arg=value');
    WS::Runner($this->adapter)->run(
      $this->app1 = new Test_WS_App1($this->app2 = new Test_WS_App2()));
  }
///     </body>
///   </method>

///   <method name="test_good">
///     <body>
  public function test_run() {
    $this->
      assert_equal($this->adapter->response, Net_HTTP::Response("from app2\nadded by app1"));
  }
///     </body>
///   </method>

///   <method name="test_bad">
///     <body>
  public function test_environment() {
    $this->asserts->accessing->
      assert_read($this->app2->env, array(
        'app1_values' => array('app1_value1', 'app1_value2'),
        'app2_values' => array('app2_value1', 'app2_value2'),
        'common' => 'value from app2'
      ));
    $this->asserts->accessing->
      assert_read($this->app1->env, array(
        'app1_values' => array('app1_value1', 'app1_value2'),
        'common' => 'value from app1'
      ));
    $this->assert_false(isset($this->app1->env->app2_values));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>