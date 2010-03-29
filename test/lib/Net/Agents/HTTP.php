<?php
/// <module name="Test.Net.Agents.HTTP" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Net.Agents.HTTP');

/// <class name="Test.Net.Agent.HTTP" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Net_Agents_HTTP implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Net.Agents.HTTP.', 'AgentCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Net.Agents.HTTP.Agent" extends="Net.Agents.HTTP.Agent">
class Test_Net_Agents_HTTP_Agent extends Net_Agents_HTTP_Agent {
  public $execute_options = array();
///   <protocol name="supporting">

///   <method name="execute">
///     <args>
///       <arg name="id" type="int" brief="curl identifer" />
///       <arg name="options" type="array" brief="массив опций" />
///     </args>
///     <body>
  protected function execute($id, $options) {
    $this->execute_options = $options;
    return parent::execute($id, $options);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.Net.Agents.HTTP.AgentCase" extends="Dev.Unit.TestCase">
class Test_Net_Agents_HTTP_AgentCase extends Dev_Unit_TestCase {
  protected $agent;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->agent = new Test_Net_Agents_HTTP_Agent(array(CURLOPT_TIMEOUT => 60));
  }
///     </body>
///   </method>

///   <method name="test_creating">
///     <body>
  public function test_creating() {
    $this->
      assert_equal(
        $this->agent->options,
        array(CURLOPT_TIMEOUT => 60));
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->agent->
      option(CURLOPT_REFERER, '192.168.0.1')->
      options(array(CURLOPT_USERAGENT => 'UFO'))->
      send(Net_HTTP::Request('http://localhost/'));
    $this->asserts->accessing->
      assert_read_only($this->agent, $ro = array(
        'options' => array(
          CURLOPT_TIMEOUT => 60,
          CURLOPT_REFERER => '192.168.0.1',
          CURLOPT_USERAGENT => 'UFO'
        ),
        'url' => 'http://localhost/'
      ))->
      assert_exists_only($this->agent, $eo = array(
        'info',
        'content_type',
        'http_code',
        'header_size',
        'request_size',
        'filetime',
        'ssl_verify_result',
        'redirect_count',
        'total_time',
        'namelookup_time',
        'connect_time',
        'pretransfer_time',
        'size_upload',
        'size_download',
        'speed_download',
        'speed_upload',
        'download_content_length',
        'upload_content_length',
        'starttransfer_time',
        'redirect_time'
      ))->
      assert_undestroyable($this->agent, array_keys($ro) + $eo)->
      assert_missing($this->agent);
  }
///     </body>
///   </method>

///   <method name="test_calling">
///     <body>
  public function test_calling() {
    $this->agent->
      with_credentials('user', 'password')->
      using_proxy('192.168.0.25', 'user', 'password')->
      timeout(50)->
      referer('192.168.0.1')->
      user_agent('UFO')->
      follow_location();
    $this->
      assert_equal(
        $this->agent->options,
        array(
          CURLOPT_TIMEOUT => 50,
          CURLOPT_USERPWD => 'user:password',
          CURLOPT_PROXY => '192.168.0.25',
          CURLOPT_PROXYUSERPWD => 'user:password',
          CURLOPT_REFERER => '192.168.0.1',
          CURLOPT_USERAGENT => 'UFO',
          CURLOPT_FOLLOWLOCATION => 1
        )
      );
  }
///     </body>
///   </method>

///   <method name="test_send">
///     <body>
  public function test_send() {
    $request = Net_HTTP::Request('http://localhost/');
    $response = $this->agent->send($request);
    $this->
      assert_class('Net.HTTP.Response', $response)->
      assert_equal($this->agent->execute_options, array());

    $request = Net_HTTP::Request('http://localhost/')->
      method(Net_HTTP::PUT)->
      header('X-Header', 'test header')->
      parameters(array('key1' => 'value1', 'key2' => 'value2'));
    $response = $this->agent->send($request);

    $this->
      assert_class('Net.HTTP.Response', $response)->
      assert_equal($this->agent->execute_options, array(
        CURLOPT_POSTFIELDS => 'key1=value1&key2=value2',
        CURLOPT_HTTPHEADER => array(
          'X-Header: test header',
          'Content-Length: 23'
        )
      ));

    $request = Net_HTTP::Request('http://localhost/')->
      method(Net_HTTP::POST)->
      header('X-Header', 'test header')->
      parameters(array('key1' => 'value1', 'key2' => 'value2'))->
      body(array('key3' => 'value3', 'key4' => 'value4'));
    $response = $this->agent->send($request);

    $this->
      assert_class('Net.HTTP.Response', $response)->
      assert_equal($this->agent->execute_options, array(
        CURLOPT_POSTFIELDS => array('key3' => 'value3', 'key4' => 'value4'),
        CURLOPT_HTTPHEADER => array(
          'X-Header: test header',
        )
      ));

    $request = Net_HTTP::Request('http://localhost/')->
      method(Net_HTTP::DELETE)->
      header('X-Header', 'test header')->
      parameters(array('key1' => 'value1', 'key2' => 'value2'))->
      body('key3=value3&key4=value4');
    $response = $this->agent->send($request);

    $this->
      assert_class('Net.HTTP.Response', $response)->
      assert_equal($this->agent->execute_options, array(
        CURLOPT_POSTFIELDS => 'key3=value3&key4=value4',
        CURLOPT_HTTPHEADER => array(
          'X-Header: test header',
          'Content-Length: 23'
        )
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>