<?php
/// <module name="Test.Mail.Serialize" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Mail.Serialize');

/// <class name="Test.Mail.Serialize" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Mail_Serialize implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Mail.Serialize.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Mail.Serialize.Case" extends="Dev.Unit.TestCase">
class Test_Mail_Serialize_Case extends Dev_Unit_TestCase {
  protected $decoder;
  protected $stream;
  protected $data;
  protected $message_from_decoder;
  protected $message_manual_constructed;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->data =
// From Thunderbird
<<<EOF
X-Account-Key: account2
X-Uidl: 48e32a5b000029bb
X-Mozilla-Status: 0001
X-Mozilla-Status2: 10000000
X-Mozilla-Keys:
Return-Path: <svistunov@techart.ru>
Received: from [192.168.0.109] (svistunov.techart.intranet [192.168.0.109])
  by mx.techart.ru (8.14.1/8.14.1) with ESMTP id o1I7RCos040367
  for <svistunov@techart.ru>; Thu, 18 Feb 2010 10:27:12 +0300 (MSK)
  (envelope-from svistunov@techart.ru)
Message-ID: <4B7CEBF8.7060901@techart.ru>
Date: Thu, 18 Feb 2010 10:27:52 +0300
From: svistunov <svistunov@techart.ru>
User-Agent: Thunderbird 2.0.0.23 (X11/20090817)
MIME-Version: 1.0
To: svistunov@techart.ru
Subject: =?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDRgdC+0L7QsdGJ0LXQvdC40LU=?=
Content-Type: Multipart/Mixed;
 boundary="------------050009090007080401070000"
Status:

This is a multi-part message in MIME format.
--------------050009090007080401070000
Content-Type: Multipart/Alternative;
 boundary="------------020204060805040402080303"


--------------020204060805040402080303
Content-Type: text/plain; charset=UTF-8; format=flowed
Content-Transfer-Encoding: 8bit


  Сообщение

Текст Text Текст Text :-)

--------------020204060805040402080303
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: 8bit

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html;charset=UTF-8" http-equiv="Content-Type">
</head>
<body bgcolor="#ffffff" text="#000000">
<h1>Сообщение</h1>
Текст Text Текст Text <span class="moz-smiley-s1"><span> :-) </span></span><br>

</body>
</html>

--------------020204060805040402080303--

--------------050009090007080401070000
Content-Type: application/x-rar;
 name="Attach.rar"
Content-Transfer-Encoding: base64
Content-Disposition: inline;
 filename="Attach.rar"

UmFyIRoHAM+QcwAADQAAAAAAAABC03QggCsAKwAAACQAAAADE7nyVTZTUjwdMwsApIEAAE1l
c3NhZ2UudHh0ERVUvhQInkKKsDBZFvCD6g3OyMWZgkItu1UmXNDFDPFSKv8OhZRMfxQdtMQ9
ewBABwA=
--------------050009090007080401070000--

EOF;



    $this->stream = IO_Stream::NamedResourceStream('php://memory')->
      write($this->data)->rewind();
    $this->decoder = Mail_Serialize::Decoder()->from($this->stream);

    $this->message_from_decoder = $this->decoder->decode();

    $this->message_manual_constructed = Mail_Message::Message(true)->
      field('X-Account-Key', 'account2')->
      field('X-UIDL', '48e32a5b000029bb')->
      field('X-Mozilla-Status', '0001')->
      field('X-Mozilla-Status2', '10000000')->
      field('X-Mozilla-Keys', '')->
      field('Return-Path', '<svistunov@techart.ru>')->
      field('Received', 'from [192.168.0.109] (svistunov.techart.intranet [192.168.0.109]) '.
              'by mx.techart.ru (8.14.1/8.14.1) with ESMTP id o1I7RCos040367 '.
              'for <svistunov@techart.ru>; Thu, 18 Feb 2010 10:27:12 +0300 (MSK) '.
              '(envelope-from svistunov@techart.ru)')->
      field('Message-ID', '<4B7CEBF8.7060901@techart.ru>')->
      field('Date', 'Thu, 18 Feb 2010 10:27:52 +0300')->
      field('From', 'svistunov <svistunov@techart.ru>')->
      field('User-Agent', 'Thunderbird 2.0.0.23 (X11/20090817)')->
      field('MIME-Version', '1.0')->
      field('To', 'svistunov@techart.ru')->
      field('Subject', 'Тестовое сообщение')->
      preamble('This is a multi-part message in MIME format.')->
      multipart_mixed("------------050009090007080401070000")->
      field('Status', '')->
      part(
        Mail_Message::Message(true)->
          multipart_alternative('------------020204060805040402080303')->
          part(Mail_Message::Part()->
                field('Content-Type', array(
                  'text/plain',
                  'charset' => 'UTF-8',
                  'format' => 'flowed'
                ))->
                field('Content-Transfer-Encoding', '8bit')->
                body("\n  Сообщение\n\nТекст Text Текст Text :-)\n\n")
          )->
          part(Mail_Message::Part()->
                field('Content-Type', array(
                  'text/html',
                  'charset' => 'UTF-8'
                ))->
                field('Content-Transfer-Encoding', '8bit')->
                body(
<<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html;charset=UTF-8" http-equiv="Content-Type">
</head>
<body bgcolor="#ffffff" text="#000000">
<h1>Сообщение</h1>
Текст Text Текст Text <span class="moz-smiley-s1"><span> :-) </span></span><br>

</body>
</html>


EOF
                )
          )
      )->
      part(
        Mail_Message::Part()->
          file('test/data/Mail/Serialize/Attach.rar', '"Attach.rar"')->
          field('Content-Disposition', array('inline', 'filename' => '"Attach.rar"'))
      );

  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_all() {
    $this->assert_equal(
      $this->message_from_decoder,
      $this->message_manual_constructed
    );


    //TODO: сравнение с учетом переноса строк
    $this->assert_equal(
            str_replace(' ', '', str_replace(MIME::LINE_END, '',
      $this->stream->load())),
            str_replace(' ', '', str_replace(MIME::LINE_END, '',
      $this->message_manual_constructed->as_string()))
    );
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// </module>

?>