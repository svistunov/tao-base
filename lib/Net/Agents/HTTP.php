<?php
/// <module name="Net.Agents.HTTP" version="0.2.1" maintainer="timokhin@techart.ru">

Core::load('Net.HTTP');

/// <class name="Net.Agents.HTTP" stereotype="module">
///   <implements interface="Core.ConfigurableModuleInterface" />
class Net_Agents_HTTP implements Core_ConfigurableModuleInterface {

///   <constants>
  const VERSION = '0.2.1';
///   </constants>

  static protected $options = array(
    'curl_options' => array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_TIMEOUT        => 20,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_FOLLOWLOCATION => 1
      ));

///   <protocol name="creating">

///   <method name="initialize" scope="class">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  static public function initialize(array $options = array()) { self::options($options); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="options" returns="array" scope="class">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  static public function options(array $options = array()) {
    if (count($options)) Core_Arrays::update(self::$options, $options);
    return self::$options;
  }
///     </body>
///   </method>

///   <method name="option" returns="mixed" scope="class">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="value" default="null" />
///     </args>
///     <body>
  static public function option($name, $value = null) {
    $prev = isset(self::$options[$name]) ? self::$options[$name] : null;
    if ($value !== null) self::options(array($name => $value));
    return $prev;
  }
///     </body>
///   </method>

///   <method name="default_curl_options" scope="class">
///     <args>
///       <arg name="options" type="array" />
///     </args>
///     <body>
  static public function default_curl_options(array $options) { Core_Arrays::merge(self::$options['curl_options'], $options); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="Agent" returns="Curl.Agent.HTTP" scope="class">
///     <body>
  static public function Agent(array $curl_options = array()) { return new Net_Agents_HTTP_Agent($curl_options); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Net.Agents.HTTP.Agent">
///   <implements interface="Net.HTTP.AgentInterface" />
class Net_Agents_HTTP_Agent implements Net_HTTP_AgentInterface, Core_PropertyAccessInterface {

  protected $options;
  protected $info = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="curl_options" type="array" default="array()" />
///     </args>
///     <body>
  public function __construct(array $curl_options = array()) {
      $this->options = $curl_options;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="option" returns="Net.Agents.HTTP.Agent">
///     <args>
///       <arg name="option" type="int" />
///       <arg name="value" />
///     </args>
///     <body>
  public function option($option, $value) {
    $this->options[$option] = $value;
    return $this;
  }
///     </body>
///   </method>

///   <method name="options" returns="Net.Agents.HTTP.Agent">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  public function options(array $options = array()) {
    foreach ($options as $k => $v) $this->option($k, $v);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing" interface="Core.PropertyAccessInterface">

///   <method name="__get" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'options':
      case 'info':
        return $this->$property;
      default:
        if (isset($this->info[$property]))
          return $this->info[$property];
        else
          throw new Core_MissingPropertyException($property);
      }
  }
///     </body>
///   </method>

///   <method name="__set" returns="Net.Agents.HTTP.Agent">
///     <args>
///       <arg name="property" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'options':
      case 'info':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw $this->__isset($property) ?
          new Core_ReadOnlyPropertyException($property) :
          new Core_MissingPropertyException($property);
      }
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'options':
      case 'info':
        return true;
      default:
        return isset($this->info[$property]);
    }
  }
///     </body>
///   </method>

///   <method name="__unset" returns="Net.Agents.HTTP.Agent">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) {
    throw $this->__isset($property) ?
      new Core_UndestroyablePropertyException($property) :
      new Core_MissingPropertyException($property);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="mixed">
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  public function __call($method, $args) {
    switch ($method) {
      case 'with_credentials':
        return $this->option(CURLOPT_USERPWD, $args[0].(isset($args[1]) ? ':'.$args[1] : ''));
      case 'using_proxy':
        $this->option(CURLOPT_PROXY, $args[0]);
        if (isset($args[1]))
          $this->option(CURLOPT_PROXYUSERPWD, $args[1].(isset($args[2]) ? ':'.$args[2] : ''));
        return $this;
      default:
        $supported = array(
          'timeout'         => CURLOPT_TIMEOUT,
          'referer'         => CURLOPT_REFERER,
          'user_agent'      => CURLOPT_USERAGENT,
          'follow_location' => array(CURLOPT_FOLLOWLOCATION, 1));

        if (isset($supported[$method]))
          return  is_array($supported[$method]) ?
            $this->option($supported[$method][0], isset($args[0]) ? $args[0] : $supported[$method][1]) :
            $this->option($supported[$method], $args[0]);
        else
          throw new Core_MissingMethodException($method);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="send" returns="Net.HTTP.Response">
///     <args>
///       <arg name="request" type="Net.HTTP.Request" />
///     </args>
///     <body>
  public function send(Net_HTTP_Request $request) {
    $id = $this->make_curl($request->uri);
    $options = array();

    $headers = $request->headers->as_array(true);

    curl_setopt_array($id, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CUSTOMREQUEST  => strtoupper($request->method_name),
      CURLOPT_HEADER         => 1,
      CURLOPT_NOBODY         => 0));

    switch ($request->method) {
      case Net_HTTP::GET:
      case Net_HTTP::HEAD:
        break;
      case Net_HTTP::POST:
      case Net_HTTP::PUT:
      case Net_HTTP::DELETE:
        $body = isset($request->body) ?
          (is_array($request->body) ? $request->body : (string) $request->body) :
          $request->post_data;

        if (is_string($body))  $headers[] = 'Content-Length: '.strlen($body);

        $options[CURLOPT_POSTFIELDS] = $body;
        break;
    }

    if ($headers) $options[CURLOPT_HTTPHEADER] = $headers;

    $result = $this->execute($id, $options);
    $this->info = curl_getinfo($id);
    curl_close($id);

    return $result === false ? null : Net_HTTP_Response::from_string($result);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="execute">
///     <args>
///       <arg name="id" type="int" brief="curl identifer" />
///       <arg name="options" type="array" brief="массив опций" />
///     </args>
///     <body>
  protected function execute($id, $options) {
    curl_setopt_array($id, $options);
    return curl_exec($id);
  }
///     </body>
///   </method>

///   <method name="make_curl" returns="int" access="protected">
///     <args>
///       <arg name="uri" type="string" />
///     </args>
///     <body>
  protected function make_curl($uri) {
    $id = curl_init($uri);
    curl_setopt_array($id, Net_Agents_HTTP::option('curl_options'));
    curl_setopt_array($id, $this->options);
    return $id;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>