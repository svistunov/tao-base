<?php
/// <module name="Test.WS.Adapters.Apache" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'WS.Adapters.Apache');

/// <class name="Test.WS.Adapters.Apache" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_WS_Adapters_Apache implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.WS.Adapters.Apache.', 'AdapterCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Adapters.Apache.AdapterCase" extends="Dev.Unit.TestCase">
class Test_WS_Adapters_Apache_AdapterCase extends Dev_Unit_TestCase {
  protected $adapter;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->adapter = WS_Adapters_Apache::Adapter();
    $this->hack_apache();
  }
///     </body>
///   </method>

///   <method name="test_request">
///     <body>
 public function test_all() {

//  $a = array(
//    'k1' => 2,
//    'k2' => array('b_k1', 'b_k2'),
//  );
//  $b = array(
//    'k1' => array(3,2),
//    'k2' => 1
//  );
//var_export(Core_Arrays::deep_merge_new($a, $b));
//
//die();


    $_GET['q'] = 'search string';
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['HTTP_CONTENT_TYPE'] = 'text/html';
    $_SERVER['REQUEST_URI'] = '/path/?key1=1&key2=2';
    $_SERVER['REMOTE_ADDR'] = '192.168.1.25';
    $_POST['_method'] = 'delete';

    //<input type="file" name="submission[screenshot]['a']" />
    //<input type="file" name="submission[screenshot][1]" />
    //<input type="file" name="simple" />
    $_FILES = array (
        'submission' =>
        array (
          'name' =>
          array (
            'screenshot' =>
            array (
              'a' => 'a.jpg',
              1 => '1.jpg',
            ),
          ),
          'type' =>
          array (
            'screenshot' =>
            array (
              'a' => 'image/jpeg',
              1 => 'image/jpeg',
            ),
          ),
          'tmp_name' =>
          array (
            'screenshot' =>
            array (
              'a' => 'test/data/WS/Adapters/Apache/a',
              1 => 'test/data/WS/Adapters/Apache/1',
            ),
          ),
          'error' =>
          array (
            'screenshot' =>
            array (
              'a' => 0,
              1 => 0,
            ),
          ),
          'size' =>
          array (
            'screenshot' =>
            array (
              'a' => 24856,
              1 => 25258,
            ),
          ),
        ),
        'simple' =>
        array (
          'name' => '5e45ea659fba401428ccd3d0f96a547e-190x190.jpg',
          'type' => 'image/jpeg',
          'tmp_name' => 'test/data/WS/Adapters/Apache/simple',
          'error' => 0,
          'size' => 24665,
        ),
      );

      $request = $this->adapter->make_request();
      $this->asserts->accessing->
        assert_read($request, array(
          'scheme' => 'https',
          'host' => 'localhost',
          'path' => '/path/',
          'query' => 'key1=1&key2=2',
          'meta' => array('REMOTE_ADDR' => '192.168.1.25'),
          'method' => Net_HTTP::DELETE,
          'headers' => new Net_HTTP_Head(array(
                        'content_type' => 'text/html',
                        'host' => 'localhost'
                      ))
        ));

        $this->asserts->indexing->
          assert_read($request, array(
            'key1' => '1',
            'key2' => '2',
            'q' => 'search string',
            'submission' => array(
                'screenshot' => array(
                    'a' => IO_FS::File('test/data/WS/Adapters/Apache/a'),
                    1 => IO_FS::File('test/data/WS/Adapters/Apache/1')
              )),
            'simple' => IO_FS::File('test/data/WS/Adapters/Apache/simple')
          ));
  }
///     </body>
///   </method>

//TODO: реализовать проверку header
///   <method name="test_response">
///     <body>
  public function test_response() {
    $this->
      assert_equal(
        $this->hack_process_response(Net_HTTP::Response('ok')),
        'ok')->
      assert_equal(
        $this->hack_process_response(Net_HTTP::Response(
          array('line1', 'line2'))),
        'line1line2'
      )->
      assert_equal(
        $this->hack_process_response(Net_HTTP::Response(
          new Test_WS_Adapters_Apache_StringifyBody())),
        'test string'
      )->
      assert_equal(
        $this->hack_process_response(Net_HTTP::Response()->body(100)),
        '100'
      );
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="hack_process_response">
///     <body>
  private function hack_process_response($response) {
    ob_start();
    $this->adapter->process_response($response);
    return ob_get_clean();
  }
///     </body>
///   </method>

///   <method name="hack_apache">
///     <body>
  private function hack_apache() {
    if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
      $arh = array();
      $rx_http = '/\AHTTP_/';
      foreach ($_SERVER as $key => $val) {
        if (preg_match($rx_http, $key)) {
          $arh_key = preg_replace($rx_http, '', $key);
          $rx_matches = array();
          // do some nasty string manipulations to restore the original letter case
          // this should work in most cases
          $rx_matches = explode('_', $arh_key);
          if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
            foreach ($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
            $arh_key = implode('-', $rx_matches);
          }
          $arh[$arh_key] = $val;
        }
      }
      return( $arh );
    }
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Adapters.Apache.StringifyBody">
///   <implements interface="Core.StringifyInterface" />
class Test_WS_Adapters_Apache_StringifyBody implements Core_StringifyInterface {
///   <protocol name="stringifying">

///   <method name="as_string" returns="string">
///     <body>
  public function as_string() {
    return 'test string';
  }
///     </body>
///   </method>

///   <method name="__toString" returns="string">
///     <body>
  public function __toString() {
    $this->as_string();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>