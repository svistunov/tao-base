<?php
/// <module name="Test.Digest" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Digest');

/// <class name="Test.Digest" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Digest implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Digest.', 'DigestCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Digest.DigestCase" extends="Dev.Unit.TestCase">
class Test_Digest_DigestCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->
      assert_equal(Digest::crypt('test', 'rl'), 'rl1IV0t8l4rcQ')->
      assert_equal(Digest_MD5::hexdigest('apple'), '1f3870be274f6c49b3e31a0c6728957f')->
      assert_equal(Digest_MD5::digest('apple'), md5('apple', true))->
      assert_equal(Digest_SHA1::hexdigest('apple'), 'd0be2dc421be4fcd0172e5afceea3970e2f3d940')->
      assert_equal(Digest_SHA1::digest('apple'), sha1('apple', true));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>