<?php
/// <module name="WS.Session" version="0.2.0" maintainer="timokhin@techart.ru">
Core::load('Net.HTTP', 'WS');

/// <class name="WS.Session" stereotype="module">
///   <implements interface="Core.ModulteInterface" />
class WS_Session implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.2.1';
///   </constants>

  static private $store;

///   <protocol name="building">

///   <method name="Store" returns="WS.Session.Store" scope="class">
///     <body>
  static public function Store() {
    return isset(self::$store) ?
      self::$store : (self::$store = new WS_Session_Store()); }
///     </body>
///   </method>

///   <method name="Flash" returns="WS.Session.Store" scope="class">
///     <body>
  static public function Flash(array $now = array()) { return new WS_Session_Flash($now); }
///     </body>
///   </method>

///   <method name="Service" returns="WS.Session.Service" scope="class">
///     <body>
  static public function Service(WS_ServiceInterface $application) { return new WS_Session_Service($application); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="WS.Session.Store">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="Core.IndexedAccessInterface" />
///   <implements interface="WS.SessionInterface" />
class WS_Session_Store
  implements Net_HTTP_SessionInterface,
             Core_PropertyAccessInterface,
             Core_IndexedAccessInterface {

///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct() {
    session_start();
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="get" returns="mixed">
///     <args>
///       <arg name="name"    type="string" />
///       <arg name="default" />
///     </args>
///     <body>
  public function get($name, $default) { return isset($this[$name]) ? $this[$name] : $this[$name] = $default; }
///     </body>
///   </method>

///   <method name="commit" returns="void">
///     <body>
  public function commit() { session_commit(); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing" interface="Core.IndexedAccessInterface">

///   <method name="offsetSet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function offsetSet($index, $value) { $_SESSION[$index] = $value; return $this; }
///     </body>
///   </method>

///   <method name="offsetGet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetGet($index) { return $_SESSION[$index]; }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetExists($index) { return isset($_SESSION[$index]); }
///     </body>
///   </method>


///   <method name="offsetUnset">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetUnset($index) { unset($_SESSION[$index]); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing" interface="Core.PropertyAccessInterface">

///    <method name="__get" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'id':
        return session_id();
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'id':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
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
      case 'id':
        return true;
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) {
    switch ($property) {
      case 'id':
        throw new Core_UndestroyablePropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="WS.Session.Service" extends="WS.MiddlewareService">
class WS_Session_Service extends WS_MiddlewareService {

///   <protocol name="performing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $error = null;
    $session = WS_Session::Store();

    $env->request->session($session);

    $env->flash = WS_Session::Flash(
      (isset($session['flash']) && is_array($session['flash'])) ? $session['flash'] : array());

    try {
      $result = $this->application->run($env);
    } catch(Exception $e) {
      $error = $e;
    }

    $session['flash'] = $env->flash->later;
    $session->commit();

    if ($error) throw $error;
    else        return $result;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="WS.Session.Flash">
///   <implements interface="Core.IndexedAccessInterface" />
class WS_Session_Flash
  implements Core_IndexedAccessInterface,
             Core_PropertyAccessInterface {

  protected $now;
  protected $later;

///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct($now = array()) {
    $this->now = $now;
    $this->later = array();
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing" interface="Core.IndexedAccessInterface">

///   <method name="offsetGet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetGet($index) { return isset($this->now[$index]) ? $this->now[$index] : null; }
///     </body>
///   </method>

///   <method name="offsetSet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function offsetSet($index, $value) { $this->later[$index] = $value; return $this; }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetExists($index) { return array_key_exists($index, $this->now); }
///     </body>
///   </method>

///   <method name="offsetUnset">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetUnset($index) { unset($this->later[$index]); }
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
      case 'now':
      case 'later':
        return $this->$property;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <args>
///       <arg name="property" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function __set($property, $value) { throw new Core_ReadOnlyObjectException($this); }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'now':
      case 'later':
        return isset($this->$property);
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) { throw new Core_ReadOnlyObjectException($this); }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// </module>
?>