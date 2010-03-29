<?php
/// <module name="Test.Time">
Core::load('Dev.Unit', 'Time');

/// <class name="Test.Time" stereotype="module">
class Test_Time implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() { return Dev_Unit::load_with_prefix('Test.Time.', 'DateTime'); }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="Test.Time.DateTime" extends="Dev.Unit.TestCase">
class Test_Time_DateTime extends Dev_Unit_TestCase {

  const CLS    = 'Time.DateTime';

  const AS_TS  = 1264065762;
  const AS_STR = '2010-01-21 12:22:42';

///   <protocol name="testing">

///   <method name="test_creating">
///     <body>
  public function test_creating() {
    $this->
      assert_class(self::CLS, $t = Time::DateTime(self::AS_TS))->
      assert($t->timestamp === self::AS_TS)->
      assert_class(self::CLS, Time::now())->
      assert_class(self::CLS, $n = Time::DateTime($t = Time::now()))->
      assert($n->timestamp === $t->timestamp)->
      assert_class(self::CLS, $t = Time::DateTime(self::AS_TS))->
      assert($t->as_string() === self::AS_STR)->
      assert(Time::DateTime(self::AS_STR)->timestamp === self::AS_TS)->
      assert(Time::parse(self::AS_STR)->timestamp === self::AS_TS)->
      assert(Time::parse('21.01.2010 12:22:42')->timestamp === self::AS_TS)->
      assert(Time::parse('2010=21=01 42:22:12', '%Y=%d=%m %S:%M:%H')->timestamp === self::AS_TS)->
      assert_class(self::CLS, $t = Time::compose(2010,1,21,12,22,42))->
      assert($t->timestamp === self::AS_TS)->
      assert(Time::compose(2010)->format()                === '2010-01-01 00:00:00')->
      assert(Time::compose(2010, 3)->format()             === '2010-03-01 00:00:00')->
      assert(Time::compose(2010, 3, 10)->format()         === '2010-03-10 00:00:00')->
      assert(Time::compose(2010, 3, 10, 15)->format()     === '2010-03-10 15:00:00')->
      assert(Time::compose(2010, 3, 10, 15, 20)->format() === '2010-03-10 15:20:00');
  }
///     </body>
///   </method>

///   <method name="test_changing">
///     <body>
  public function test_changing() {
    $t = Time::DateTiMe(self::AS_TS);

    $this->
      assert($t->add(1)->second              === 43)->
      assert($t->add(0, 1)->minute           === 23)->
      assert($t->add(0, 0, 1)->hour          === 13)->
      assert($t->add(0, 0, 0, 1)->day        === 22)->
      assert($t->add(0, 0, 0, 0, 1)->month   === 2)->
      assert($t->add(0, 0, 0, 0, 0, 1)->year === 2011);

    $this->
      assert($t->add_seconds(2)->second === 45)->
      assert($t->add_minutes(2)->minute === 25)->
      assert($t->add_hours(2)->hour     === 15)->
      assert($t->add_days(2)->day       === 24)->
      assert($t->add_months(2)->month   === 4)->
      assert($t->add_years(2)->year     === 2013);

    $this->
      assert($t->sub_seconds(2)->second === 43)->
      assert($t->sub_minutes(2)->minute === 23)->
      assert($t->sub_hours(2)->hour     === 13)->
      assert($t->sub_days(2)->day       === 22)->
      assert($t->sub_months(2)->month   === 2)->
      assert($t->sub_years(2)->year     === 2011);


    $this->
      assert($t->sub(1)->second              === 42)->
      assert($t->sub(0, 1)->minute           === 22)->
      assert($t->sub(0, 0, 1)->hour          === 12)->
      assert($t->sub(0, 0, 0, 1)->day        === 21)->
      assert($t->sub(0, 0, 0, 0, 1)->month   === 1)->
      assert($t->sub(0, 0, 0, 0, 0, 1)->year === 2010);
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only(
        $t = Time::DateTime(self::AS_TS),
        array(
          'year' => 2010, 'month'  => 1,   'day'    => 21,
          'hour' => 12,   'minute' => 22,  'second' => 42,
          'wday' => 4,    'yday'   => 21))->
      assert_read(
        $t, array('timestamp' => self::AS_TS))->
      assert_undestroyable(
        $t, array('year', 'month', 'day', 'hour', 'minute', 'second', 'wday', 'yday'))->
      assert_write($t, array('timestamp' => self::AS_TS + 5*60*60))->
      assert_missing($t);

    $this->assert($t->hour === 17);
  }
///     </body>
///   </method>

///   <method name="test_comparing">
///     <body>
  public function test_comparing() {
    $t = Time::DateTime(self::AS_TS);
    $this->
      assert(Core::equals($t, Time::DateTime(self::AS_STR)))->
      assert_true($t->later_than(Time::DateTime('2009-02-01')))->
      assert_false($t->later_than(Time::DateTime('2011-02-01')))->
      assert_false($t->not_earlier_than(Time::DateTime('2011-02-01')))->
      assert_true($t->not_earlier_than(Time::DateTime(self::AS_TS)))->
      assert_true($t->earlier_than(Time::DateTime('2011-02-01')))->
      assert_true($t->not_later_than(Time::DateTime('2011-02-01')))->
      assert_true($t->not_later_than(Time::DateTime(self::AS_TS)))->
      assert_false($t->earlier_than(Time::DateTime('2009-02-01')))->
      assert_true($t->same_date_as(Time::DateTime('2010-01-21 15:38')));
  }
///     </body>
///   </method>


///   <method name="test_converting">
///     <body>
  public function test_converting() {
    $t = Time::DateTime(self::AS_TS);

    $this->
      assert($t->format() === self::AS_STR)->
      assert($t->format(Time::FMT_DMYHMS) === '21.01.2010 12:22:42')->
      assert($t->as_string() === self::AS_STR)->
      assert_match('{Thu, 21 Jan 2010 12:22:42}', $t->as_rfc1123());
  }
///     </body>
///   </method>

///   <method name="test_misc">
///     <body>
  public function test_misc() {
    $this->
      assert(
        Time::seconds_between(Time::DateTime(self::AS_TS), Time::DateTime(self::AS_TS + 100))== 100)->
      assert(
        Time::seconds_between(Time::DateTime(self::AS_TS + 100), Time::DateTime(self::AS_TS))== 100);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>