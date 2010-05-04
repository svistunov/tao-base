<?php
/// <module name="Test.WS.Middleware.Cache" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Middleware.Cache', 'Test.WS');

/// <class name="Test.WS.Middleware.Cache" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Middleware_Cache implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Middleware.Cache.', 'ServiceCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Middleware.Cache.ServiceCase" extends="Dev.Unit.TestCase">
class Test_WS_Middleware_Cache_ServiceCase extends Dev_Unit_TestCase {
  protected $app;
  protected $service;
  protected $adapter;
  protected $path;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app = new Test_WS_SaveEnvApp();
    $this->service = WS_Middleware_Cache::Service(
      $this->app,
      'fs://'.($this->path = './test/data/WS/Middleware/Cache/'),
       array(
        '{cache}' => 60
       ));
    $this->adapter = new Test_WS_Adapter();
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    foreach (IO_FS::Query()->recursive()->apply_to(IO_FS::Dir($this->path)) as $file)
      IO_FS::rm($file->path);
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->adapter->request = Net_HTTP::Request('http://localhost/cache/');
    WS::Runner($this->adapter)->run($this->service);
    $cache = $this->app->env->cache;
    $this->assert_class('Cache.Backend', $cache);
    $this->assert_equal(
      $cache->get('ws.middleware.cache.pages:'.$this->adapter->request->uri),
      Net_HTTP::Response('ok'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>