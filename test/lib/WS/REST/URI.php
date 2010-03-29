<?php
/// <module name="Test.WS.REST.URI" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.REST.URI');

/// <class name="Test.WS.REST.URI" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_REST_URI implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.REST.URI.', 'TemplateCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.REST.URI.TemplateCase" extends="Dev.Unit.TestCase">
class Test_WS_REST_URI_TemplateCase extends Dev_Unit_TestCase {
  protected $template;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->template = WS_REST_URI::Template('{n:\d+}/{name:\w+}');
  }
///     </body>
///   </method>

///   <method name="test_creating">
///     <body>
 public function test_creating() {
    $this->asserts->accessing->
      assert_read_only($this->template, $o = array(
        'template' => '{n:\d+}/{name:\w+}',
        'regexp' => '{^/(\d+)/(\w+)(/.*)?}',
        'parms' => array('n', 'name')
      ))->
      assert_undestroyable($this->template, array_keys($o))->
      assert_missing($this->template);
  }
///     </body>
///   </method>

///   <method name="test_match">
///     <body>
  public function test_match() {
    $m = $this->template->match('/567/show/resource/55/');

    $this->assert_class('WS.REST.URI.MatchResults', $m);

    $this->asserts->accessing->
      assert_read_only($m, $o = array(
        'tail' => '/resource/55/',
        'parms' => array('n' => '567', 'name' => 'show')
      ))->
      assert_undestroyable($m, array_keys($o))->
      assert_missing($m);

    $this->asserts->indexing->
      assert_read_only($m, $o = array(
        'n' => '567',
        'name' => 'show'
      ))->
      assert_undestroyable($m, array_keys($o))->
      assert_missing($m);

    $this->asserts->iterating->
      assert_read($m, $o);
  }
///     </body>
///   </method>

///   <method name="tets_empty_template">
///     <body>
  public function test_empty_template() {
    $t = WS_REST_URI::Template('');
    $m = $t->match('/news/5/');
    $this->asserts->accessing->
      assert_read_only($m, array(
        'tail' => '/news/5/',
        'parms' => array()
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>