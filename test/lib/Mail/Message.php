<?php
/// <module name="Test.Mail.Message" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Mail.Message');

/// <class name="Test.Mail.Message" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Mail_Message implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Mail.Message.',
      'FieldCase', 'HeadCase', 'PartCase', 'MessageCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.Message.FieldCase" extends="Dev.Unit.TestCase">
class Test_Mail_Message_FieldCase extends Dev_Unit_TestCase {
  protected $field;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->field = Mail_Message::Field("CoNteNt-Type",
      'Multipart/Alternative;'.
      ' boundary="=_0e022aded6513529c1be211d501cd986";'.
      ' test = \'value\'');
  }
///     </body>
///   </method>

///   <method name="test_quering">
///     <body>
  public function test_quering() {
   $this->
    assert_true($this->field->matches('content-Type'))->
    assert_false($this->field->matches('contentType'));
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_read($this->field, array(
        'boundary' => '=_0e022aded6513529c1be211d501cd986',
        'test' => 'value'
      ))->
      assert_write($this->field, array(
        'boundary' => '1234',
        'test' => 'new value',
        'key' => 'value for key'
      ))->
      assert_equal(
        $this->field->body,
        'Multipart/Alternative; boundary=1234; test="new value"; key="value for key"')->
      assert_nullable($this->field, array(
        'test', 'key', 'boundary'
      ))->
      assert_equal(
        $this->field->body,
        "Multipart/Alternative");
  }
///     </body>
///   </method>

///   <method name="test_stringify">
///     <body>
  public function test_stringify() {
    $this->asserts->stringifying->
      assert_string(
        $this->field,
        "Multipart/Alternative; boundary=\"=_0e022aded6513529c1be211d501cd986\"; test = 'value'");
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->field, $o = array(
        'name' => 'Content-Type'
      ))->
      assert_read($this->field, $r = array(
        'value' => 'Multipart/Alternative',
        'body' => 'Multipart/Alternative;'.
                  ' boundary="=_0e022aded6513529c1be211d501cd986";'.
                  ' test = \'value\''
      ))->
      assert_write($this->field, $w = array(
        'value' => 'test_value',
        'body' => 'body body'
      ));

    $this->field->body(array('_value_', 'key1' => 'value1', 'key2' => 'value2'));
    $this->
      assert_equal(
        $this->field->body,
        "_value_; key1=value1; key2=value2"
        );
  }
///     </body>
///   </method>

