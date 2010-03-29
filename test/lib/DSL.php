<?php
/// <module name="Test.DSL" version="0.2.0" maintainer="timokhin@techart.ru">
Core::load('Dev.Unit', 'DSL');
/// <class name="Test.DSL" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_DSL implements Dev_Unit_TestModuleInterface {
///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.DSL.', 'Builder');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.DSL..Target">
///   <implements interface="Core.CallInterface" />
class Test_DSL_Builder__Target implements Core_CallInterface {

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="Test.DSL..Target">
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args"   type="array" />
///     </args>
///     <body>
  public function __call($method, $args) {
    $this->$method = $args[0];
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.DSL.Builder..Builder" extends="DSL.Builder">
class Test_DSL_Builder__Builder extends DSL_Builder {

///   <protocol name="building">

///   <method name="begin" returns="Test.DSL.Builder..Builder">
///     <args>
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public function begin($name) {
    $this->object->$name = new Test_DSL_Builder__Target();
    return new self($this, $this->object->$name);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.DSL.Builder" extends="Dev.Unit.TestCase">
class Test_DSL_Builder extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_all() {
    $o = Core::with(
      $b = new Test_DSL_Builder__Builder(null, new Test_DSL_Builder__Target()))->
      begin('owner')->
        name('John Doe')->
        email('john.doe@yoyodine.com')->
      end->
      begin('message')->
        begin('headers')->
          title('test message')->
          to('j.smith@virtucon.com')->
          priority(2)->
        end->
        body('a test')->
      end->
    end;

    $this->
      assert_class('Test.DSL.Builder..Target', $o)->
      assert_class('Test.DSL.Builder..Target', $o->owner)->
      assert($o->owner->name === 'John Doe')->
      assert($o->owner->email === 'john.doe@yoyodine.com')->
      assert_class('Test.DSL.Builder..Target', $o->message)->
      assert($o->message->body === 'a test')->
      assert_class('Test.DSL.Builder..Target', $o->message->headers)->
      assert($o->message->headers->title === 'test message')->
      assert($o->message->headers->to === 'j.smith@virtucon.com')->
      assert($o->message->headers->priority === 2);

    $this->asserts->accessing->
      assert_read_only($b, array('object' => $o));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>