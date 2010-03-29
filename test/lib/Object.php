<?php
/// <module name="Test.Object">
Core::load('Dev.Unit', 'Object', 'Time');

/// <class name="Test.Object" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Object implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix(
      'Test.Object.',  'Struct', 'Listener', 'Const', 'Attribute', 'AttrList', 'Factory', 'Aggregator');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Attribute" extends="Dev.Unit.TestCase">
class Test_Object_Attribute extends Dev_Unit_TestCase {

  protected $oa;
  protected $ca;
  protected $va;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->oa = new Object_ObjectAttribute('object', array('type' => 'stdClass'));
    $this->va = new Object_ValueAttribute('value', array('type' => 'string'));
    $this->ca = new Object_CollectionAttribute('collection');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_types">
///     <body>
  public function test_types() {
    $this->
      assert_true($this->oa->is_object())->
      assert_false($this->oa->is_value())->
      assert_false($this->oa->is_collection())->
      assert_true($this->va->is_value())->
      assert_false($this->va->is_object())->
      assert_false($this->va->is_collection())->
      assert_true($this->ca->is_collection())->
      assert_false($this->ca->is_value())->
      assert_false($this->ca->is_object());
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->
      assert($this->oa->name === 'object')->
      assert($this->oa->type === 'stdClass')->
      assert($this->va->name === 'value')->
      assert($this->va->type === 'string')->
      assert($this->ca->name === 'collection');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.AttrList..Thing">
class Test_Object_AttrList__Thing implements Object_AttrListInterface {

  public $name = 'test item';
  public $produced;
  public $size;
  public  $variants;

///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct($length, $width, $height) {
    $this->produced = Time::now();
    $this->variants = Core::hash();
    $this->size = Core::object(compact('length', 'width', 'height'));
  }
///     </body>
///   </method>

///   <method name="variant" returns="Test.Object.AttrList..Entity">
///     <args>
///       <arg name="color" type="string" />
///       <arg name="quality" type="string" />
///     </args>
///     <body>
  public function variant($color, $quality) {
    $this->variants[] = Core::object(compact('color', 'quality'));
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">

///   <method name="__attrs" returns="Object.AttrList">
///     <args>
///       <arg name="flavor" default="null" />
///     </args>
///     <body>
  public function __attrs($flavor = null) {
    static $attrs;
    if (!$attrs) $attrs = Object::AttrList()->
      value('name', 'string')->
      value('produced', 'date')->
      object('size', 'stdClass')->
      collection('variants', 'stdClass');
    return $attrs;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Attribute" extends="Dev.Unit.TestCase">
class Test_Object_AttrList extends Dev_Unit_TestCase {

  protected $e;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->e = Core::with(new Test_Object_AttrList__Thing(3,2,1))->
      variant('white', 'low')->
      variant('black', 'high');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_attributes">
///     <body>
  public function test_attributes() {
    $this->
      assert_class('Object.AttrList', $this->e->__attrs())->
      assert_class('AppendIterator', $this->e->__attrs()->getIterator());

    $attrs = array();
    foreach ($this->e->__attrs() as $k => $v) $attrs[$k] = $v;

    $this->
      assert_class('Object.ValueAttribute', $attrs['name'])->
      assert($attrs['name']->type === 'string')->
      assert_class('Object.ObjectAttribute', $attrs['size'])->
      assert($attrs['size']->type, 'stdClass')->
      assert_class('Object.CollectionAttribute', $attrs['variants'])->
      assert($attrs['variants']->items === 'stdClass');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Const..NumStatus" extends="Object.Const">
class Test_Object_Const__NumStatus extends Object_Const {

///   <constants>
  const DRAFT     = 0;
  const PUBLISHED = 1;
  const ARCHIVED  = 2;
///   </constants>

  protected $mark;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="value" type="int" />
///     </args>
///     <body>
  public function __construct($value) {
    parent::__construct($value);
    $this->mark = Core::with_index(array('m', 'p', 'a'), $value);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="DRAFT" returns="Test.Object.Const.NumStatus" scope="class">
///     <body>
  static public function DRAFT() { return new self(self::DRAFT); }
///     </body>
///   </method>

///   <method name="PUBLISHED" returns="Test.Object.Const.NumStatus" scope="class">
///     <body>
  static public function PUBLISHED() { return new self(self::PUBLISHED); }
///     </body>
///   </method>

///   <method name="ARCHIVED" returns="Test.Object.Const.NumStatus" scope="class">
///     <body>
  static public function ARCHIVED() { return new self(self::ARCHIVED); }
///     </body>
///   </method>

///   <method name="object" returns="Test.Object.Const.NumStatus" scope="class">
///     <args>
///       <arg name="value" />
///     </args>
///     <body>
  static public function object($value) {
    return self::object_for('Test_Object_Const__NumStatus', $value, 3);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="get_brief" returns="string" access="protected">
///     <body>
  protected function get_brief() {
    return Core::with_index(
      array('draft created', 'story published', 'story archived'), $this->value);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="Test.Object.Const..StrStatus" extends="Object.Const">
class Test_Object_Const__StrStatus extends Object_Const {

///   <constants>
  const DRAFT     = 'DRAFT';
  const PUBLISHED = 'PUBLISHED';
  const ARCHIVED  = 'ARCHIVED';
///   </constants>

///   <protocol name="building">

///   <method name="DRAFT" returns="Test.Object.Const.StrStatus" scope="class">
///     <body>
  static public function DRAFT() { return new self(self::DRAFT); }
///     </body>
///   </method>

///   <method name="PUBLISHED" returns="Test.Object.Const.StrStatus" scope="class">
///     <body>
  static public function PUBLISHED() { return new self(self::DRAFT); }
///     </body>
///   </method>

///   <method name="ARCHIVED" returns="Test.Object.Const.StrStatus" scope="class">
///     <body>
  static public function ARCHIVED() { return new self(self::ARCHIVED); }
///     </body>
///   </method>

///   <method name="object" returns="Test.Object.Const.StrStatus" scope="class">
///     <body>
  static public function object($value) { return self::object_for('Test_Object_Const__StrStatus', $value, 3); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Const" extends="Dev.Unit.TestCase">
class Test_Object_Const extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_numeric">
///     <body>
  public function test_numeric() {
    $this->
      assert(
        Test_Object_Const__NumStatus::ARCHIVED === Test_Object_Const__NumStatus::ARCHIVED()->value)->
      assert_class(
        'Test.Object.Const..NumStatus', Test_Object_Const__NumStatus::ARCHIVED())->
      assert_class(
        'Test.Object.Const..NumStatus',
        Test_Object_Const__NumStatus::object(Test_Object_Const__NumStatus::ARCHIVED))->
      assert_equal(
        Test_Object_Const__NumStatus::ARCHIVED(),
        Test_Object_Const__NumStatus::object(Test_Object_Const__NumStatus::ARCHIVED))->
      assert(
        (string) Test_Object_Const__NumStatus::ARCHIVED() == (string) Test_Object_Const__NumStatus::ARCHIVED);

      $this->set_trap();
      try {
        Test_Object_Const__NumStatus::object(1000);
      } catch (Object_BadConstException $e) {
        $this->trap($e);
      }
      $this->assert_exception();
  }
///     </body>
///   </method>

///   <method name="test_string">
///     <body>
  public function test_string() {
    $this->
      assert(
        Test_Object_Const__StrStatus::ARCHIVED === Test_Object_Const__StrStatus::ARCHIVED()->value)->
      assert_class(
        'Test.Object.Const..StrStatus', Test_Object_Const__StrStatus::ARCHIVED())->
      assert_class(
        'Test.Object.Const..StrStatus',
        Test_Object_Const__StrStatus::object(Test_Object_Const__StrStatus::ARCHIVED))->
      assert_equal(
        Test_Object_Const__StrStatus::ARCHIVED(),
        Test_Object_Const__StrStatus::object(Test_Object_Const__StrStatus::ARCHIVED))->
      assert(
        (string) Test_Object_Const__StrStatus::ARCHIVED() === Test_Object_Const__StrStatus::ARCHIVED);

    $this->set_trap();
    try {
      Test_Object_Const__StrStatus::object('undefined_constant');
    } catch (Object_BadConstException $e) {
      $this->trap($e);
    }
    $this->assert_exception();
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->
      asserts->accessing->
        assert_read_only(
          $a = Test_Object_Const__NumStatus::ARCHIVED(),
          array('value' => Test_Object_Const__NumStatus::ARCHIVED,
                'brief' => 'story archived', 'mark' => 'a'))->
        assert_missing($a)->
        assert_undestroyable($a, array('value', 'brief', 't'));
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="Test.Object.Struct..Person" extends="Object.Struct">
class Test_Object_Struct__Person extends Object_Struct {

  protected $name;
  protected $age;
  protected $email;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///     </args>
///     <body>
  public function __construct(array $attrs = array()) {
    foreach (array('name', 'age', 'email') as $a) if (isset($attrs[$a])) $this->$a = $attrs[$a];
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="set_full_name" returns="Test.Object.Person" access="protected">
///     <body>
  protected function set_full_name() { throw new Core_ReadOnlyPropertyException('full_name'); }
///     </body>
///   </method>

///   <method name="get_full_name" returns="string" access="protected">
///     <body>
  protected function get_full_name() { return $this->name.' <'.$this->email.'>'; }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Struct" extends="Dev.Unit.TestCase">
class Test_Object_Struct extends Dev_Unit_TestCase {

  protected $object;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() { $this->object = self::make_john_doe(); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->object, array('full_name' => 'John Doe <jdoe@yoyodyne.com>'))->
      assert_read($this->object, array('name' => 'John Doe', 'age' => 35))->
      assert_write($this->object, array('name' => 'John Smith', 'age' => 30))->
      assert_undestroyable($this->object, 'full_name')->
      assert_nullable($this->object, 'name')->
      assert_missing($this->object);
  }
///     </body>
///   </method>

///   <method name="test_equality">
///     <body>
  public function test_equality() {
    $this->
      assert_true(Core::equals($this->object,  $this->object))->
      assert_true(Core::equals($this->object,  self::make_john_doe()))->
      assert_false(Core::equals($this->object, self::make_john_doe()->age(30)));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="make_john_doe" returns="Test.Object..Person" scope="class">
///     <body>
  static private function make_john_doe() {
    return Core::with(new Test_Object_Struct__Person())->
      name('John Doe')->
      age(35)->
      email('jdoe@yoyodyne.com');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Listener.EmptyDelegate">
class Test_Object_Listener_EmptyDelegate {}
/// </class>


/// <class name="Test.Object..Delegate" stereotype="abstract">
abstract class Test_Object__Delegate {

  protected $id;
  protected $tracks;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="tracks" type="ArrayObject" />
///       <arg name="id"     type="string" />
///     </args>
///     <body>
  public function __construct(ArrayObject $tracks, $id) {
    $this->tracks = $tracks;
    $this->id = $id;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run">
///     <args>
///       <arg name="value" type="string" />
///     </args>
///     <body>
  public function run($value) { $this->tracks[] = $this->id.' '.$value; }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


class Test_Object_Listener_SpecificDelegate extends Test_Object__Delegate {}

class Test_Object_Listener_CommonDelegate extends Test_Object__Delegate {}

/// <class name="Test.Object.Listener" extends="Dev.Unit.TestCase">
class Test_Object_Listener extends Dev_Unit_TestCase {

  protected $specific_listener;
  protected $common_listener;

  protected $tracks;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->tracks = Core::hash();

    $this->specific_listener =
      Object::Listener('Test.Object.Listener.SpecificDelegate')->
        append(new Test_Object_Listener_SpecificDelegate($this->tracks, 'specific1'))->
        append(new Test_Object_Listener_SpecificDelegate($this->tracks, 'specific2'));

    $this->common_listener = Object::Listener()->
      append(new Test_Object_Listener_SpecificDelegate($this->tracks, 'specific1'))->
      append(new Test_Object_Listener_EmptyDelegate())->
      append(new Test_Object_Listener_CommonDelegate($this->tracks, 'common1'));

  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_creating">
///     <body>
  public function test_creating() {
    $this->
      assert_class('Object.Listener', $this->specific_listener)->
      assert_class('Object.Listener', $this->common_listener);

    $this->set_trap();
    try {
      $this->specific_listener->append(
        new Test_Object_Listener_CommonDelegate($this->tracks, 'common2'));
    } catch (Core_InvalidArgumentTypeException $e) {
      $this->trap($e);
    }
    $this->assert_exception();
  }
///     </body>
///   </method>


///   <method name="test_specific">
///     <body>
  public function test_specific() {
    $this->set_trap();
    try {
    $this->specific_listener->
      run('test')->
      missing_method();
    } catch (Exception $e) {
      $this->trap($e);
    }
    $this->
      assert_no_exception()->
      assert_equal($this->tracks, Core::hash(array('specific1 test', 'specific2 test')));
  }
///     </body>
///   </method>

///   <method name="test_common">
///     <body>
  public function test_common() {
    $this->set_trap();
    try {
      $this->
        common_listener->
          run('test')->
          missing_method();
    } catch (Exception $e) {
      $this->trap($e);
    }
    $this->
      assert_no_exception()->
      assert_equal($this->tracks, Core::hash(array('specific1 test', 'common1 test')));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Factory..Hash" extends="ArrayObject">
class Test_Object_Factory__Hash extends ArrayObject {}
/// </class>


/// <class name="Test.Object.Factory" extends="Dev.Unit.TestCase">
class Test_Object_Factory extends Dev_Unit_TestCase {

  protected $factory;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->factory = Object::Factory('Test.Object.')->
      map(array('person' => 'Struct..Person'))->
      map('hash', 'Factory..Hash');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_class('Object.Factory', $this->factory)->
      assert_class(
        'Test.Object.Struct..Person',
        $p = $this->factory->person(array('name' => 'John Doe', 'age' => 35)))->
      assert($p->name === 'John Doe')->
      assert($p->age === 35)->
      assert_class(
        'Test.Object.Factory..Hash',
        $h = $this->factory->hash(array(1,2,3)))->
      assert_equal($h, new Test_Object_Factory__Hash(array(1,2,3)));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Aggregator..Delegate1" extends="Test.Object..Delegate">
class Test_Object_Aggregator__Delegate1 extends Test_Object__Delegate {

///   <protocol name="performing">

///   <method name="test1">
///     <args>
///       <arg name="str" type="string" />
///     </args>
///     <body>
  public function test1($str) {
    $this->run($str);
    return "$this->id $str";
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.Object.Aggregator..Delegate2" extends="Test.Object..Delegate">
class Test_Object_Aggregator__Delegate2 extends Test_Object__Delegate {

///   <protocol name="performing">

///   <method name="test1">
///     <args>
///       <arg name="str" type="string" />
///     </args>
///     <body>
  public function test2($str) {
    $this->run($str);
    return "$this->id $str";
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="Test.Object.Aggregator..Delegate3" extends="Test.Object..Delegate">
class Test_Object_Aggregator__Delegate3 extends Test_Object__Delegate {

///   <protocol name="performing">

///   <method name="test1">
///     <args>
///       <arg name="str" type="string" />
///     </args>
///     <body>
  public function test3($str) {
    $this->run($str);
    return "$this->id $str";
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="Test.Object.Aggregator" extends="Dev.Unit.TestCase">
class Test_Object_Aggregator extends Dev_Unit_TestCase {

  protected $tracks;
  protected $aggregator;

///   <protocol name="performing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->tracks = Core::hash();
    $this->aggregator = Object::Aggregator()->
      append(
        new Test_Object_Aggregator__Delegate1($this->tracks, 'test1'),
        new Test_Object_Aggregator__Delegate2($this->tracks, 'test2'))->
      fallback_to(
        Object::Aggregator()->
          append(new Test_Object_Aggregator__Delegate3($this->tracks, 'test3')));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="testing">

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->
      assert_class('Object.Aggregator', $this->aggregator);

    $this->set_trap();
    try {
      $this->aggregator->test1('call1');
      $this->aggregator->test2('call2');
      $this->aggregator->test3('call3');
    } catch (Exception $e) {
    	$this->trap($e);
    }
    $this->
      assert_no_exception()->
      assert_equal(
        $this->tracks,
        Core::hash(array('test1 call1', 'test2 call2', 'test3 call3')));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>