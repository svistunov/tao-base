<?php
/// <module name="Test.Core" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit');

/// <class name="Test.Core" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Core implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Core.',
      'ModuleLoaderCase', 'RegexpCase', 'StringsCase', 'TypesCase', 'ArraysCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Core.ModuleLoaderCase" extends="Dev.Unit.TestCase">
class Test_Core_ModuleLoaderCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_true(Core::is_loaded('Dev.Unit'))->
      assert_false(Core::is_loaded('NOMODULE'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Core.RegexpCase" extends="Dev.Unit.TestCase">
class Test_Core_RegexpCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_math() {
    $this->
      assert_true(Core_Regexps::match('{php}i', "PHP is the scripting language of choice."))->
      assert_false(Core_Regexps::match('{php}i', "Perl is the scripting language of choice."))->
      assert_true(Core_Regexps::match("{\bweb\b}i", "PHP is the web scripting language of choice."));
  }
///     </body>
///   </method>

///   <method name="test_match_with_results">
///     <body>
  public function test_match_with_results() {
    $this->
      assert_equal(
        Core_Regexps::match_with_results('@^(?:http://)?([^/]+)@i', "http://www.php.net/index.html"),
         array (0 => 'http://www.php.net', 1 => 'www.php.net')
      );
  }
///     </body>
///   </method>

///   <method name="test_match_all">
///     <body>
  public function test_match_all() {
    $this->
      assert_equal(
        Core_Regexps::match_all("/\(?  (\d{3})?  \)?  (?(1)  [\-\s] ) \d{3}-\d{4}/x", "Call 555-1212 or 1-800-555-1212"),
         array (
          0 =>
          array (
            0 => '555-1212',
            1 => '800-555-1212',
          ),
          1 =>
          array (
            0 => '',
            1 => '800',
          ),
        )
    );
  }
///     </body>
///   </method>

///   <method name="test_quate">
///     <body>
  public function test_quate() {
    $this->
      assert_equal(
        Core_Regexps::quote('$40 for a g3/400'),
        '\\$40 for a g3/400'
      );
  }
///     </body>
///   </method>

///   <method name="test_replace">
///     <body>
  public function test_replace() {
    $this->
      assert_equal(
        Core_Regexps::replace('/(\w+) (\d+), (\d+)/i', '${1}1,$3', 'April 15, 2003'),
        "April1,2003"
      );
  }
///     </body>
///   </method>

///   <method name="test_replace_using_callback">
///     <body>
  public function test_replace_using_callback() {
    $this->
    assert_equal(
      Core_Regexps::replace_using_callback("|(\d{2}/\d{2}/)(\d{4})|", array($this,'callback'), "April fools day is 04/01/2002"),
       "April fools day is 04/01/2003"
    );
  }
///     </body>
///   </method>

///   <method name="test_replace_ref">
///     <body>
  public function test_replace_ref() {
    $string  = 'Test string to search';
    $this->
      assert_equal(Core_Regexps::replace_ref('/string/','replace', $string), 1)->
      assert_equal($string, 'Test replace to search');
  }
///     </body>
///   </method>

///   <method name="test_split_by">
///     <body>
  public function test_split_by() {
    $this->
      assert_equal(
        Core_Regexps::split_by('/ /', 'Test string to search'),
        array('Test', 'string', 'to', 'search')
      );
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">
///   <method name="callback" return="string">
///     <args>
///       <arg name="matches" type="array" />
///     </args>
///     <body>
  public function callback($matches) { return $matches[1].($matches[2]+1); }
///     </body>
///   </method>
///   </protocol>
}
/// </class>

/// <class name="Test.Core.StringsCase" extends="Dev.Unit.TestCase">
class Test_Core_StringsCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_conact">
///     <body>
  public function test_concat() {
    $this->
      assert_equal(Core_Strings::concat('1', '2', '3'), '123')->
      assert_equal(Core_Strings::concat(array('1', '2', '3')), '123');
  }
///     </body>
///   </method>

///   <method name="test_concat_with">
///     <body>
  public function test_concat_with() {
    $this->
      assert_equal(Core_Strings::concat_with('/', '1', '2', '3'), '1/2/3')->
      assert_equal(Core_Strings::concat_with(array('/', '1', '2', '3')), '1/2/3');
  }
///     </body>
///   </method>

///   <method name="test_substr">
///     <body>
  public function test_substr() {
    $this->
      assert_equal(Core_Strings::substr('string', 2), 'ring')->
      assert_equal(Core_Strings::substr('string', 2, 2), 'ri');
  }
///     </body>
///   </method>

///   <method name="test_replace">
///     <body>
  public function test_replace() {
    $this->
      assert_equal(Core_Strings::replace('string', 's', 'S'), 'String');
  }
///     </body>
///   </method>

///   <method name="test_chop">
///     <body>
  public function test_chop() {
    $this->
      assert_equal(Core_Strings::chop('chop   '), 'chop');
  }
///     </body>
///   </method>

///   <method name="test_trim">
///     <body>
  public function test_trim() {
    $this->
      assert_equal(Core_Strings::trim('  trim  '), 'trim')->
      assert_equal(Core_Strings::trim('a  trim  a ', 'a '), 'trim');
  }
///     </body>
///   </method>

///   <method name="test_split">
///     <body>
  public function test_split() {
    $this->
      assert_equal(
        Core_Strings::split("piece1 piece2 piece3 piece4"),
        array("piece1", "piece2", "piece3", "piece4")
      );
  }
///     </body>
///   </method>

///   <method name="test_split_by">
///     <body>
  public function test_split_by() {
    $this->
      assert_equal(
        Core_Strings::split_by(',', "piece1,piece2,piece3,piece4"),
        array("piece1", "piece2", "piece3", "piece4")
      )->
      assert_equal(
        Core_Strings::split_by(',', ""),
        array()
      );
  }
///     </body>
///   </method>

///   <method name="test_format">
///     <body>
  public function test_format() {
    $this->
      assert_equal(
        Core_Strings::format("%04d-%02d-%02d", '1988', '08', '01'),
        "1988-08-01"
      );
  }
///     </body>
///   </method>

///   <method name="test_starts_with">
///     <body>
  public function test_starts_with() {
    $this->
      assert_true(Core_Strings::starts_with('Start', 'St'))->
      assert_false(Core_Strings::starts_with('Start', 'rt'));
  }
///     </body>
///   </method>

///   <method name="test_ends_with">
///     <body>
  public function test_ends_with() {
    $this->
      assert_false(Core_Strings::ends_with('Start', 'St'))->
      assert_true(Core_Strings::ends_with('Start', 'rt'));
  }
///     </body>
///   </method>

///   <method name="test_contains">
///     <body>
  public function test_contains() {
    $this->
      assert_false(Core_Strings::contains('Start', 'www'))->
      assert_true(Core_Strings::contains('Start', 'ar'));
  }
///     </body>
///   </method>

///   <method name="test_downcase">
///     <body>
  public function test_downcase() {
    $this->
      assert_equal(Core_Strings::downcase('StRiNG'), 'string');
  }
///     </body>
///   </method>

///   <method name="test_upcase">
///     <body>
  public function test_upcase() {
    $this->
      assert_equal(Core_Strings::upcase('StRiNG'), 'STRING');
  }
///     </body>
///   </method>

///   <method name="test_capitalize">
///     <body>
  public function test_capitalize() {
    $this->
      assert_equal(Core_Strings::capitalize('string'), 'String');
  }
///     </body>
///   </method>

///   <method name="test_lcfirst">
///     <body>
  public function test_lcfirst() {
    $this->
      assert_equal(Core_Strings::lcfirst('String'), 'string');
  }
///     </body>
///   </method>

///   <method name="test_capitalize_words">
///     <body>
  public function test_capitalize_words() {
    $this->
      assert_equal(
        Core_Strings::capitalize_words('string string s'),
        'String String S'
      );
  }
///     </body>
///   </method>

///   <method name="test_to_camel_case">
///     <body>
  public function test_to_camel_case() {
    $this->
      assert_equal(
        Core_Strings::to_camel_case('to_camel_case'),
        'ToCamelCase'
      );
  }
///     </body>
///   </method>

///   <method name="test_encode64_decode64">
///     <body>
  public function test_encode64_decode64() {
    $this->
      assert_equal(
        Core_Strings::decode64(Core_Strings::encode64("Coding me")),
        "Coding me"
      );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Core.TypesCase" extends="Dev.Unit.TestCase">
class Test_Core_TypesCase extends Dev_Unit_TestCase {
///   <protocol name="testing">

///   <method name="test_is_array">
///     <body>
  public function test_is_array() {
    $this->
      assert_true(Core_types::is_array($arr = array(1,2,3)))->
      assert_false(Core_types::is_array($obj = (object) array(1,2,3,4)));
  }
///     </body>
///   </method>

///   <method name="test_is_string">
///     <body>
  public function test_is_string() {
    $this->
      assert_true(Core_types::is_string($str ='1,2,3'))->
      assert_true(Core_types::is_string($emp = ''))->
      assert_true(Core_types::is_string($self = self))->
      assert_false(Core_types::is_string($arr = array(1,2,3,4)));
  }
///     </body>
///   </method>

///   <method name="test_is_number">
///     <body>
  public function test_is_number() {
    $this->
      assert_true(Core_types::is_number($zero = 0))->
      assert_true(Core_types::is_number($zero_str = '0'))->
      assert_false(Core_types::is_number($str = '0a'));
  }
///     </body>
///   </method>

///   <method name="test_is_object">
///     <body>
  public function test_is_object() {
    $this->
      assert_true(Core_types::is_object($obj = new ArrayObject(array(1,2,3))))->
      assert_false(Core_types::is_object($arr = array(1,2,3,4)));
  }
///     </body>
///   </method>

///   <method name="test_is_resource">
///     <body>
  public function test_is_resource() {
    $this->
      assert_true(Core_types::is_resource($f = fopen("test/data/Config/DSL/test.php", 'r')))->
      assert_false(Core_types::is_resource($str = "1.0"));
  }
///     </body>
///   </method>

///   <method name="test_is_iterable">
///     <body>
  public function test_is_iterable() {
    $this->
      assert_true(Core_types::is_iterable($arr = array(1,2,3)))->
      assert_true(Core_types::is_iterable($obj = new ArrayObject(array(1,2,3))))->
      assert_false(Core_types::is_iterable($std = (object) array(1,2,3)));
  }
///     </body>
///   </method>

///   <method name="test_is_subclass_of">
///     <body>
  public function test_is_subclass_of() {
    $this->
      assert_true(Core_types::is_subclass_of('stdClass', (object) array(1,2)))->
      assert_true(Core_types::is_subclass_of('Dev.Unit.TestCase', $this))->
      assert_true(Core_types::is_subclass_of('Dev_Unit_TestCase', $this))->
      assert_true(Core_types::is_subclass_of('Dev_Unit_TestCase', 'Test.Core.TypesCase'))->
      assert_true(Core_types::is_subclass_of('IteratorAggregate', 'ArrayObject'))->
      assert_false(Core_types::is_subclass_of('IteratorAggregate', array(1,2)));
  }
///     </body>
///   </method>

///   <method name="test_class_name_for">
///     <body>
  public function test_class_name_for() {
    $this->
      assert_equal(Core_Types::class_name_for($this), 'Test_Core_TypesCase')->
      assert_equal(Core_Types::class_name_for($this, true), 'Test.Core.TypesCase');
  }
///     </body>
///   </method>

///   <method name="test_virtual_class_name_for">
///     <body>
  public function test_virtual_class_name_for() {
    $this->
      assert_equal(Core_Types::virtual_class_name_for($this), 'Test.Core.TypesCase');
  }
///     </body>
///   </method>

///   <method name="test_real_class_name_for">
///     <body>
  public function test_real_class_name_for() {
    $this->
      assert_equal(Core_Types::real_class_name_for($this), 'Test_Core_TypesCase');
  }
///     </body>
///   </method>

///   <method name="test_module_name_for">
///     <body>
  public function test_module_name_for() {
    $this->
      assert_equal(Core_Types::module_name_for($this), 'Test.Core');
  }
///     </body>
///   </method>

///   <method name="test_reflection_for">
///     <body>
  public function test_reflection_for() {
    $this->
      assert_class('ReflectionClass', Core_Types::reflection_for($this))->
      assert_class('ReflectionClass', Core_Types::reflection_for('Test.Core'));
  }
///     </body>
///   </method>

///   <method name="test_class_hierarchy_for">
///     <body>
  public function test_class_hierarchy_for() {
    $this->
      assert_equal(
        Core_Types::class_hierarchy_for($this, true),
        array (
          0 => 'Test.Core.TypesCase',
          1 => 'Dev.Unit.TestCase',
          2 => 'Dev.Unit.AssertBundle'
        )
      )->
      assert_equal(
        Core_Types::class_hierarchy_for($this),
        array (
          0 => 'Test_Core_TypesCase',
          1 => 'Dev_Unit_TestCase',
          2 => 'Dev_Unit_AssertBundle'
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_class_exists">
///     <body>
  public function test_class_exists() {
    $this->
      assert_true(Core_Types::class_exists('Test_Core'))->
      assert_true(Core_Types::class_exists('Test.Core'))->
      assert_false(Core_Types::class_exists('Test.NOMODULE'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Core.ArraysCase" extends="Dev.Unit.TestCase">
class Test_Core_ArraysCase extends Dev_Unit_TestCase {
///   <protocol name="testing">

///   <method name="test_keys">
///     <body>
  public function test_keys() {
    $arr = array('key1' => 'value1', 'key2' => 'value2');
    $this->
      assert_equal(
        Core_Arrays::keys($arr),
        array('key1', 'key2')
        );
  }
///     </body>
///   </method>

///   <method name="test_shift">
///     <body>
  public function test_shift() {
    $arr = array('key1' => 'value1', 'key2' => 'value2');
    $this->
      assert_equal(Core_Arrays::shift($arr), 'value1')->
      assert_equal($arr, array('key2' => 'value2'));
  }
///     </body>
///   </method>

///   <method name="test_pop">
///     <body>
  public function test_pop() {
    $arr = array('key1' => 'value1', 'key2' => 'value2');
    $this->
      assert_equal(Core_Arrays::pop($arr), 'value2')->
      assert_equal($arr, array('key1' => 'value1'));
  }
///     </body>
///   </method>

///   <method name="test_pick">
///     <body>
  public function test_pick() {
    $arr = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3');
    $this->
      assert_equal(Core_Arrays::pick($arr, 'key2'), 'value2')->
      assert_equal($arr, array('key1' => 'value1', 'key3' => 'value3'));
  }
///     </body>
///   </method>

///   <method name="test_reverse">
///     <body>
  public function test_reverse() {
    $arr = array('value1', 'value2');
    $this->
      assert_equal(Core_Arrays::reverse($arr), array('value2', 'value1'))->
      assert_equal(Core_Arrays::reverse($arr, true), array(1 => 'value2', 0 => 'value1'));
  }
///     </body>
///   </method>

///   <method name="test_flatten">
///     <body>
  public function test_flatten() {
    $arr = array(
      'value1',
      'value2',
      'arr' => array(1,2,3, 'a' => array(1, 2, 'value'))
    );
    $this->
      assert_equal(
        Core_Arrays::flatten($arr),
         array (
          0 => 'value1',
          1 => 'value2',
          2 => 1,
          3 => 2,
          4 => 3,
          'a' =>
          array (
            0 => 1,
            1 => 2,
            2 => 'value',
          ),
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_map">
///     <body>
  public function test_map() {
    $this->
      assert_equal(
        Core_Arrays::map('return "$x"."1";', $arr = array(1,2,3)),
        array('11', '21', '31')
      );
  }
///     </body>
///   </method>

///   <method name="test_merge">
///     <body>
  public function test_merge() {
    $this->
      assert_equal(
        Core_Arrays::merge(
          array("color" => "red", 2, 4),
          array("a", "b", "color" => "green", "shape" => "trapezoid", 4)
          ),
        array("color" => "green", 0 => 2, 1 => 4, 2 => "a", "b", "shape" => "trapezoid", 4 => 4 )
      );
  }
///     </body>
///   </method>

///   <method name="test_deep_merge_update">
///     <body>
  public function test_deep_merge_update() {
    $arr1 = array(
      "color" => array("favorite" => "red"),
      5
    );
    $arr2 = array(
      10,
      "color" => array("favorite" => "green", "blue"));
    $this->
      assert_equal(
        Core_Arrays::deep_merge_update($arr1, $arr2),
        array (
          'color' =>
          array (
            'favorite' => 'green',
            0 => 'blue',
          ),
          0 => 10,
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_deep_merge_append">
///     <body>
  public function test_deep_merge_append() {
    $arr1 = array(
      "color" => array("favorite" => "red"),
      5
    );
    $arr2 = array(
      10,
      "color" => array("favorite" => "green", "blue"));
    $this->
      assert_equal(
        Core_Arrays::deep_merge_append($arr1, $arr2),
        array (
          'color' =>
          array (
            'favorite' => array(
               'red',
               'green'
              ),
            0 => 'blue',
          ),
          0 => array(
            5,
            10
          )
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_deep_merge_update_inplace">
///     <body>
  public function test_deep_merge_update_inplace() {
    $arr1 = array(
      "color" => array("favorite" => "red"),
      5
    );
    $arr2 = array(
      10,
      "color" => array("favorite" => "green", "blue"));
    Core_Arrays::deep_merge_update_inplace($arr1, $arr2);
    $this->
      assert_equal(
        $arr1,
        array (
          'color' =>
          array (
            'favorite' => 'green',
            0 => 'blue',
          ),
          0 => 10,
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_update">
///     <body>
  public function test_update() {
    $arr1 = array(1, 2, 3, 'color' => 'red', 'shape'=> 'circle');
    $arr2 = array(4, 5, 'color' => 'blue', 'height' => '100');
    Core_Arrays::update($arr1, $arr2);
    $this->
      assert_equal(
        $arr1,
        array(4, 5, 3, 'color' => 'blue', 'shape' => 'circle')
      );
  }
///     </body>
///   </method>

///   <method name="test_expand">
///     <body>
  public function test_expand() {
    $arr1 = array(1, 2, 3, 'color' => 'red', 'shape'=> 'circle');
    $arr2 = array(4, 5, 'color' => 'blue', 'height' => '100');
    Core_Arrays::expand($arr1, $arr2);
    $this->
      assert_equal(
        $arr1,
        array(1, 2, 3, 'color' => 'red', 'shape' => 'circle', 'height' => '100')
      );
  }
///     </body>
///   </method>

///   <method name="test_join_with">
///     <body>
  public function test_join_with() {
    $arr1 = array(1, 2, 3, 'color' => 'red', 'shape'=> 'circle');
    $this->
      assert_equal(
        Core_Arrays::join_with(':', $arr1),
        '1:2:3:red:circle'
      );
  }
///     </body>
///   </method>

///   <method name="test_search">
///     <body>
  public function test_search() {
    $array = array(0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red');
    $array0 = array(1, 2, 3);
    $this->
      assert_equal(Core_Arrays::search('red', $array), 1)->
      assert_equal(Core_Arrays::search('green', $array), 2)->
      assert_equal(Core_Arrays::search(2, $array0), 1)->
      assert_equal(Core_Arrays::search('2', $array0, true), false);
  }
///     </body>
///   </method>

///   <method name="test_contains">
///     <body>
  public function test_contains() {
    $array = array(0 => 'blue', 1 => 'red', 2 => 'green', 3 => 'red');
    $this->
      assert_true(Core_Arrays::contains($array, 'red'))->
      assert_false(Core_Arrays::contains($array, 'black'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>
/// </module>
?>