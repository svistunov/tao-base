<?php
/// <module name="Test.L10N" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'L10N');

/// <class name="Test.L10N" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_L10N implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.L10N.', 'RUCase' , 'ENCase' );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.L10N.RUCase" extends="Dev.Unit.TestCase">
class Test_L10N_RUCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    L10N::locale('ru');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_equal(L10N::month_name(1), 'январь')->
      assert_equal(L10N::weekday_name(2), 'вторник')->
      assert_equal(L10N::strftime('%A %a %b %B %d %B %e %B', '2009-04-05 23:00:01', L10N_RU::PREPOSITIONAL),
        'воскресенье вск апр апреле 05 апреля 5 апреля')->
      assert_equal(L10N::locale()->plural_for(10001, 'строка', 'строки', 'строк'), 'строка')->
      assert_equal(L10N::locale()->plural_for(0, 'строка', 'строки', 'строк'), 'строк')->
      assert_equal(L10N::locale()->plural_for(4, 'строка', 'строки', 'строк'), 'строки');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.L10N.ENCase" extends="Dev.Unit.TestCase">
class Test_L10N_ENCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    L10N::locale('en');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_equal(L10N::month_name(1), 'january')->
      assert_equal(L10N::weekday_name(2), 'tuesday')->
      assert_equal(L10N::strftime('%A %a %b %B %d %B %e %B', '2009-04-05 23:00:01'),
        'sunday sun apr april 05 april 5 april')
      ;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>