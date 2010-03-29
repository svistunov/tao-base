<?php
/// <module name="Test.WS.Auth" maintainer="svistunov@techart.ru" version="0.1.0">
Core::load('WS.Auth');

/// <class name="Test.WS.Auth" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class Test_WS_Auth implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.1.0';
///   </constants>
}
/// </class>

/// <class name="Test.WS.Auth.App">
///   <implements interface="WS.ServiceInterface" />
class Test_WS_Auth_App implements WS_ServiceInterface {
  protected $e;
  public $env;
///   <protocol name="processing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment"/>
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->env = $env;
    if ($this->e) throw $this->e;
    return Net_HTTP::Response('ok');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="changing">

///   <method name="exception">
///     <args>
///       <arg name="e" type="Exception" />
///     </args>
///     <body>
  public function exception(Exception $e) {
    $this->e = $e;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.WS.Auth.AuthModule">
///   <implements interface="WS.Auth.AuthModuleInterface" />
class Test_WS_Auth_AuthModule implements WS_Auth_AuthModuleInterface {

  protected $users = array(
  );

///   <protocol name="performing">

///   <method name="authenticate" returns="mixed">
///     <args>
///       <arg name="login"    type="string" />
///       <arg name="password" type="string" />
///     </args>
///     <body>
  public function authenticate($login, $password) {
    foreach ($this->users as $id => $u)
      if ($u['login'] == $login && $u['password'] == $password)
        return $u;
    return false;
  }
///     </body>
///   </method>

///   <method name="find_user" returns="mixed">
///     <args>
///       <arg name="id" type="int" />
///     </args>
///     <body>
  public function find_user($id) {
    return $this->users[$id];
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="changing">

///   <method name="user">
///     <args>
///       <arg name="id" type="mixin" />
///       <arg name="user" type="array" />
///     </args>
///     <body>
  public function user($id, array $user) {
    $this->users[$id] = $user;
    return $this;
  }
///     </body>
///   </method>

///   <method name="users">
///     <body>
  public function users($users) {
    foreach ($users as $id => $u)
      $this->user($id, $u);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>