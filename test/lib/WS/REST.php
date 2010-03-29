<?php
/// <module name="Test.WS.REST" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.REST', 'Test.WS', 'WS.REST.DSL', 'WS.DSL');

/// <class name="Test.WS.REST" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_REST implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.REST.',
      'MethodCase', 'ResourceCase', 'ApplicationCase', 'DispatcherCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.MethodCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_MethodCase extends Dev_Unit_TestCase {
  protected $method;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->method = WS_REST::Method('method name')->
      http(Net_HTTP::POST)->
      path('event_{id:\d\d}')->
      produces('html', 'xml', 'rss');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
 public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->method, array(
        'name' => 'method name',
        'formats' => array('html', 'xml', 'rss')
      ))->
      assert_read($this->method, array(
        'http_mask' => Net_HTTP::POST,
        'path' => new WS_REST_URI_Template('event_{id:\d\d}')
      ))->
      assert_write($this->method, array(
        'http_mask' => Net_HTTP::GET,
        'path' => new WS_REST_URI_Template('new path')
      ))->
      assert_missing($this->method)->
      assert_undestroyable($this->method, array(
        'name', 'path', 'formats', 'http_mask'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.ResourceCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_ResourceCase extends Dev_Unit_TestCase {
  protected $resource;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->resource = WS_REST::Resource('-Test.ClassName', '{id:\d\d}')->
      produces('html', 'xml')->
      method(WS_REST::Method('method1'))->
      method(WS_REST::Method('method2'));
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->resource, $o = array(
        'path' => WS_REST_URI::Template('{id:\d\d}'),
        'classname' => 'Test.ClassName',
        'formats' => array('html', 'xml'),
        'is_module' => true
      ))->
      assert_undestroyable($this->resource, array_keys($o))->
      assert_missing($this->resource);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.Application" extends="WS.REST.Application">
///   <implements interface="Core.CallInterface" />
class Test_WS_REST_Application extends WS_REST_Application implements Core_CallInterface {

  protected $opt4 = 'value for opt4';
  protected $called_methods = array();

///   <protocol name="creating">

///   <method name="setup">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  protected function setup(array $options = array()) {
    $this->add_call('setup', array('options' => $options));
    $this->options = $this->options + $options;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="before_run">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  protected function before_run(WS_Environment $env) {
    $this->add_call('before_run', array('env' => $env));
  }
///     </body>
///   </method>

///   <method name="after_instantiate">
///     <args>
///       <arg name="resource" />
///     </args>
///     <body>
  protected function after_instantiate($resource) {
    $this->add_call('after_instantiate', array('resource' => $resource));
  }
///     </body>
///   </method>

///   <method name="get_opt3">
///     <body>
  protected function get_opt3() {
    return 'value for opt3';
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling">

///   <method name="__call">
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  public function __call($method, $args) {
    $this->add_call($method, $args);
    return $this;
  }
///     </body>
////  </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="add_call" access="private">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  private function add_call($name, $args) {
    $this->called_methods[$name][] = $args;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.Mock">
///   <implements interface="Core.CallInterface" />
///   <implements interface="Core.PropertyAccessInterface" />
class Test_WS_REST_Mock implements
  Core_CallInterface,
  Core_PropertyAccessInterface {

  protected $called_methods = array();

///   <protocol name="performing">

///   <method name="add_call">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  protected function add_call($name, $args) {
    $this->called_methods[$name][] = $args;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling">

///   <method name="__call">
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  public function __call($method, $args) {
    $this->add_call($method, $args);
    return $this;
  }
///     </body>
////  </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'called_methods':
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
    throw new Core_ReadOnlyObjectException($this);
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
      case 'called_methods':
        return true;
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
    throw new Core_ReadOnlyObjectException($this);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.Resource" extends="Test.WS.REST.Mock">
class Test_WS_REST_Resource extends Test_WS_REST_Mock {

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="path" type="string" />
///       <arg name="application" type="WS.REST.Application" />
///       <arg name="env" type="WS.Environment" />
///       <arg name="format" type="string" />
///       <arg name="paramaters" type="array" />
///       <arg name="parms" type="array" />
///       <arg name="request" type="Net.HTTP.Request" />
///       <arg name="null" />
///       <arg name="def" type="string" default="'default value'" />
///     </args>
///     <body>
  public function __construct($path , $application, $env, $format, $parameters, $parms, $request, $null, $def = 'default value') {
    $this->add_call('__construct', array(
      'path' => $path,
      'application' => $application,
      'env' => $env,
      'format' => $format,
      'parameters' => $parameters,
      'parms' => $parms,
      'request' => $request,
      'null' => $null,
      'def' => $def
    ));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="test_make_args">
///     <args>
///       <arg name="date" type="string" />
///       <arg name="application" type="WS.REST.Application" />
///       <arg name="env" type="WS.Environment" />
///       <arg name="format" type="string" />
///       <arg name="paramaters" type="array" />
///       <arg name="parms" type="array" />
///       <arg name="request" type="Net.HTTP.Request" />
///       <arg name="null" />
///       <arg name="def" type="string" default="'default value'" />
///     </args>
///     <body>
  public function test_make_args($date, $application, $env, $format, $parameters, $parms, $request, $null, $def = 'default value') {
    $this->add_call('test_make_args', array(
      'date' => $date,
      'application' => $application,
      'env' => $env,
      'format' => $format,
      'parameters' => $parameters,
      'parms' => $parms,
      'request' => $request,
      'null' => $null,
      'def' => $def
    ));
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   <method name="sublocator">
///     <args>
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public function sublocator($name) {
    $this->add_call('sublocator', array('name' => $name));
    return new Test_WS_REST_SubResource($name);
  }
///     </body>
///   </method>

///   <method name="test_http_mask">
///     <body>
  public function test_http_mask() {
    $this->add_call('test_http_mask', array());
    return 'ok';
  }
///     </body>
///   </method>

///   <method name="test_media_type">
///     <args>
///       <arg name="format" type="string" />
///     </args>
///     <body>
  public function test_media_type($format) {
    $this->add_call('test_media_type', array('format' => $format));
    return 'ok';
  }
///     </body>
///   </method>



///   <method name="test_accept">
///     <args>
///       <arg name="format" type="string" />
///     </args>
///     <body>
  public function test_accept($format) {
    $this->add_call('test_accept', array('format' => $format));
    return 'ok';
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

///   <class name="Test.WS.REST.SubResource" extends="Test.WS.REST.Mock">
class Test_WS_REST_SubResource extends Test_WS_REST_Mock {

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public function __construct($name) {
    $this->add_call('__construct', array(
      'id' => $name
    ));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="body">
///     <body>
  public function index() {
    $this->add_call('index', array());
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.ApplicationCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_ApplicationCase extends Dev_Unit_TestCase {
  protected $adapter;
  protected $app;
  protected $res;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = new Test_WS_Adapter();
    $this->app = WS_REST_DSL::Application(
      new Test_WS_REST_Application('blog',
        array('opt1' => 'value', 'opt2' => 'value')))->
      media_type('rss', 'application/rss+xml')->
        begin_resource('resource name', 'Test.WS.REST.Resource', '{path:blog}')->
          for_format('html,xml,rss')->
            get_for('index', 'test_accept')->
            get_for('test_media_type', 'test_media_type')->
          end->
          for_format('html')->
            get_for('{date:\d\d-\d\d}', 'test_make_args')->
            method('test_http_mask', Net_HTTP::POST | Net_HTTP::PUT, 'test_http_mask')->
            sublocator('sublocator', '{name:\w+}')->
          end->
        end->
        begin_resource('sublocator', 'Test.WS.REST.SubResource', NULL)->
          for_format('html')->
            index()->
          end->
        end->
    end;
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->app, array(
        'options' => array('opt1' => 'value', 'opt2' => 'value'),
        'opt4' => 'value for opt4',
        'opt3' => 'value for opt3',
        'called_methods' => array('setup' => array(
                                    array('options' => array(
                                   'opt1' => 'value', 'opt2' => 'value')))),
        'media_types' => array (
                        'html' => 'text/html',
                        'js' => 'application/json',
                        'json' => 'application/json',
                        'xml' => 'application/xml',
                        'rss' => 'application/rss+xml'
                         ),
        'default_format' => 'html',
        'prefix' => 'blog',
        'resources' => array('resource name' => $res =
                        WS_REST::Resource('Test.WS.REST.Resource', '{path:blog}')->
                          method(
                          WS_REST::Method('test_accept')->
                            path('index')->
                            http(Net_HTTP::GET)->
                            produces('html', 'xml', 'rss')
                        )->method(
                          WS_REST::Method('test_media_type')->
                            path('test_media_type')->
                            http(Net_HTTP::GET)->
                            produces('html', 'xml', 'rss')
                        )->method(
                          WS_REST::Method('test_make_args')->
                            path('{date:\d\d-\d\d}')->
                            http(Net_HTTP::GET)->
                            produces('html')
                        )->method(
                          WS_REST::Method('test_http_mask')->
                            path('test_http_mask')->
                            http(Net_HTTP::POST | Net_HTTP::PUT)->
                            produces('html')
                        )->method(
                          WS_REST::Method('sublocator')->
                            path('{name:\w+}')->
                            http(0)->
                            produces('html')
                        ),
                        'sublocator' => $sub = WS_REST::Resource('Test.WS.REST.SubResource', NULL)->
                          method(
                            WS_REST::Method('index')->
                              path('index')->
                              http(Net_HTTP::GET)->
                              produces('html')
                          )
                       ),
        'classes' => array('Test_WS_REST_Resource' => $res, 'Test_WS_REST_SubResource' => $sub)
      ));
  }
///     </body>
///   </method>

///   <method name="test_make_args">
///     <body>
  public function test_make_args() {

    $this->adapter->request = Net_HTTP::Request('http://localhost/blog/01-12.html?q=search');
    WS::Runner($this->adapter)->run($this->app);

    $this->assert_equal($this->adapter->response,
      Net_HTTP::Response('ok')->header('content_type', 'text/html'));

    //params for first call 'after_instantiate' method ( after_instantiate($resource) )
    $r = $this->app->called_methods['after_instantiate'][0]['resource'];
    $this->assert_class($this->app->resources['resource name']->classname, $r);

    //params for first call 'test_make_args':
    $method_parms = $r->called_methods['test_make_args'][0];
    //params for call '__construct'
    $construct_parms = $r->called_methods['__construct'][0];

    $this->asserts->indexing->
      assert_read($method_parms, array(
        'date' => '01-12',
        'format' => 'text/html') + $common = array(
        'def' => 'default value',
        'null' => null,
        'application' => $this->app,
        'parms' => array('q' => 'search'),
        'parameters' => array('q' => 'search'),
        'request' => $this->adapter->request,
        'env' => $env = WS::Environment()->
                  app($this->app)->
                  request($this->adapter->request)
      ))->
      assert_read($construct_parms, array(
        'path' => 'blog',
        'format' => null) + $common
      );
    $this->assert_equal($this->app->called_methods['before_run'][0]['env'], $env);

  }
///     </body>
///   </method>

//TODO: добавить проверку количества вызывов

///   <method name="test_sublocator">
///     <body>
  public function test_sublocator() {
    $this->adapter->request = Net_HTTP::Request('http://locahost/blog/vasya/');
    WS::Runner($this->adapter)->run($this->app);
    $this->assert_equal($this->adapter->response,
      Net_HTTP::Response('ok')->header('content_type', 'text/html'));

    //first call
    $main_res = $this->app->called_methods['after_instantiate'][0]['resource'];
    //second call
    $sub_res = $this->app->called_methods['after_instantiate'][1]['resource'];

    $this->assert_equal($main_res->called_methods['sublocator'], array(
        0 => array('name' => 'vasya')//first call params
    ));
    $this->assert_equal($sub_res->called_methods, array(
      '__construct' => array(
        0 => array('id' => 'vasya')
      ),
      'index' => array(
        0 => array()
       )
    ));
  }
///     </body>
///   </method>

///   <method name="test_http_mask">
///     <body>
  public function test_http_mask() {
    foreach (array(
          Net_HTTP::GET => Net_HTTP::NOT_FOUND,
          Net_HTTP::POST => Net_HTTP::OK,
          Net_HTTP::PUT => Net_HTTP::OK,
          Net_HTTP::DELETE => Net_HTTP::NOT_FOUND
      ) as $method => $status) {
      $this->adapter->request = Net_HTTP::Request('http://locahost/blog/test_http_mask.html')->
        method($method);
      WS::Runner($this->adapter)->run($this->app);
      $this->assert_equal(
        $r = $this->adapter->response->status,
        $n = Net_HTTP::Status($status),
        sprintf("status not compare for method %s: %s != %s ",
          Net_HTTP::method_name_for($method),
          $this->stringify($r),
          $this->stringify($n)));
    }

  }
///     </body>
///   </method>

///   <method name="test_media_type">
///     <body>
  public function test_media_type() {
    foreach (array(
      'html' => Net_HTTP::Response('ok')->content_type($this->app->media_type_for('html')),
      'xml' => Net_HTTP::Response('ok')->content_type($this->app->media_type_for('xml')),
      'rss' => Net_HTTP::Response('ok')->content_type($this->app->media_type_for('rss')),
      'js' => Net_HTTP::not_found()
    ) as $ext => $response) {
      $this->adapter->request = Net_HTTP::Request('http://locahost/blog/test_media_type.'.$ext);
      WS::Runner($this->adapter)->run($this->app);
      $this->
        assert_equal(
          $this->adapter->response,
          $response,
          "wrong response for extention '$ext'");
    }
  }
///     </body>
///   </method>

///   <method name="test_accept">
///     <body>
  public function test_accept() {
    foreach (array(
      /* Chrome */
      'application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5' =>
        Net_HTTP::Response('ok')->content_type('application/xml'),
      /*IE*/
      'image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, */*' =>
        Net_HTTP::Response('ok')->content_type('text/html'),
      /* Opera */
      'text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/x-xbitmap, */*;q=0.1' =>
        Net_HTTP::Response('ok')->content_type('text/html'),
      /* FireFox */
      'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' =>
        Net_HTTP::Response('ok')->content_type('text/html'),
      /* Custom */
        '*/*' =>
          Net_HTTP::Response('ok')->content_type('text/html'),
        'tex/html, application/xml;q=0.9, application/rss+xml;q=1.0, */*;q=0.1' =>
          Net_HTTP::Response('ok')->content_type('application/rss+xml')
    ) as $accept => $response) {
      $this->adapter->request = Net_HTTP::Request('http://localhost/blog/')->
        accept($accept);
      WS::Runner($this->adapter)->run($this->app);
      $this->assert_equal(
        $this->adapter->response,
        $response,
        "wrong response for accept: $accept");
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.NewsApp">
class Test_WS_REST_NewsApp extends WS_REST_Application {

///   <protocol name="creating">

///   <method name="setup">
///     <body>
  protected function setup() {
    WS_REST_DSL::Application($this)->
      begin_resource('res', 'Test.WS.REST.NewsResource')->
        for_format('html')->
          index()->
        end->
      end->
    end;
  }
///     </body>
///   </method>
///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.VideoApp" extends="WS.REST.Application">
class Test_WS_REST_VideoApp extends WS_REST_Application {

///   <protocol name="creating">

///   <method name="setup">
///     <body>
  protected function setup() {
    WS_REST_DSL::Application($this)->
      begin_resource('res', 'Test.WS.REST.VideoResource')->
        for_format('html')->
          index()->
        end->
      end->
    end;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.DefaultApp" extends="WS.REST.Application">
class Test_WS_REST_DefaultApp extends WS_REST_Application {

///   <protocol name="creating">

///   <method name="setup">
///     <body>
  protected function setup() {
    WS_REST_DSL::Application($this)->
      begin_resource('res', 'Test.WS.REST.DefaultResource')->
        for_format('html')->
          index()->
        end->
      end->
    end;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.NewsResource">
class Test_WS_REST_NewsResource {
///   <protocol name="performing">

///   <method name="index">
///     <body>
  public function index() {
    return 'news';
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.VideoResource">
class Test_WS_REST_VideoResource {
///   <protocol name="performing">

///   <method name="index">
///     <body>
  public function index() {
    return 'video';
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.DefaultResource">
class Test_WS_REST_DefaultResource {
///   <protocol name="performing">

///   <method name="index">
///     <body>
  public function index() {
    return 'default';
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.DispatcherCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_DispatcherCase extends Dev_Unit_TestCase {
  protected $dispatcher;
  protected $adapter;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = new Test_WS_Adapter();
    $this->dispatcher = WS_DSL::application_dispatcher(array(
      'news' => 'Test.WS.REST.NewsApp',
      'video' => 'Test.WS.REST.VideoApp',
    ), 'Test.WS.REST.DefaultApp');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
   foreach (array(
    '/news/' => Net_HTTP::Response('news')->content_type('text/html'),
    '/video/' => Net_HTTP::Response('video')->content_type('text/html'),
    '/' => Net_HTTP::Response('default')->content_type('text/html'),
    '/missing/' => Net_HTTP::not_found()
   ) as $prefix => $response) {
      $this->adapter->request = Net_HTTP::Request('http://localhost'.$prefix);
      WS::Runner($this->adapter)->run($this->dispatcher);
      $this->assert_equal(
        $this->adapter->response,
        $response,
        "wrong response for prefix: $prefix");
   }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>