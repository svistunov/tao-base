<?php
/// <module name="Test.Net.HTTP" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Net.HTTP', 'Time', 'WS.Session');

/// <class name="Test.Net.HTTP" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Net_HTTP implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Net.HTTP.',
      'StatusCase', 'HeadCase', 'MessageCase',
      'RequestCase', 'ResponseCase', 'UploadCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.HTTP.StatusCase" extends="Dev.Unit.TestCase" >
class Test_Net_HTTP_StatusCase extends Dev_Unit_TestCase {

  protected $status;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->status = Net_HTTP::Status(300);
  }
///     </body>
///   </method>

///   <method name="test_stringify">
///     <body>
  public function test_stringify() {
    $this->asserts->stringifying->assert_string($this->status, '300 MULTIPLE CHOICES');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->status, ($o = array(
        'code' => 300,
        'full_message' => '300 MULTIPLE CHOICES',
        'is_info' => false,
        'is_success' => false,
        'is_redirect' => true,
        'is_error' => false,
        'is_client_error' => false,
        'is_server_error' => false,
        'response' => Net_HTTP::Response($this->status)
        )) + array(
        'message' => 'MULTIPLE CHOICES'))->
      assert_undestroyable($this->status, array_keys($o))->
      assert_missing($this->status);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.HTTP.HeadCase" extends="Dev.Unit.TestCase" >
class Test_Net_HTTP_HeadCase extends Dev_Unit_TestCase {

  protected $headers;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->headers = Core::with(new Net_HTTP_Head(array('connection' => 'keep-alive')))->
      field('Content-Encoding', 'gzip')->
      fields(array(
        'Content-Length' => '32591',
        'Last-Modified' => 'Wed, 03 Feb 2010 16:48:22 GMT',
        'Content-Type' => 'text/html; charset=windows-1251',
        'Date' => 'Wed, 03 Feb 2010 15:05:22 GMT'
      ))->
      nested_test('1')->
      field('nested-test', array('2', '3'))->
      field('nested-test', '4');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->headers, $o = array(
        'connection' => 'keep-alive',
        'content_encoding' => 'gzip',
        'content_length' => '32591',
        'last_modified_date' => Time::parse('Wed, 03 Feb 2010 16:48:22 GMT', Time::FMT_RFC1123),
        'content_type' => 'text/html; charset=windows-1251',
        'date' => Time::DateTime('2010-02-03 15:05:22'),
        'nested_test' => array('1', '2', '3', '4')
      ))->
      assert_write($this->headers, array(
        'test_write' => 'value'
      ))->
      assert_nullable($this->headers, array_keys($o));
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_read($this->headers, $o = array(
        'connection' => 'keep-alive',
        'content_encoding' => 'gzip',
        'content_length' => '32591',
        'last_modified' => 'Wed, 03 Feb 2010 16:48:22 GMT',
        'content_type' => 'text/html; charset=windows-1251',
        'date' => 'Wed, 03 Feb 2010 15:05:22 GMT',
        'nested_test' => array('1', '2', '3', '4')
      ))->
      assert_write($this->headers, array(
        'test_write' => 'value'
      ))->
      assert_nullable($this->headers, array_keys($o));
  }
///     </body>
///   </method>

///   <method name="test_count">
///     <body>
  public function test_count() {
    $this->assert_equal(count($this->headers), 7);
  }
///     </body>
///   </method>

///   <method name="test_iteration">
///     <body>
  public function test_iteration() {
    foreach ($this->headers as $k => $v) {
      $this->assert_equal($this->headers[$k], $v);
    }
  }
///     </body>
///   </method>

