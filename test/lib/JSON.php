<?php
/// <module name="Test.JSON" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'JSON');

/// <class name="Test.JSON" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_JSON implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.JSON.', 'ConverterCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.JSON.ConverterCase" extends="Dev.Unit.TestCase">
class Test_JSON_ConverterCase extends Dev_Unit_TestCase {
  protected $converter;
  protected $empty_object;
  protected $init_object;
  protected $small_object;
  protected $json_test;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {

    $this->converter = JSON::Converter()->using(new Test_JSON_UsingConverter());
    $this->empty_object = new Test_JSON_Object();
    $this->init_object = Core::with(new Test_JSON_Object())->init();
    $this->small_object = Core::with(new Test_JSON_Object())->small();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->assert_equal($this->init_object,
                        $this->converter->to($this->empty_object,
                                             $this->converter->from($this->init_object) //json
                                             )
                        );
  }
///     </body>
///   </method>

///   <method name="test_flavor">
///     <body>
  public function test_flavor() {
    $this->assert_equal($this->small_object,
                        $this->converter->to($this->empty_object,
                                             $this->converter->from($this->small_object, array('small' => true)), //json
                                             array('small' => true)
                                             )
                        );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Json.MiniObject" extends="Data.Object">
///   <implements interface="Object.AttrListInterface" />
class Test_JSON_MiniObject extends Object_Struct implements Object_AttrListInterface {
  protected $value;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="value" default="null" />
///     </args>
///     <body>
  public function __construct($value = null) {
    $this->value = $value;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="typing">

///   <method name="__attrs" returns="Object.AttrList">
///     <args>
///       <arg name="flavor" type="null" />
///     </args>
///     <body>
  public function __attrs($flavor = null) {
    return Core::with(new Object_AttrList())->
      value('value', 'int');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Json.ParentObject" extends="Data.Object">
///   <implements interface="Object.AttrListInterface" />
class Test_JSON_ParentObject extends Object_Struct implements Object_AttrListInterface {
  protected $parent_value;
///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="value" default="null" />
///     </args>
///     <body>
  public function __construct($value = null) {
    $this->parent_value = $value;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="typing">

///   <method name="__attrs" returns="Object.AttrList">
///     <args>
///       <arg name="flavor" type="null" />
///     </args>
///     <body>
  public function __attrs($flavor = null) {
    return Core::with(new Object_AttrList())->
      object('parent_value', 'Test.JSON.MiniObject');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.JSON.Object" extends="Test.JSON.ParentObject">
///   <implements interface="Object.AttrListInterface" />
class Test_JSON_Object extends Test_JSON_ParentObject implements Object_AttrListInterface {

  protected $int = 1;
  protected $string = "test string";
  protected $date1;
  protected $date2;
  protected $array_of_dates = array();
  protected $array_of_mixed_values = array();
  protected $collection;
  protected $struct;

///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct() {
    $args = func_get_args();
    parent::__construct($args[0]);
    $this->collection = Core::hash();
  }
///     </body>
///   </method>



///   <method name="init">
///     <body>
  public function init() {
    $this->parent_value = new Test_JSON_MiniObject(1000);

    $this->date1 = Time::parse("2009-10-11 10:59:01");
    $this->date2 = Time::parse("2009-10-11 10:59:02");
    $this->array_of_dates = array(Time::parse("2009-10-11 10:59:01"),Time::parse("2009-10-21 00:33:11"));
    $this->array_of_mixed_values = array(1, "test", false, true);
    $this->collection = Core::hash(array(new Test_JSON_MiniObject(99), new Test_JSON_MiniObject(100)));
    $this->struct = new Test_JSON_Struct(101, "test string in struct");
    return $this;
  }
///     </body>
///   </method>

///   <method name="small">
///     <body>
  public function small() {
     $this->parent_value = new Test_JSON_MiniObject(1000);
     $this->date1 = Time::parse("2009-10-11 10:59:01");
     $this->date2 = Time::parse("2009-10-11 10:59:02");
     return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="typing">

///   <method name="__attrs" returns="Object.AttrList">
///     <args>
///       <arg name="flavor" type="null" />
///     </args>
///     <body>
  public function __attrs($flavor = array()) {
    $res =  Core::with(new Object_AttrList())->
      extend(parent::__attrs($flavor))->
      value(array('date1', 'date2'), 'datetime');
    if (is_array($flavor) && $flavor['small']) return $res;
      $res->
      value('int', 'int')->
      value('string', array('type' => 'string'))->
      collection('array_of_dates', 'datetime', array('operation' => array($this, "add_date")))->
      collection('array_of_mixed_values', null,array('operation' => array($this, "add_trash")))->
      collection('collection', 'Test.JSON.MiniObject')->
      value('struct', 'Test.JSON.Struct')
    ;
    return $res;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">


///   <method name="add_date">
///     <args>
///       <arg name="date" type="Time.DateTime" />
///     </args>
///     <body>
  public function add_date(Time_DateTime $date) {
    $this->array_of_dates[] = $date;
  }
///     </body>
///   </method>

///   <method name="add_trash">
///     <args>
///       <arg name="t" />
///     </args>
///     <body>
  public function add_trash($t) {
    $this->array_of_mixed_values[] = $t;
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.JSON.Struct" extends="Data.AbstractStruct">
class Test_JSON_Struct extends Object_Struct {
  protected $string;
  protected $int;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="int" type="int" />
///       <arg name="string" type="string" />
///     </args>
///     <body>
  public function __construct($int, $string) {
    $this->int = $int;
    $this->string = $string;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="match" interface="Core.EqualityInterface">

///   <method name="equals" returns="boolean">
///     <args>
///       <arg name="with" />
///     </args>
///     <body>
 public function equals($with) {
    if (!($with instanceof Test_JSON_Struct))
      return false;
    if (!($this->int == $with->int && $this->string == $with->string)) return false;

    return true;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.JSON.UsingConverter">
class Test_JSON_UsingConverter extends JSON_AttributeConverter {
///   <protocol name="converting">

///   <method name="encode_test_json_struct">
///     <args>
///       <arg name="opt" type="" />
///     </args>
///     <body>
  public function encode_test_json_struct(Test_JSON_Struct $struct, $attr = null) {
     return (object) array($struct->int => $struct->string);
  }
///     </body>
///   </method>

///   <method name="encode_test_json_struct">
///     <args>
///       <arg name="opt" type="" />
///     </args>
///     <body>
  public function decode_test_json_struct($obj, $attr = null) {
    return  new Test_JSON_Struct(key($obj), current($obj));
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// </module>
?>