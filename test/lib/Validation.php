<?php
/// <module name="Test.Validation" version="0.1.0" maintainer="0.1.0@techart.ru">
Core::load('Dev.Unit', 'Validation');

/// <class name="Test.Validation" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Validation implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Validation.', 'ValidatorCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Validation.EqualValidator" extends="Validation.AbstractTest">
class Test_Validation_EqualValidator extends Validation_AbstractTest {
  protected $value_to_equal;
  protected $message;
  protected $attribute;

/// <protocol name="creating">
/// <method name="__construct">
///   <body>
  public function __construct($attribute, $value, $message) {
    $this->value_to_equal = $value;
    $this->message = $message;
    $this->attribute = $attribute;
  }
///   </body>
/// </method>
/// </protocol>

///   <protocol name="performing">

///   <method name="test" returns="boolean">
///     <args>
///       <arg name="object" />
///       <arg name="errors" type="Validation.Errors" />
///       <arg name="array_access" type="boolean" default="false" />
///     </args>
///     <body>
  public function test($object, Validation_Errors $errors, $array_access = false) {
    if($this->value_of_attribute($object, $this->attribute, $array_access) != $this->value_to_equal) {
      $errors->reject($this->message);
      return false;
    }
    return true;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Validation.ValidatorCase" extends="Dev.Unit.TestCase">
class Test_Validation_ValidatorCase extends Dev_Unit_TestCase {

  protected $validator;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->validator = Validation::Validator();
  }
///     </body>
///   </method>

///   <method name="test_own_validater">
///     <body>
 public function test_own_validater() {
    Validation::use_tests(array('validate_equal_of' => 'EqualValidator'),'Test.Validation.');

    $obj = array('value' => 5);
    $this->validator->validate_equal_of('value', 5, 'Not equal');

    $this->asserts->accessing->
      assert_true($this->validator->validate($obj, true))->
      assert_true($this->validator->is_valid())->
      assert_read_only($this->validator, $o = array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash()
      ))->
      assert_undestroyable($this->validator, array_keys($o))->
      assert_missing($this->validator);
  }
///     </body>
///   </method>

///   <method name="test_presence_of">
///     <body>
  public function test_presence_of() {
    $obj = (object) array('name' => 'Sed ut perspiciatis');
    $this->validator->validate_presence_of('name', 'Empty field name');

    $this->
      assert_true($this->validator->validate($obj))->
      assert_true($this->validator->is_valid());

    $obj->name = null;
    $this->asserts->accessing->
      assert_false($this->validator->validate($obj))->
      assert_false($this->validator->is_valid())->
      assert_true($this->validator->is_invalid())->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array('name' => 'Empty field name'))
      ));
  }
///     </body>
///   </method>

///   <method name="test_email_for">
///     <body>
  public function test_email_for() {
    $obj = Core::hash(array(
      'email' => 'test@techart.ru',
      'bad_email' => 'mail@112.767'
    ));
    $this->validator->validate_email_for('bad_email', 'Bad email');
    $this->validator->validate_email_for('email', 'Bad email');

    $this->asserts->accessing->
      assert_false($this->validator->validate($obj, true))->
      assert_true($this->validator->is_invalid('bad_email'))->
      assert_true($this->validator->is_valid('email'))->
      assert_true($this->validator->is_invalid())->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array('bad_email' => 'Bad email'))
      ));
  }
///     </body>
///   </method>

///   <method name="test_format_of">
///     <body>
  public function test_format_of() {
    $obj = Core::hash(array(
      'year' => '2005',
      'bad_year' => 'year'
    ));
    $this->validator->validate_format_of('year', '/^\d{4}/', 'Bad year field');
    $this->validator->validate_format_of('bad_year', '/^\d{4}/', 'Bad year field');

    $this->
      assert_false($this->validator->validate($obj, true))->
      assert_false($this->validator->is_valid('bad_year'))->
      assert_true($this->validator->is_valid('year'))->
      assert_equal($this->validator->property_errors['bad_year'], 'Bad year field');
  }
///     </body>
///   </method>

///   <method name="test_confirmation_of">
///     <body>
  public function test_confirmation_of() {
    $obj = Core::hash(array(
      'password' => '12345',
      'confirm' => '12345',
      'bad_confirm' => '123456'
    ));
    $this->validator->
      validate_confirmation_of(
        'password',
        'confirm',
        $message = 'Non equal fields: password & confirm')->
      validate_confirmation_of(
        'password',
        'bad_confirm',
        $message);

    $this->asserts->accessing->
      assert_false($this->validator->validate($obj, true))->
      assert_false($this->validator->is_valid())->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array(
          'password' => $message,
          'bad_confirm' => $message
        ))
      ));
  }
///     </body>
///   </method>

///   <method name="test_numericality_of">
///     <body>
  public function test_numericality_of() {
    $obj = Core::hash(array(
      'number' => 123,
      'no_number' => array(123)

    ));
    $this->validator->
      validate_numericality_of('number', $m = 'must be numeric')->
      validate_numericality_of('no_number', $m);

    $this->asserts->accessing->
      assert_false($this->validator->validate($obj, true))->
      assert_false($this->validator->is_valid())->
      assert_true($this->validator->is_valid('number'))->
      assert_false($this->validator->is_valid('no_number'))->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array('no_number' => $m))
      ));

  }
///     </body>
///   </method>

///   <method name="test_range_of">
///     <body>
  public function test_range_of() {
    $obj = Core::hash(array(
      'value' => 15,
      'big_value' => 50
    ));
    $this->validator->
      validate_range_of('value', 10, 15, $m = 'Field not in range')->
      validate_range_of('big_value', 10, 15, $m);

    $this->asserts->accessing->
      assert_false($this->validator->validate($obj, true))->
      assert_false($this->validator->is_valid())->
      assert_true($this->validator->is_valid('value'))->
      assert_false($this->validator->is_valid('big_value'))->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array('big_value' => $m))
      ));
  }
///     </body>
///   </method>

  ///   <method name="test_inclusion_of">
///     <body>
  public function test_inclusion_of() {
    $obj = Core::hash(array(
      'value' => 22,
      'bad_value' => 13,
      'info' => (object) array('color' => 'red', 'style' => 'solid'),
      'bad_info' => (object) array('color' => 'green', 'style' => 'solid')
    ));
    $this->validator->
      validate_inclusion_of(
        'value',
        $values = array(1, 2, 22, 23),
        $m = 'error'
      )->
      validate_inclusion_of(
        'bad_value',
        $values,
        $m
      )->
      validate_inclusion_of(
        'info',
        $info_values = array(
          (object) array('color' => 'red', 'style' => 'solid'),
          (object) array('color' => 'blue', 'style' => 'none'),
          (object) array('color' => 'black')
        ),
        $m,
        $o = array('attribute' => 'color')
      )->
      validate_inclusion_of(
        'bad_info',
        $info_values,
        $m,
        $o
      );

    $this->asserts->accessing->
      assert_false($this->validator->validate($obj, true))->
      assert_false($this->validator->is_valid())->
      assert_true($this->validator->is_valid('value'))->
      assert_true($this->validator->is_valid('info'))->
      assert_false($this->validator->is_valid('bad_value'))->
      assert_false($this->validator->is_valid('bad_info'))->
      assert_read_only($this->validator, array(
        'global_errors' => Core::hash(),
        'property_errors' => Core::hash(array(
          'bad_value' => $m,
          'bad_info' => $m
        ))
      ))
      ;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>