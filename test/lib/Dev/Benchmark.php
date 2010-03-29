<?php
/// <module name="Test.Dev.Benchmark" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit','Dev.Benchmark');

/// <class name="Test.Dev.Benchmark" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Dev_Benchmark implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Dev.Benchmark.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Benchmark" extends="Dev.Unit.TestCase">
class Test_Dev_Benchmark_Case extends Dev_Unit_TestCase {
   protected $timer;
   protected $counter;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
   protected function setup() {
     $this->timer = Dev_Benchmark::Timer();
     $this->counter = 0;
   }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->timer->start();
    sleep(0.2);
    $this->timer->lap('lap1');
    sleep(0.3);
    $this->timer->lap('lap2');
    sleep(0.1);
    $this->timer->stop();

    $this->assert_true($this->timer instanceof Core_StringifyInterface);
    $this->asserts->accessing->
      assert_exists_only($this->timer, array('started_at', 'stopped_at', 'total_time', 'events'))->
      assert_undestroyable($this->timer, array('started_at', 'stopped_at', 'total_time', 'events'));

    $this->
      assert_true($this->timer->started_at < $this->timer->stopped_at)->
      assert_true($this->timer->total_time > 0)->
      assert_equal($this->timer->events[0]->note, 'lap1')->
      assert_equal($this->timer->events[1]->note, 'lap2')->
      assert_equal($this->timer->events[2]->note, '_stop_');
   }
///     </body>
///   </method>

///   <method name="test_event">
///     <body>
  public function test_event() {
    $this->timer->start();
    $this->timer->stop();
    $this->asserts->accessing->
      assert_exists($this->timer->events[0], array('note', 'lap', 'cumulative', 'percentage', 'time'));
  }
///     </body>
///   </method>

///   <method name="test_performing">
///     <body>
   public function test_performing() {
     $this->timer->repeat(1000, array($this, 'call_me'));
     $this->assert_equal($this->counter, 1000);
   }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="call_me" >
///     <body>
   public function call_me() {
     $this->counter++;
   }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>