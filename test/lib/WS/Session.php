<?php
/// <module name="Test.WS.Session" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Session', 'Test.WS');

/// <class name="Test.WS.Session" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Session implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Session.',
      'SessionCase', 'FlashCase', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Session.SessionCase" extends="Dev.Unit.TestCase">
class Test_WS_Session_SessionCase extends Dev_Unit_TestCase {
  protected $session;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->session  = WS_Session::Store();
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
 public function test_accessing() {
    $this->asserts->accessing->
      assert_exists_only($this->session, $o = array('id'))->
      assert_undestroyable($this->session, $o)->
      assert_missing($this->session);
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_write($this->session, $o = array(
        'key1' => 'value1',
        'key2' => 'value2'
      ))->
      assert_nullable($this->session, array_keys($o));
  }
///     </body>
///   </method>

///   <method name="test_performing">
///     <body>
  public function test_performing() {
    $this->assert_equal($this->session->get('key', 'default'), 'default');
    $this->session['key'] = 'value';
    $this->assert_equal($this->session->get('key', 'default'), 'value');

    $this->session->commit();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Session.FlashCase" extends="Dev.Unit.TestCase">
class Test_WS_Session_FlashCase extends Dev_Unit_TestCase {
  protected $flash;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->flash = WS_Session::Flash(array(
      'key1' => 'now_value1',
      'key2' => 'now_value2'
    ));
    $this->flash['key1'] = 'later_value1';
    $this->flash['key2'] = 'later_value2';
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->flash, $o = array(
        'now' => array('key1' => 'now_value1', 'key2' => 'now_value2'),
        'later' => array('key1' => 'later_value1', 'key2' => 'later_value2'),
      ))->
      assert_undestroyable($this->flash, array_keys($o))->
      assert_missing($this->flash);
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_read($this->flash, array(
        'key1' => 'now_value1',
        'key2' => 'now_value2'
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Session.App">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_Session_App implements WS_ServiceInterface {
  public $env;
///   <protocol name="processing">

///   <method name="run" returns="mixed">
///     <brief>Выполняет обработку запроса</brief>
///     <args>
///       <arg name="env" type="WS.Environment" brief="объект окружения" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->env = $env;
    $env->flash['key'] = 'value';
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Session.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Session_ServiceCase extends Dev_Unit_TestCase {
  protected $adapter;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/');
    WS::Runner($this->adapter)->
      run(WS_Session::Service($app = new Test_WS_Session_App()));

    $this->assert_equal(
      $this->adapter->response,
      Net_HTTP::Response('ok'));
    $this->assert_class('WS.Session.Store', $this->adapter->request->session);
    $this->assert_class('WS.Session.Flash', $app->env->flash);
    $this->assert_null($app->env->flash['key']);

    WS::Runner($this->adapter)->
      run(WS_Session::Service($app));

    $this->assert_equal($app->env->flash['key'], 'value');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>