<?php
/// <module name="Test.IO" version="0.2.0" maintainer="timokhin@techart.ru">

Core::load('Dev.Unit', 'IO');

/// <class name="Test.IO" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_IO implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">

///   <method name="suite" returns="Dev.Unit.TestCase" scope="class">
///     <body>
  static public function suite() { return Dev_Unit::load_with_prefix('Test.IO.', 'Module'); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.Module" extends="Dev.Unit.TestCase">
class Test_IO_Module extends Dev_Unit_TestCase {
///   <protocol name="testing">

///   <method name="test_building">
///     <body>
  public function test_building() {
    $this->
      assert_class('IO.Stream.ResourceStream', IO::stdin())->
      assert((string) IO::stdin()->id === 'Resource id #1')->
      assert_class('IO.Stream.ResourceStream', IO::stdout())->
      assert((string) IO::stdout()->id === 'Resource id #2')->
      assert_class('IO.Stream.ResourceStream', IO::stderr())->
      assert((string) IO::stderr()->id === 'Resource id #3');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// </module>
?>