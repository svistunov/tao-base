<?php
/// <module name="Test.Fn" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Fn');

/// <class name="Test.Fn" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Fn implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Fn.', 'FnCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Fn.Case" extends="Dev.Unit.TestCase">
class Test_Fn_FnCase extends Dev_Unit_TestCase {

///   <protocol name="supporting">

///   <method name="map">
///     <args>
///       <arg name="key" type="int" />
///       <arg name="value" />
///     </args>
///     <body>
  public function map($key, $value) {
    return array($key,$value*2);
  }
///     </body>
///   </method>

///   <method name="filter">
///     <args>
///       <arg name="key" type="int" />
///       <arg name="value" />
///     </args>
///     <body>
  public function filter($key, $value) {
    return $value%2;
  }
///     </body>
///   </method>

///   <method name="generate">
///     <args>
///       <arg name="count" type="int" />
///     </args>
///     <body>
  public function generate($count) {
    return $count == 5 ? null : array($count, $count*2);
  }
///     </body>
///   </method>

///   <method name="value">
///     <body>
  public function value() {
    return 2;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_map">
///     <body>
 public function test_map() {
    $this->asserts->iterating->
      assert_read(
        Fn::map(array($this,'map'), array(1, 2, 3, 4)),
        array(2, 4, 6, 8)
      );
  }
///     </body>
///   </method>

///   <method name="test_filter">
///     <body>
 public function test_filter() {
    $this->asserts->iterating->
      assert_read(
        Fn::Filter(array($this,'filter'), array(1, 2, 3, 4)),
        array(0 => 1, 2 => 3)
      );
  }
///     </body>
///   </method>

///   <method name="test_joiner">
///     <body>
 public function test_joiner() {
    $this->asserts->iterating->
      assert_read(
        Fn::join(array(0 => 1, 1 => 2),array(2 => 3, 3 => 4),array(4 => 5,5 => 6)),
        array(1, 2, 3, 4, 5, 6)
      );
  }
///     </body>
///   </method>

///   <method name="test_generator">
///     <body>
 public function test_generator() {
    $this->asserts->iterating->
      assert_read(
        Fn::generate(array($this, 'generate')),
        array(0,2,4,6,8)
      );
  }
///     </body>
///   </method>

///   <method name="test_singular">
///     <body>
  public function test_singular() {
    $this->asserts->iterating->
      assert_read(
        Fn::singular($this, 'value'),
        array(2)
      );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>