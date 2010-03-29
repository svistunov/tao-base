<?php
/// <module name="WS.Middleware" version="0.2.0" maintainer="timokhin@techart.ru">

/// <class name="WS.Middleware" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class WS_DSL implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">

///   <method name="config" returns="WS.Middleware.Config.Service" scope="class">
///     <args>
///       <arg name="path" type="string" />
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function config($path, WS_ServiceInterface $application) {
    Core::load('WS.Middleware.Config');
    return WS_Middleware_Config::Service($application, $path);
  }
///     </body>
///   </method>

///   <method name="db" returns="WS.Middleware.DB.Service" scope="class">
///     <body>
  static public function db() {
    Core::load('WS.Middleware.DB');
    $args = func_get_args();
    return (count($args) == 1) ?
      WS_Middleware_DB::Service($args[0]) :
      WS_Middleware_DB::Service($args[1], $args[0]);
  }
///     </body>
///   </method>

///   <method name="orm" returns="WS.Middleware.ORM.Service" scope="class">
///     <body>
  static public function orm() {
    Core::load('WS.Middleware.ORM');
    $args = func_get_args();
    return (count($args) == 2) ?
      WS_Middleware_ORM::Service($args[1], $args[0]) :
      WS_Middleware_ORM::Service($args[2], $args[1], $args[0]);
  }
///     </body>
///   </method>

///   <method name="cache" returns="WS.Middleware.Cache.Service" scope="class">
///     <body>
  static public function cache() {
    Core::load('WS.Middleware.Cache');
    $args = func_get_args();
    if (count($args) == 1) return WS_Middleware_Cache::Service($args[0]);
    if (count($args) == 3) return WS_Middleware_Cache::Service($args[2], $args[0], $args[1]);
    if (count($args) == 2) {
      return is_array($args[0]) ?
        WS_Middleware_Cache::Service($args[1], '', $args[0]) :
        WS_Middleware_Cache::Service($args[1], $args[0]);
    }

  }
///     </body>
///   </method>

///   <method name="status" returns="WS.Middleware.Status.Service" scope="class">
///     <body>
  static public function status() {
    Core::load('WS.Middleware.Status');
    $args = func_get_args();
    return (count($args) == 2) ?
      WS_Middleware_Status::Service($args[1], $args[0]) :
      WS_Middleware_Status::Service($args[2], $args[1], $args[0]);
  }
///     </body>
///   </method>

///   <method name="template" returns="WS.Middleware.Template.Service" scope="class">
///     <args>
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function template($application) {
    Core::load('WS.Middleware.Template');
    return WS_Middleware_Template::Service($application);
  }
///     </body>
///   </method>

///   <method name="session" returns="WS.Session.Service" scope="class">
///     <args>
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function session($application) {
    Core::load('WS.Session');
    return WS_Session::Service($application);
  }
///     </body>
///   </method>

///   <method name="auth_session" returns="WS.Auth.Session.Service" scope="class">
///     <body>
  static public function auth_session() {
    Core::load('WS.Auth.Session');
    $args = func_get_args();
    return (count($args) == 2) ?
      WS_Auth_Session::Service($args[1], $args[0]) :
      WS_Auth_Session::Service($args[2], $args[1], $args[0]);
  }
///     </body>
///   </method>

///   <method name="auth_basic" returns="WS.Auth.Basic.Service" scope="class">
///     <args>
///       <arg name="auth_module" type="WS.Auth.AuthModuleInterface" />
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function auth_basic(WS_Auth_AuthModuleInterface $auth_module, WS_ServiceInterface $application) {
    Core::load('WS.Auth.Basic');
    return WS_Auth_Basic::Service($application, $auth_module);
  }
///     </body>
///   </method>

///   <method name="auth_openid" returns="WS.Auth.OpenID.Service" scope="class">
///     <args>
///       <arg name="ayth_module" type="WS.Auth.AuthModuleInterface" />
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function auth_openid(WS_Auth_AuthModuleInterface $auth_module, WS_ServiceInterface $application) {
    Core::load('WS.Auth.OpenID');
    return WS_Auth_OpenID::Service($application, $auth_module);
  }
///     </body>
///   </method>

///   <method name="application_dispatcher" returns="WS.Middleware.Dispatcher" scope="class">
///     <args>
///       <arg name="mappings" type="array" />
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function application_dispatcher(array $mappings, $default = '') {
    Core::load('WS.REST');
    return WS_REST::Dispatcher($mappings, $default);
  }
///     </body>
///   </method>

///   <method name="environment">
///     <args>
///       <arg name="values"      type="array" />
///       <arg name="application" type="WS.ServiceInterface" />
///     </args>
///     <body>
  static public function environment(array $values, WS_ServiceInterface $application) {
    Core::load('WS.Middleware.Environment');
    return WS_Middleware_Environment::Service($application, $values);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>