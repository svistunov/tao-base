<?php
/// <module name="Test.Cache" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Cache', 'Time');

/// <class name="Test.Cache" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Cache implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Cache.', 'FSCase', 'MemcacheCase', 'APCCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Cache.Case" extends="Dev.Unit.TestCase" scope="abstract">
abstract class Test_Cache_Case extends Dev_Unit_TestCase {
  protected $cache;

///   <method name="getCache" returns="Cache.Backend" scope="abstract" access="protected">
///     <body>
  abstract protected function getCache();
///     </body>
///   </method>

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->cache = $this->getCache();
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $value = new ArrayObject(array(1,2,3,4,5));
    $this->asserts->indexing->
      assert_write($this->cache, array('key' => 'value1', 'key1' => $value))->
      assert_read($this->cache, array('key' => 'value1', 'key1' => $value))->
      assert_nullable($this->cache, array('key', 'key1'));
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->assert_true($this->cache->set('key', 'value1'));
    $this->
      assert_equal($this->cache->get('key'), 'value1')->
      assert_true($this->cache->has('key'));

    $this->cache->delete('key');
    $this->
      assert_false($this->cache->has('key'))->
      assert_null($this->cache->get('key'));

  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Cache.FSCase" extends="Test.Cache.Case">
class Test_Cache_FSCase extends Test_Cache_Case {
///   <protocol name="creating">

///   <method name="getCache" returns="Cache.Backend" scope="abstract" access="protected">
///     <body>
  protected function getCache() {
    return Cache::connect('fs://./test/data/Cache/');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_building">
///     <body>
  public function test_building() {
    $this->assert_class('Cache.FS.Backend', $this->cache);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.Cache.MemcacheCase" extends="Test.Cache.Case">
class Test_Cache_MemcacheCase extends Test_Cache_Case {
///   <protocol name="creating">

///   <method name="getCache" returns="Cache.Backend" scope="abstract" access="protected">
///     <body>
  protected function getCache() {
    return Cache::connect('memcache://localhost:11211');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_building">
///     <body>
  public function test_building() {
    $this->assert_class('Cache.Memcache.Backend', $this->cache);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.Cache.APCCase" extends="Test.Cache.Case">
class Test_Cache_APCCase extends Test_Cache_Case {
///   <protocol name="creating">

///   <method name="getCache" returns="Cache.Backend" scope="abstract" access="protected">
///     <body>
  protected function getCache() {
    return Cache::connect('apc://');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_building">
///     <body>
  public function test_building() {
    $this->assert_class('Cache.Apc.Backend', $this->cache);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// </module>
?>