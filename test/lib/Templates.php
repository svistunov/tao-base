<?php
/// <module name="Test.Templates" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Templates');

/// <class name="Test.Templates" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Templates implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Templates.', 'TemplateCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.Template" extends="Templates.NestableTemplate">
class Test_Templates_Template extends Templates_NestableTemplate {

  public $called_methods = array();

///   <protocol name="supporting">

///   <method name="render_nested" access="protected" returns="string">
///     <args>
///       <arg name="content" type="ArrayObject|null" />
///     </args>
///     <body>
  protected function render_nested(ArrayObject $content = null) {
    $this->called_methods['render_nested'][] = array('content' => $content);
    return 'ok';
  }
///     </body>
///   </method>

///   <method name="get_helpers">
///     <body>
  protected function get_helpers() {
    return Object::Aggregator();
  }
///     </body>
///   </method>

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->called_methods['setup'][] = true;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.TemplateCase" extends="Dev.Unit.TestCase">
class Test_Templates_TemplateCase extends Dev_Unit_TestCase {
  protected $template;
  protected $outer_template;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->template = Core::with(new Test_Templates_Template('test name'))->
      inside($this->outer_template = new Test_Templates_Template('inside'));
  }
///     </body>
///   </method>

///   <method name="test_creating">
///     <body>
  public function test_creating() {
    $this->assert_true($this->template->called_methods['setup'][0]);
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->template->
      with(array('key1' => 'value1'))->
      with('key2', 'value2', 'key3', 'value3');
    $this->asserts->accessing->
      assert_read_only($this->template, $ro = array(
        'name' => 'test name',
        'parms' => array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'),
        'path' => './test name'
      ))->
      assert_exists($this->template, $e = array('helpers'))->
      assert_undestroyable($this->template, array_keys($ro) + $e)->
      assert_missing($this->template);
  }
///     </body>
///   </method>

///   <method name="test_stringify">
///     <body>
  public function test_stringify() {
    $this->asserts->stringifying->
      assert_string($this->template, 'ok')->
      assert_equal($this->template->called_methods['render_nested'][0],
        array('content' => null));
  }
///     </body>
///   </method>

///   <method name="test_nested">
///     <body>
  public function test_nested() {
    $this->outer_template->with('key1', 'value1', 'key3', 'value3');
    $this->template->with('key1', 'new value', 'key2', 'value2');
    $this->asserts->accessing->
      assert_read_only($this->template, $ro = array(
        'container' => $this->outer_template,
        'parms' => array('key1' => 'new value', 'key2' => 'value2', 'key3' => 'value3')
      ))->
      assert_undestroyable($this->template, array_keys($ro));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>