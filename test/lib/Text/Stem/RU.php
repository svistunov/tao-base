<?php
/// <module name="Test.Text.Stem.RU" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Text.Stem.RU');

/// <class name="Test.Stem.RU" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Text_Stem_RU implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Text.Stem.RU.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Text.Stem.RU.Case" extends="Dev.Unit.TestCase">
class Test_Text_Stem_RU_Case extends Dev_Unit_TestCase {
  protected $stemmer;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->stemmer = Text_Stem_RU::Stemmer();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->
      assert_equal($this->stemmer->stem('курица'), 'куриц')->
      assert_equal($this->stemmer->stem('колбаса'), 'колбас')->
      assert_equal($this->stemmer->stem('декабря'), 'декабр')->
      assert_equal($this->stemmer->stem('январские'), 'январск')->
      assert_equal($this->stemmer->stem('велопипедная'), 'велопипедн');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>