<?php
/// <module name="Test.WS.REST.DSL" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.REST.DSL');

/// <class name="Test.WS.REST.DSL" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_REST_DSL implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.REST.DSL.', 'ApplicationCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.DSL.ApplicationCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_DSL_ApplicationCase extends Dev_Unit_TestCase {
  protected $app;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->app =
      WS_REST_DSL::Application()->
        media_type('rss', 'application/rss+xml')->
        begin_resource('resource name', 'Test.Resource', '{name:\w+}')->
          for_methods(Net_HTTP::GET | Net_HTTP::POST)->
            bind('bind_name', 'bind_path', 'bind_format')->
          end->
          for_path('test_path')->
            for_format('html,rss')->
              get()->
            end->
          end->
          for_format('html')->
            index()->
            get_for('get_for_path')->
            get_for('get_for_path_name', 'name')->
            post()->
            post_for('post_for_path', 'post_name', 'html,rss,xml')->
            put('put_name', 'xml')->
            put_for('put_for_path')->
            delete()->
            delete_for('delete_for_path')->
            method('method_name', Net_HTTP::GET | Net_HTTP::HEAD)->
            sublocator('sub', 'sub_path')->
          end->
        end->
      end;

  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->asserts->accessing->
      assert_read($this->app, array(
        'media_types' => array (
                        'html' => 'text/html',
                        'js' => 'application/json',
                        'json' => 'application/json',
                        'xml' => 'application/xml',
                        'rss' => 'application/rss+xml'
                         ),
        'default_format' => 'html',
        'prefix' => '',
        'resources' => array('resource name' =>
                          WS_REST::Resource('Test.Resource', '{name:\w+}')->
                            method(
                              WS_REST::Method('bind_name')->
                                path('bind_path')->
                                http(Net_HTTP::GET | Net_HTTP::POST)->
                                produces('bind_format')
                            )->method(
                              WS_REST::Method('index')->
                                path('test_path')->
                                http(Net_HTTP::GET)->
                                produces('html', 'rss')
                            )->method(
                              WS_REST::Method('index')->
                                path('index')->
                                http(Net_HTTP::GET)->
                                produces('html')
                            )->method(
                              WS_REST::Method('get_for_path')->
                                path('get_for_path')->
                                http(Net_HTTP::GET)->
                                produces('html')
                            )->method(
                              WS_REST::Method('name')->
                                path('get_for_path_name')->
                                http(Net_HTTP::GET)->
                                produces('html')
                            )->method(
                              WS_REST::Method('create')->
                                path('index')->
                                http(Net_HTTP::POST)->
                                produces('html')
                            )->method(
                              WS_REST::Method('post_name')->
                                path('post_for_path')->
                                http(Net_HTTP::POST)->
                                produces('html', 'rss', 'xml')
                            )->method(
                              WS_REST::Method('put_name')->
                                path('index')->
                                http(Net_HTTP::PUT)->
                                produces('xml')
                            )->method(
                              WS_REST::Method('put_for_path')->
                                path('put_for_path')->
                                http(Net_HTTP::PUT)->
                                produces('html')
                            )->method(
                              WS_REST::Method('delete')->
                                path('index')->
                                http(Net_HTTP::DELETE)->
                                produces('html')
                            )->method(
                              WS_REST::Method('delete_for_path')->
                                path('delete_for_path')->
                                http(Net_HTTP::DELETE)->
                                produces('html')
                            )->method(
                              WS_REST::Method('method_name')->
                                path('index')->
                                http(Net_HTTP::GET | Net_HTTP::HEAD)->
                                produces('html')
                            )->method(
                              WS_REST::Method('sub')->
                                path('sub_path')->
                                http(0)->
                                produces('html')
                            )
                         )
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>