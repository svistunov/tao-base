<?php
/// <module name="Test.MIME" version="0.2.0" maintainer="timokhin@techart.ru">

Core::load('Dev.Unit', 'MIME');

/// <class name="Test.MIME" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_MIME implements Dev_Unit_TestModuleInterface {
///   <constants>
  const VERSION = '0.2.1';
///   </constants>

///   <protocol name="building">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() { return Dev_Unit::load_with_prefix('Test.MIME.', 'Module', 'Type'); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.MIME.Module" extends="Dev.Unit.TestCase">
class Test_MIME_Module extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_quering">
///     <body>
  public function test_quering() {
    $this->
      assert_string(MIME::boundary())->
      assert(MIME::default_charset() === 'UTF-8')->
      assert_true(MIME::match('text/html', 'text/*'))->
      assert_true(MIME::match('text/html', '*/*'))->
      assert_true(MIME::match('text/html', '*'))->
      assert_true(MIME::match('text/html', 'text/html'))->
      assert_false(MIME::match('text/html', 'image/jpeg'))->
      assert_false(MIME::match('text/html', 'image/*'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Test.MIME.Type" extends="Dev.Unit.TestCase">
class Test_MIME_Type extends Dev_Unit_TestCase {

  const STR_8BIT = "раз два три четыре пять, вышел зайчик погулять";
  const STR_B64  = "0YDQsNC3INC00LLQsCDRgtGA0Lgg0YfQtdGC0YvRgNC1INC/0Y/RgtGMLCDQstGL0YjQtdC7INC3\n0LDQudGH0LjQuiDQv9C+0LPRg9C70Y/RgtGM";
  const STR_QP   = "=D1=80=D0=B0=D0=B7=20=D0=B4=D0=B2=D0=B0=20=D1=82=D1=80=D0=B8=20=D1=87=D0=\n=B5=D1=82=D1=8B=D1=80=D0=B5=20=D0=BF=D1=8F=D1=82=D1=8C,=20=D0=B2=D1=8B=D1=\n=88=D0=B5=D0=BB=20=D0=B7=D0=B0=D0=B9=D1=87=D0=B8=D0=BA=20=D0=BF=D0=BE=D0=\n=B3=D1=83=D0=BB=D1=8F=D1=82=D1=8C";

  protected $t;

///   <protocol name="testing">

  protected function setup() {
    $this->t = MIME::type('text/html');
  }

///   <method name="test_building">
///     <body>
// TODO: test type_for_file
  public function test_building() {
    $this->
      assert_class('MIME.Type', $this->t)->
      assert_equal($this->t, MIME::type_for_suffix('html'));

    $this->assert_equal($this->t, MIME::type_for_file('test/data/MIME/test.html'));
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
        assert_read_only(
          $this->t,
          array(
            'type' => 'text/html',
            'name' => 'text/html',
            'encoding' => 'quoted-printable',
            'simplified' => 'text/html',
            'media_type' => 'text',
            'main_type'  => 'text',
            'subtype' => 'html',
            'extensions' => array('htm', 'html', 'shtml', 'htx', 'htmlx')))->
        assert_missing($this->t);
  }
///     </body>
///   </method>

///   <method name="test_stringifying">
///     <body>
    public function test_stringifying() {
      return $this->asserts->stringifying->
        assert_string($this->t, 'text/html');
    }
///     </body>
///   </method>

///   <method name="test_misc">
///     <body>
  public function test_misc() {
    $this->
      assert_true(MIME::is_printable('test test > ! @ < ~ ='))->
      assert_false(MIME::is_printable('тест'))->
      assert_true(MIME::is_printable_qp('test'))->
      assert_false(MIME::is_printable_qp('test test > ! @ < ~ ='))->
      assert_false(MIME::is_printable_qp('тест'));

     $this->
      assert(MIME::encode(self::STR_8BIT)                      === self::STR_B64)->
      assert(MIME::encode(self::STR_8BIT, MIME::ENCODING_B64)  === self::STR_B64)->
      assert(MIME::encode(self::STR_8BIT, MIME::ENCODING_QP)   === self::STR_QP)->
      assert(MIME::encode(self::STR_8BIT, MIME::ENCODING_8BIT) === self::STR_8BIT)->
      assert(MIME::encode(self::STR_8BIT, MIME::ENCODING_B64)  === MIME::encode_b64(self::STR_8BIT))->
      assert(MIME::encode(self::STR_8BIT, MIME::ENCODING_QP)   === MIME::encode_qp(self::STR_8BIT));

      $this->set_trap();
      try {
        MIME::encode(self::STR_8BIT, 'unknown');
      } catch (MIME_UnsupportedEncodingException $e) {
        $this->trap($e);
      }
      $this->assert_exception();

      $this->
        assert(MIME::decode(self::STR_B64)                       === self::STR_8BIT)->
        assert(MIME::decode(self::STR_B64, MIME::ENCODING_B64)   === self::STR_8BIT)->
        assert(MIME::decode(self::STR_QP, MIME::ENCODING_QP)     === self::STR_8BIT)->
        assert(MIME::decode(self::STR_8BIT, MIME::ENCODING_8BIT) === self::STR_8BIT)->
        assert(MIME::decode(self::STR_B64, MIME::ENCODING_B64)   === MIME::decode_b64(self::STR_B64))->
        assert(MIME::decode(self::STR_QP, MIME::ENCODING_QP)     === MIME::decode_qp(self::STR_QP));

      $this->set_trap();
      try {
        MIME::decode(self::STR_8BIT, 'unknown');
      } catch (MIME_UnsupportedEncodingException $e) {
        $this->trap($e);
      }
      $this->assert_exception();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>