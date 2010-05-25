<?php
/// <module name="Test.Forms" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Forms');

/// <class name="Test.Forms" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Forms implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Forms.', 'FormsCase' , 'CommonsCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Forms.IntField" extends="Forms.AbstractField">
class Test_Forms_IntField extends Forms_AbstractField {
  protected $default;
///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="name" type="itn" />
///       <arg name="default" type="string" default="''" />
///     </args>
///     <body>
  public function __construct($name, $default = 0) {
    $this->default = $default;
    parent::__construct($name, (int) $default);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="load" returns="boolean">
///     <args>
///       <arg name="source" />
///     </args>
///     <body>
  public function load($source) {
    $this->value = isset($source[$this->name]) ?
      (int) $source[$this->name] : $this->default;
    return true;
  }
///     </body>
///   </method>

///   <method name="set_value" returns="string" access="protected">
///     <args>
///       <arg name="value" type="int"/>
///     </args>
///     <body>
  protected function set_value($value) {
    return $this->value = (int) $value;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Forms.ValidatorMock" extends="Validation.Validator">
class Test_Forms_ValidationMock extends Validation_Validator {
  protected $called_methods = array();
///   <protocol name="performing">

///   <method name="validate" returns="boolean">
///     <brief>Проверяет объект всеми тестами</brief>
///     <args>
///       <arg name="object" brief="объект проверки" />
///       <arg name="array_access" type="boolean" default="false" brief="флаг индексного доступа к объекту" />
///     </args>
///     <body>
  public function validate($object, $array_access = false) {
    $this->called_methods['validate'][] = array(
      'object' => $object,
      'array_access' => $array_access
    );
    return true;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing">
///   <method name="called_methods">
///     <body>
  public function called_method($name) {
    return $this->called_methods[$name];
  }
///     </body>
///   </method>
///   </protocol>
}
/// </class>

/// <class name="Test.Forms.Forms" extends="Dev.Unit.TestCase">
class Test_Forms_FormsCase extends Dev_Unit_TestCase {

  protected $form;
  protected $validator;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->form = Forms::Form('test_form')->validate_with($this->validator = new Test_Forms_ValidationMock());
    Forms::register_field_types(array('int' => 'IntField'), 'Test.Forms.');
    $this->form->begin_fields->
      input('title', 'default_title')->
      int('order', 1)->
    end;
    $this->form->field(Forms::field('textarea', array('body', 'default_body')));
  }
///     </body>
///   </method>

///   <method name="teardoen">
///     <body>
  public function teardown() {
    unset($this->form->validator);
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
 public function test_indexing() {
   $this->asserts->indexing->
    assert_read($this->form, $o = array(
      'body' => 'default_body',
      'title' => 'default_title',
      'order' => 1
    ))->
    assert_write($this->form, array(
      'body' => 'test body',
      'title' => 'test title',
      'order' => 22
    ))->
    assert_undestroyable($this->form, array_keys($o))->
    assert_missing($this->form);
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->form, $o = array(
        'name' => 'test_form',
        'fields' => new ArrayObject(array(
          'title' => Core::with(new Forms_Commons_StringField('title', 'default_title'))->bind_to($this->form),
          'body' =>  Core::with(new Forms_Commons_TextAreaField('body', 'default_body'))->bind_to($this->form),
          'order' => Core::with(new Test_Forms_IntField('order', 1))->bind_to($this->form)
         )),
        'options' => array(
          'action' => '',
          'method' => Net_HTTP::POST,
          'enctype' => 'application/x-www-form-urlencoded'
         ),
        'errors' => null,
        'property_errors' => null,
        'global_errors' => null
      ))->assert_read($this->form, array(
        'action' => '',
        'method' => Net_HTTP::POST,
        'enctype' => 'application/x-www-form-urlencoded',
        'validator'=> $this->validator,
      ))->assert_write($this->form, array(
        'action' => 'index.php',
        'method' => Net_HTTP::GET,
        'enctype' => 'multipart/form-data',
        'validator'=> Validation::Validator()
      ))->assert_undestroyable($this->form, array_keys($o))->
      assert_nullable($this->form, array('validator'))
      ;
  }
///     </body>
///   </method>

///   <method name="test_configure">
///     <body>
  public function test_configure() {
    $this->form->
      multipart()->
      validate_with($v = Validation::Validator())->
      field($f = Forms::field('textarea', array('body_translate', 'default_body')))->
      action('index.php')->
      method(Net_HTTP::PUT);
    $this->asserts->accessing->
      assert_read($this->form, array(
        'enctype' => 'multipart/form-data',
        'validator' => $v,
        'method' => Net_HTTP::PUT,
        'action' => 'index.php'
      ));
    $this->assert_equal($this->form->fields['body_translate'], $f);
  }
///     </body>
///   </method>

///   <method name="test_assign">
///     <body>
  public function test_assign() {
    $arr_init = array(
      'body' => 'test_body',
      'title' => 'test_title',
      'order' => 25);

    $assign_to_i = new ArrayObject();
    $assign_to_p = new stdClass();
    $assign_from_i = new ArrayObject($arr_init);
    $assign_from_p = (object) $arr_init;

    $this->form->assign_to($assign_to_p);
    $this->asserts->accessing->
      assert_read($assign_to_p, $t = array(
        'body' => 'default_body',
        'title' => 'default_title',
        'order' => 1
      ));

    $this->form->assign_to($assign_to_i, true);
    $this->asserts->indexing->
      assert_read($assign_to_i, $t);

    $this->form->assign_from($assign_from_i);
    $this->asserts->indexing->
      assert_read($assign_from_i, $f = array(
        'body' => 'test_body',
        'title' => 'test_title',
        'order' => 25
      ));

    $this->form->assign_from($assign_from_p);
    $this->asserts->accessing->
      assert_read($assign_from_p, $f);
  }
///     </body>
///   </method>

///   <method name="test_processing">
///     <body>
  public function test_processing() {
    $request = Net_HTTP::Request('http://localhost/');
    $request['test_form'] = array(
      'title' => 'request_title',
      'body' => 'request_body',
      'order' => '123');

    $entity = new ArrayObject();
    $this->
    assert_true($this->form->process_entity($request, $entity))->
    asserts->indexing->
      assert_read($entity, array(
        'body' => 'request_body',
        'title' => 'request_title',
        'order' => 123
      ))->
      assert_equal($this->validator->called_method('validate'),array( 0 => array(
        'object' => $entity,
        'array_access' => true
      )));

    $this->
      assert_true($this->form->process($request))->
      asserts->indexing->
      assert_read($this->form, array(
        'body' => 'request_body',
        'title' => 'request_title',
        'order' => 123
      ))->
      assert_equal(Core::with_index($this->validator->called_method('validate'), 1), array(
        'object' => $this->form,
        'array_access' => true
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Forms.CommonsCase" extends="Dev_Unit_TestCase">
class Test_Forms_CommonsCase extends Dev_Unit_TestCase {
  protected $form;
  protected $request;

///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->form = Forms::Form('test_form');
    $this->request = Net_HTTP::Request('http://localhost/');
  }
///     </body>
///   </method>

///   <method name="test_input">
///     <body>
  public function test_input() {
    $this->form->field(Forms::field('input', array('input_name', 'default_input')));
    $this->request['test_form'] = array('input_name' => 4758);

    $this->
      assert_equal($this->form['input_name'], 'default_input')->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['input_name'], '4758')->
      assert_true(Core_Types::is_string($s = $this->form['input_name']));

    $this->form['input_name'] = 1;
    $this->assert_equal($this->form['input_name'], '1');
  }
///     </body>
///   </method>

///   <method name="test_password">
///     <body>
  public function test_password() {
    $this->form->field( Forms::field('password', array('password_name')));
    $this->request['test_form'] = array('password_name' => 3678);

    $this->
      assert_equal($this->form['password_name'], '')->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['password_name'], '3678')->
      assert_true(Core_Types::is_string($s = $this->form['password_name']));

    $this->form['password_name'] = 'new password';
    $this->assert_equal($this->form['password_name'], 'new password');
  }
///     </body>
///   </method>

///   <method name="test_textarea">
///     <body>
  public function test_textarea() {
    $this->form->field( Forms::field('textarea', array('textarea_name', 'default_textarea')));
    $this->request['test_form'] = array('textarea_name' => 123);

    $this->asserts->indexing->
      assert_equal($this->form['textarea_name'], 'default_textarea')->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['textarea_name'], '123')->
      assert_true(Core_Types::is_string($s = $this->form['textarea_name']))->
      assert_write($this->form, array(
        'textarea_name' => 'body'
      ));
  }
///     </body>
///   </method>

///   <method name="test_checkbox">
///     <body>
  public function test_checkbox() {
    $this->form->field(Forms::field('checkbox', array('checkbox_name', false)));
    $this->request['test_form'] = array('checkbox_name' => 'true');

    $this->
      assert_false($this->form['checkbox_name'])->
      assert_true($this->form->process($this->request))->
      assert_true($this->form['checkbox_name'])->
      assert_true(is_bool(($this->form['checkbox_name'])));

    $this->form['checkbox_name'] = '';
    $this->
      assert_false($this->form['checkbox_name'])->
      assert_true(is_bool(($this->form['checkbox_name'])));
  }
///     </body>
///   </method>

///   <method name="test_datetime">
///     <body>
  public function test_datetime() {
    $this->form->field(Forms::field('datetime', array(
      'datetime_name', Time::parse('2000-12-31 00:00:00'
    ))));
    $this->request['test_form'] = array( 'datetime_name' =>
      array(
        'year' => 2008,
        'month' => 11,
        'day' => 15,
        'hour' => 13,
        'minute' => 59
      ));

    $this->
      assert_equal(
        $this->form['datetime_name'],
        Time::parse('2000-12-31 00:00:00'))->
      assert_true($this->form->process($this->request))->
      assert_equal(
        $this->form['datetime_name'],
        Time::parse('2008-11-15 13:59:00'));

    $this->request['test_form'] =  array( 'datetime_name' => array('year' => 2008));

    $this->
      assert_true($this->form->process($this->request))->
      assert_equal(
        $this->form['datetime_name'],
        Time::parse('2008-01-1 00:00:00'));

    $this->request['test_form'] = array( 'datetime_name' => array('no_year' => 2008));
    $this->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['datetime_name'], null);
  }
///     </body>
///   </method>

///   <method name="test_upload">
///     <body>
  public function test_upload() {
    $this->form->field(Forms::field('upload', array('upload_name', 'C:/Windows/system32/')));
    $this->request['test_form'] = array('upload_name' => Net_HTTP::Upload('test_tmp_path', 'test_original_path'));
    $this->
      assert_equal($this->form['upload_name'], 'C:/Windows/system32/')->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['upload_name']->original_name, 'test_original_path')->
      assert_true($this->form['upload_name'] instanceof Net_HTTP_Upload);
  }
///     </body>
///   </method>

///   <method name="test_select">
///     <body>
  public function test_select() {
    $this->form->field(Forms::field('select', array('select_name', $o = array(
      0 => 'red',
      1 => 'green',
      2 => 'blue'
    ))));

    $this->request['test_form'] = array('select_name' => 1);
    $this->
      assert_equal($this->form['select_name'], null)->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['select_name'], 1);

    $this->request['test_form'] = array('select_name' => 4);
    $this->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['select_name'], null);
    $this->asserts->accessing->
      assert_read_only($this->form->fields['select_name'], array(
        'items' => $o
      ));
  }
