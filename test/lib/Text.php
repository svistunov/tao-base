<?php
/// <module name="Test.Text" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Text');

/// <class name="Test.Text" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Text implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Text.', 'TokenizerCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Text.TokenizerCase" extends="Dev.Unit.TestCase">
class Test_Text_TokenizerCase extends Dev_Unit_TestCase {
  protected $data;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->data = "line0\nline1\nline2";
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    foreach (Text::Tokenizer($this->data) as $k => $v)
      $this->assert_equal($v, 'line'.$k);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>