///   <method name="test_encode">
///     <body>
  public function test_encode() {
    $this->
      assert_equal(
        $this->field->encode(),
        'Content-Type: Multipart/Alternative; boundary="=_0e022aded6513529c1be211d501cd986"; test = \'value\''
      );
    $this->field->body('Длинный заголов письма кодируется не правильно в PHP, баг iconv_mime_encode');
    $this->
      assert_equal(
        $this->field->encode(),
        //must be
//        "Content-Type: =?UTF-8?B?0JTQu9C40L3QvdGL0Lkg0LfQsNCz0L7Qu9C+0LIg0L/QuNGB0YzQvNCw?=\n".
//        " =?UTF-8?B?INC60L7QtNC40YDRg9C10YLRgdGPINC90LUg0L/RgNCw0LLQuNC70YzQvdC+INCy?=\n".
//        " =?UTF-8?B?IFBIUCwg0LHQsNCzIGljb252X21pbWVfZW5jb2Rl?="
        //php bug:
        "Content-Type: =?UTF-8?B?0JTQu9C40L3QvdGL0Lkg0LfQsNCz0L7Qu9C+0LIg0L/QuNGB0YzQvA==?=\n".
        " =?UTF-8?B?0LAg0LrQvtC00LjRgNGD0LXRgtGB0Y8g0L3QtSDQv9GA0LDQstC40Ls=?=\n".
        " =?UTF-8?B?0YzQvdC+INCyIFBIUCwg0LHQsNCzIGljb252X21pbWVfZW5jb2Rl?="
      );
    $this->field->body('Вася <vasya@techart.ru>, Петя petya@techart.ru');
    $this->
      assert_equal(
        $this->field->encode(),
        "Content-Type: =?UTF-8?B?0JLQsNGB0Y8=?= <vasya@techart.ru>,\n".
        "=?UTF-8?B?INCf0LXRgtGP?= petya@techart.ru"
      );

  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.Message.HeadCase" extends="Dev.Unit.TestCase" >
class Test_Mail_Message_HeadCase extends Dev_Unit_TestCase {
  protected $head;
  protected $data;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->head = Mail_Message_Head::from_string( $this->data =
<<<EOF
Subject: =?UTF-8?B?UHLDvGZ1bmcgUHLDvGZ1bmc=?=
To: example@example.com
Date: Thu, 1 Jan 1970 00:00:00 +0000
Message-ID: <example@example.com>
Received: from localhost (localhost [127.0.0.1]) by localhost
  with SMTP id example for <example@example.com>;
  Thu, 1 Jan 1970 00:00:00 +0000 (UTC)
  (envelope-from example-return-0000-example=example.com@example.com)
Received: (qmail 0 invoked by uid 65534); 1 Thu 2003 00:00:00 +0000

EOF
);
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_read($this->head, $r = array(
        'Subject' => new Mail_Message_Field('Subject', 'Prüfung Prüfung'),
        'To' => $to = new Mail_Message_Field('To', 'example@example.com'),
        'date' => new Mail_Message_Field('date', 'Thu, 1 Jan 1970 00:00:00 +0000'),
        'message-id' => new Mail_Message_Field('message-id', '<example@example.com>'),
        'Received' => new Mail_Message_Field('Received','from localhost (localhost [127.0.0.1]) by localhost with SMTP id example for <example@example.com>; Thu, 1 Jan 1970 00:00:00 +0000 (UTC) (envelope-from example-return-0000-example=example.com@example.com)')
        )
      )->
      assert_read_only($this->head, $o = array(
        1 => $to,
        5 => new Mail_Message_Field('Received', '(qmail 0 invoked by uid 65534); 1 Thu 2003 00:00:00 +0000')
      ));

    $this->head['to'] = 'test@techart.ru';
    $this->assert_equal($this->head['to'], new Mail_Message_Field('to', 'test@techart.ru'));
  }
///     </body>
///   </method>

///   <method name="test_building">
///     <body>
  public function test_building() {
    $this->head->
      field('From', 'test@techart.ru')->
      fields(array('X-Key1' => 'value1', 'X-Key2' => 'value2'));
    $this->asserts->indexing->
      assert_read($this->head, array(
        'From' => new Mail_Message_Field('From', 'test@techart.ru'),
        'X-Key1' => new Mail_Message_Field('X-Key1', 'value1'),
        'X-Key2' => new Mail_Message_Field('X-Key2', 'value2'),
      ));
  }
///     </body>
///   </method>

///   <method name="test_iteration">
///     <body>
  public function test_iteration() {
    $this->asserts->iterating->
      assert_read($this->head, array(
        0 => new Mail_Message_Field('Subject', 'Prüfung Prüfung'),
        1 => $to = new Mail_Message_Field('To', 'example@example.com'),
        2 => new Mail_Message_Field('date', 'Thu, 1 Jan 1970 00:00:00 +0000'),
        3 => new Mail_Message_Field('message-id', '<example@example.com>'),
        4 => new Mail_Message_Field('Received','from localhost (localhost [127.0.0.1]) by localhost with SMTP id example for <example@example.com>; Thu, 1 Jan 1970 00:00:00 +0000 (UTC) (envelope-from example-return-0000-example=example.com@example.com)'),
        5 => new Mail_Message_Field('Received', '(qmail 0 invoked by uid 65534); 1 Thu 2003 00:00:00 +0000')
        )

      );
  }
///     </body>
///   </method>

///   <method name="test_quering">
///     <body>
  public function test_quering() {
    $this->
      assert_equal(
        $this->head->get('received'),
        $r1 = new Mail_Message_Field('Received','from localhost (localhost [127.0.0.1]) by localhost with SMTP id example for <example@example.com>; Thu, 1 Jan 1970 00:00:00 +0000 (UTC) (envelope-from example-return-0000-example=example.com@example.com)')
      )->
      assert_equal(
        $this->head->get_all('received'),
        Core::hash(array(
          0 => $r1,
          1 => new Mail_Message_Field('Received', '(qmail 0 invoked by uid 65534); 1 Thu 2003 00:00:00 +0000')
        ))
      )->
      assert_equal($this->head->count_for('to'), 1)->
      assert_equal($this->head->count_for('x'), 0)->
      assert_equal($this->head->count_for('received'), 2);
  }
///     </body>
///   </method>

///   <method name="test_encode">
///     <body>
  public function test_encode() {
    //TODO: проверка с учетом переносов строк
    $this->
    assert_equal(
      str_replace(' ', '', str_replace(MIME::LINE_END, '', $this->data)),
      str_replace(' ', '', str_replace(MIME::LINE_END, '', $this->head->encode()))
    );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.Message.PartCase" extends="Dev.Unit.TestCase">
class Test_Mail_Message_PartCase extends Dev_Unit_TestCase {
  protected $part;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->part = Mail_Message::Part()->body('test body');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->part, array(
        'head' => Mail_Message::Head()
      ))->
      assert_read($this->part, array(
        'body' => 'test body'
      ))->
      assert_write($this->part, array(
        'body' => 'new body'
      ));
  }
///     </body>
///   </method>

///   <method name="test_head">
///     <body>
  public function test_head() {
    $head = Mail_Message::Head()->field('X-key', 'value0');
    $this->part->head($head)->
      field('Subject', 'test subject')->
      headers(array(
        'X-key1' => 'value1',
        'X-Key2'=> 'value2'
      ));

    $this->asserts->indexing->
      assert_equal($head, $this->part->head)->
      assert_read($this->part->head, array(
        'Subject' => Mail_Message::Field('subject', 'test subject'),
        'X-key1' => Mail_Message::Field('x-key1', 'value1'),
        'X-Key2'=> Mail_Message::Field('x-key2', 'value2'),
        'X-key' => Mail_Message::Field('x-key', 'value0')
      ));
  }
///     </body>
///   </method>

///   <method name="test_file">
///     <body>
  public function test_file() {
    $f = IO_FS::File('test/data/Mail/Message/file_part.txt');
    $this->part->file($f);

    $this->
      assert_equal($this->part->body, $f)->
      assert_equal(
        $this->part->head['content-type'],
        Mail_Message::Field(
          'content-type',
          array('text/plain', 'name' => $f->name))
      )->
      assert_equal(
        $this->part->head['content-transfer-encoding'],
        Mail_Message::Field('content-transfer-encoding', MIME::ENCODING_QP)
      )
      ;
  }
///     </body>
///   </method>

///   <method name="test_stream">
///     <body>
  public function test_stream() {
    $s = IO_Stream::NamedResourceStream('php://memory');
    $this->part->stream($s, 'application/msword');

    $this->
      assert_equal($this->part->body, $s)->
      assert_equal(
        $this->part->head['content-type'],
        Mail_Message::Field(
          'content-type',
          'application/msword')
      )->
      assert_equal(
        $this->part->head['content-transfer-encoding'],
        Mail_Message::Field('content-transfer-encoding', MIME::ENCODING_B64)
      );
  }
///     </body>
///   </method>

///   <method name="test_text">
///     <body>
  public function test_text() {
    $this->part->text('test text body');

    $this->
      assert_equal($this->part->body, 'test text body')->
      assert_equal(
        $this->part->head['content-type'],
        Mail_Message::Field(
          'content-type',
          array('text/plain', 'charset' => MIME::default_charset()))
      )->
      assert_equal(
        $this->part->head['content-transfer-encoding'],
        Mail_Message::Field('content-transfer-encoding', MIME::ENCODING_QP)
      );
  }
///     </body>
///   </method>

///   <method name="test_html">
///     <body>
  public function test_html() {
    $this->part->html($b = '<html><body>test html body</body></html>');

    $this->
      assert_equal($this->part->body, $b)->
      assert_equal(
        $this->part->head['content-type'],
        Mail_Message::Field(
          'content-type',
          array('text/html', 'charset' => MIME::default_charset()))
      )->
      assert_equal(
        $this->part->head['content-transfer-encoding'],
        Mail_Message::Field('content-transfer-encoding', MIME::ENCODING_QP)
      );
  }
///     </body>
///   </method>

///   <method name="test_calling">
///     <body>
  public function test_calling() {
    $this->part->content_type('text/html');
    $this->
      assert_equal(
        $this->part->head['content-type'],
        Mail_Message::Field('content-type', 'text/html')
      );
  }
///     </body>
///   </method>

///   <method name="test_stringify">
///     <body>
  public function test_stringify() {
    $this->asserts->stringifying->
      assert_string($this->part, Mail_Serialize::Encoder()->encode($this->part));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.Message.Message" extends="Dev.Unit.TestCase">
class Test_Mail_Message_MessageCase extends Dev_Unit_TestCase {
  protected $message;
  protected $date;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->message = Mail_Message::Message()->
      preamble('test preamble')->
      epilogue('test epilogue')->
      date($this->date = Time::now());
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->message, array(
        'preamble' => 'test preamble',
        'epilogue' => 'test epilogue',
        'head' =>  Mail_Message::Head()->
            field('MIME-Version', '1.0')->
            field('date', $this->date->as_rfc1123())
      ))->
      assert_write($this->message, $o = array(
        'preamble' => 'new preamble',
        'epilogue' => 'new epilogue'
      ))->
      assert_undestroyable($this->message, array_keys($o));
  }
///     </body>
///   </method>

///   <method name="test_multipart">
///     <body>
  public function test_multipart() {
    $this->message->multipart('test type', 'test_boundary');
    $this->assert_equal(
      $this->message->head['content-type'],
      Mail_Message::Field('content-type', array('Multipart/Test type', 'boundary' => $b = '"test_boundary"'))
    )->
    assert_true($this->message->is_multipart());

    foreach (array(
      'multipart_mixed' => 'Mixed',
      'multipart_alternative' => 'Alternative',
      'multipart_related' => 'Related',
    ) as $method => $type) {
      $this->message->$method('test_boundary');
      $this->assert_equal(
        $this->message->head['Content-Type'],
        $f = Mail_Message::Field('content-type', array('Multipart/'.$type, 'boundary' => $b)),
        sprintf('invalid content-type field for %s: %s != %s ', $method, $f,  $this->message->head['content-type'])
      )->
      assert_true(
        $this->message->is_multipart(),
        sprintf('message not multipart for %s', $method)
      );
    }
  }
///     </body>
///   </method>

///   <method name="test_part">
///     <body>
  public function test_part() {
    $this->set_trap();
    try {
      $this->message->part($p = Mail_Message::Part()->text('test text'));
    } catch (Mail_Message_Exception $e) {
      $this->trap($e);
    }
    $this->assert_true($this->is_catch_prey());

    $this->message->multipart_mixed($b = 'test boundary');
    $this->message->part($p);
    $this->assert_equal($this->message->body, Core::hash(array($p)));
  }
///     </body>
///   </method>

///   <method name="test_text_part">
///     <body>
  public function test_text_part() {
    $this->message->multipart_mixed($b = 'test boundary');
    $this->message->text_part('test text', 'text/plain');
    $this->assert_equal(
      $this->message->body,
      Core::hash(array(
        Mail_Message::Part()->text('test text', 'text/plain')
      )));
  }
///     </body>
///   </method>

///   <method name="test_html_part">
///     <body>
  public function test_html_part() {
    $this->message->multipart_mixed($b = 'test boundary');
    $this->message->html_part('test html');
    $this->assert_equal(
      $this->message->body,
      Core::hash(array(
        Mail_Message::Part()->html('test html')
      )));
  }
///     </body>
///   </method>

///   <method name="test_file_part">
///     <body>
  public function test_file_part() {
    $this->message->multipart_mixed($b = 'test boundary');
    $this->message->file_part($p = 'test/data/Mail/Message/file_part.txt', $name = 'test.txt');
    $this->assert_equal(
      $this->message->body,
      Core::hash(array(
        Mail_Message::Part()->file($p, $name)
      )));
  }
///     </body>
///   </method>

///   <method name="test_iterating">
///     <body>
  public function test_iterating() {
    $this->message->body = 'test body';
    $this->asserts->iterating->
      assert_read($this->message, array(
        0 => $this->message->body
      ));

    $this->message->
      multipart_mixed()->
      part($p1 = Mail_Message::Part()->text('txt'))->
      part($p2 = Mail_Message::Part()->html('html'));
    $this->asserts->iterating->
      assert_read($this->message, array(
        0 => $p1,
        1 => $p2
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>