///     </body>
///   </method>

///   <method name="test_object_select">
///     <body>
  public function test_object_select() {
    $options = array('key' => 'key', 'attribute' => 'attribute', 'allows_null' => false);
    $items = array(
      'item1' => $i1 = (object) array('key' => 'key1', 'attribute'=> 'item1', 'body' => 'body1'),
      'item2' => $i2 = (object) array('key' => 'key2', 'attribute'=> 'item2', 'body' => 'body2'),
      'item3' => $i3 = (object) array('key' => 'key3', 'attribute'=> 'item3', 'body' => 'body3')
    );
    $this->form->field(Forms::field('object_select', array('select_name', $items, $options)));
    $this->request['test_form'] = array('select_name' => 'key2');

    $this->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['select_name'], $i2);

    $this->form->fields['select_name']->value = null;
    $this->assert_equal($this->form['select_name'], $i2);

    $options['allows_null'] = true;
    $this->form->field( Forms::field('object_select', array('select_name', $items, $options)));

    $this->form->fields['select_name']->value = null;
    $this->assert_equal($this->form['select_name'], null);

    $this->asserts->accessing->
      assert_read_only($this->form->fields['select_name'],
        $options + array('items' => new ArrayObject($items)))->
      assert_missing($this->form->fields['select_name']);
  }
///     </body>
///   </method>

///   <method name="test_object_select">
///     <body>
  public function test_multi_object_select() {
    $options = array('key' => 'key', 'attribute' => 'attribute', 'allows_null' => false);
    $items = array(
      'item1' => $i1 = (object) array('key' => 'key1', 'attribute'=> 'item1', 'body' => 'body1'),
      'item2' => $i2 = (object) array('key' => 'key2', 'attribute'=> 'item2', 'body' => 'body2'),
      'item3' => $i3 = (object) array('key' => 'key3', 'attribute'=> 'item3', 'body' => 'body3')
    );
    $this->form->field(Forms::field('object_multi_select', array('select_name', $items, $options)));
    $this->request['test_form'] = array('select_name' => array('key2', 'key3'));

    $this->
      assert_true($this->form->process($this->request))->
      assert_equal($this->form['select_name'], $v = array(
        'key2' => $i2,
        'key3' => $i3,
      ));

    $this->form->fields['select_name']->value = null;
    $this->assert_equal($this->form['select_name'], $v);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>