///   <method name="test_as_array">
///     <body>
  public function test_as_array() {
    $this->
      assert_equal($this->headers->as_array(),array(
        'Connection' => 'keep-alive',
        'Content-Encoding' => 'gzip',
        'Content-Length' => '32591',
        'Last-Modified' => 'Wed, 03 Feb 2010 16:48:22 GMT',
        'Content-Type' => 'text/html; charset=windows-1251',
        'Date' => 'Wed, 03 Feb 2010 15:05:22 GMT',
        'Nested-Test' => array('1', '2', '3', '4')
      ))->
      assert_equal($this->headers->as_array(true),array(
        0 => "Connection: keep-alive",
        1 => "Content-Encoding: gzip",
        2 => "Content-Length: 32591",
        3 => "Last-Modified: Wed, 03 Feb 2010 16:48:22 GMT",
        4 => "Content-Type: text/html; charset=windows-1251",
        5 => "Date: Wed, 03 Feb 2010 15:05:22 GMT",
        6 => "Nested-Test: 1",
        7 => "Nested-Test: 2",
        8 => "Nested-Test: 3",
        9 => "Nested-Test: 4"
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.HTTP.Message" extends="Net.HTTP.Message">
class Test_Net_HTTP_Message extends Net_HTTP_Message {

}
/// </class>

/// <class name="Test.Net.HTTP.MessageCase" extends="Dev.Unit.TestCase" >
class Test_Net_HTTP_MessageCase extends Dev_Unit_TestCase {
  protected $message;
///   <protocol name="testing">
///   <method name="setup">
///     <body>
  public function setup() {
    $this->message = new Test_Net_HTTP_Message();
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->message,
        $ro =array('headers' => new Net_HTTP_Head()))->
      assert_write($this->message,$w = array(
        'protocol' => 'test protocol',
        'body' => 'test body'
      ))->
      assert_read($this->message, $ro + $w)->
      assert_undestroyable($this->message, array('headers', 'protocol'))->
      assert_nullable($this->message, array('body'))->
      assert_missing($this->message);
  }
///     </body>
///   </method>

///   <method name="test_changing">
///     <body>
  public function test_changing() {
    $this->message->
      header('Connection', 'keep-alive')->
      headers(array('Content-Encoding' => 'gzip'))->
      protocol('test protocol')->
      body('test body')->
      content_length(5);
    $this->asserts->accessing->
      assert_read($this->message, array(
        'headers' => new Net_HTTP_Head(array(
          'connection' => 'keep-alive',
          'content_encoding' => 'gzip',
          'content_length' => 5
        )),
        'body' => 'test body',
        'protocol' => 'test protocol'
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.HTTP.RequestCase" extends="Dev.Unit.TestCase">
class Test_Net_HTTP_RequestCase extends Dev_Unit_TestCase {

  protected $request;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->request = Net_HTTP::Request('http://username:password@hostname/path?arg=value#anchor',
      array('REMOTE_ADDR' => '192.168.0.1'));
  }
///     </body>
///   </method>

///   <method name="test_changing">
///     <body>
  public function test_changing() {
    $this->asserts->accessing->
      assert_read($this->request, array(
        'scheme' => 'http',
        'host' => 'hostname',
        'path' => '/path',
        'method' => Net_HTTP::GET,
        'query' => 'arg=value',
        'parameters' => array('arg' => 'value'),
        'uri' => 'http://hostname/path?arg=value',
        'session' => null
      ));

    $this->request->
      parameters(array('key' => 'value'))->
      session(WS_Session::Store())->
      method('PoSt')->
      uri('https://test.com');

    $this->asserts->accessing->
      assert_read($this->request, array(
        'scheme' => 'https',
        'host' => 'test.com',
        'path' => '',
        'method' => Net_HTTP::POST,
        'query' => 'arg=value',
        'parameters' => array('key' => 'value', 'arg' => 'value'),
        'uri' => 'https://test.com?arg=value',
      ))->
      assert_exists($this->request, array('session'));
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read($this->request, array(
        'scheme' => 'http',
        'path' => '/path',
        'query' => 'arg=value',
        'method' => Net_HTTP::GET,
        'session' => null,
        'body' => null,
        'uri' => 'http://hostname/path?arg=value',
        'host' => 'hostname',
      ))->
      assert_read_only($this->request, array(
        'post_data' => '',
        'parameters' => array('arg' => 'value'),
        'meta' => array('REMOTE_ADDR' => '192.168.0.1'),
        'method_name' => 'get'
      ))->
      assert_write($this->request, array(
        'scheme' => 'https',
        'path' => '/path1',
        'query' => 'arg1=value1',
        'method' => Net_HTTP::POST,
        'body' => 'test body',
        'uri' => 'http://hostname1/path1?arg2=value2'
      ))->
      assert_exists($this->request, array('headers'))->
      assert_nullable($this->request, array('body'))->
      assert_undestroyable($this->request, array(
        'scheme', 'host', 'path', 'query', 'uri', 'session',
        'meta', 'parameters', 'post_data', 'method', 'method_name', 'headers'
      ));
  }
///     </body>
///   </method>

///   <method name="test_quering">
///     <body>
  public function test_quering() {
    $this->assert_false($this->request->is_xhr());
    $this->request->headers->field('X_REQUESTED_WITH', 'XMLHttpRequest');
    $this->assert_true($this->request->is_xhr());
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $_COOKIE['test cookie'] = 'value c';
    $_POST['test post'] = 'value p';
    $_GET['test get'] = 'value g';
    $this->request->parameters(array('test parametrs' => 'value pr'));
    $this->asserts->indexing->
      assert_write($this->request, array(
        'arg' => 'new value',
        'param' => 'value'
      ))->
      assert_read($this->request, array(
        'arg' => 'new value',
        'param' => 'value',
        'test parametrs' => 'value pr',
        'test cookie' => 'value c',
        'test post' => 'value p',
        'test get' => 'value g'
      ))->
      assert_nullable($this->request, array('test parameters'));
    $this->assert_equal($this->request->query, 'arg=new+value');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.HTTP.ResponseCase" extends="Dev.Unit.TestCase">
class Test_Net_HTTP_ResponseCase extends Dev_Unit_TestCase {

  protected $response;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->response = Net_HTTP_Response::from_string(
<<<TXT
HTTP/1.1 200 OK
Date: Fri, 05 Feb 2010 11:13:29 GMT
Expires: -1
Cache-Control: private, max-age=0
Content-Type: text/html; charset=windows-1251
Set-Cookie: PREF=ID=e7b276139c0718df; expires=Sun, 05-Feb-2012 11:13:29 GMT; path=/; domain=.google.ru
Set-Cookie: NID=31=oLbGc5JqGFNx33; expires=Sat, 07-Aug-2010 11:13:29 GMT; path=/; domain=.google.ru; HttpOnly
Server: gws
X-XSS-Protection: 0
Transfer-Encoding: chunked

<html><head></head><body>Test body</body></html>
TXT
);
  }
///     </body>
///   </method>

///   <method name="test_properties">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->response, array(
        'headers' => new Net_HTTP_Head(array(
          'date' => 'Fri, 05 Feb 2010 11:13:29 GMT',
          'expires' => '-1',
          'cache_control' => 'private, max-age=0',
          'content_type' => 'text/html; charset=windows-1251',
          'set_cookie' => array(
            0 => 'PREF=ID=e7b276139c0718df; expires=Sun, 05-Feb-2012 11:13:29 GMT; path=/; domain=.google.ru',
            1 => 'NID=31=oLbGc5JqGFNx33; expires=Sat, 07-Aug-2010 11:13:29 GMT; path=/; domain=.google.ru; HttpOnly'
          ),
          'server' => 'gws',
          'x_xss_protection' => '0',
          'transfer_encoding' => 'chunked'
        ))
      ))->assert_read($this->response, array(
        'status' => Net_HTTP::Status(200),
        'body' => '<html><head></head><body>Test body</body></html>'
      ))->assert_write($this->response, array(
        'status' => Net_HTTP::Status(500),
        'body' => 'test body'
      ))->assert_undestroyable($this->response, array('status'));
  }
///     </body>
///   </method>

///   <method name="test_changing">
///     <body>
  public function test_changing() {
    $this->response->status(500);
    $this->assert_equal($this->response->status, Net_HTTP::Status(500));
  }
///     </body>
///   </method>

///   <method name="test_creating">
///     <body>
  public function test_building() {
    $response = Net_HTTP::Response(Net_HTTP::BAD_REQUEST);
    $this->assert_equal($response->status, Net_HTTP::Status(Net_HTTP::BAD_REQUEST));

    $test_data = new ArrayObject(array('String1', 'String2')) ;
    $response = Net_HTTP::Response($test_data);
    $this->assert_equal($response->body, $test_data);

    $response = Net_HTTP::Response(null);
    $this->assert_equal($response->status, Net_HTTP::Status(Net_HTTP::NO_CONTENT));

    $response = Net_HTTP::Response(Net_HTTP::BAD_REQUEST, 'Body');
    $this->
      assert_equal($response->status, Net_HTTP::Status(Net_HTTP::BAD_REQUEST))->
      assert_equal($response->body, 'Body');

    $response = Net_HTTP::Response(Net_HTTP::BAD_REQUEST, 'Body', array('key1' => 'value1'));
    $this->
      assert_equal($response->status, Net_HTTP::Status(Net_HTTP::BAD_REQUEST))->
      assert_equal($response->body, 'Body')->
      assert_equal($response->headers['key1'], 'value1');
  }
///     </body>
///   </method>

///   </protocol>
}
///  </class>

/// <class name="Test.Net.HTTP.UploadCase" extends="Dev.Unit.TestCase">
class Test_Net_HTTP_UploadCase extends Dev_Unit_TestCase {

  protected $upload;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->upload = Net_HTTP::Upload(
      'test/data/Net/HTTP/upload_from.html',
      'test/data/Net/HTTP/upload_to.html');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
  public function test_accessing() {
    $this->
      assert_equal($this->upload->original_name, 'upload_to.html')->
      assert_equal($this->upload, IO_FS::File('test/data/Net/HTTP/upload_from.html'));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>