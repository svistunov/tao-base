<?php
/// <module name="WS.Auth.Basic" version="0.2.0" maintainer="timokhin@techart.ru">

Core::load('WS.Auth', 'Digest');

/// <class name="WS.Auth.Basic" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class WS_Auth_Basic implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.2.1';
///   </constants>

///   <protocol name="building">

///   <method name="Service" returns="WS.Auth.Basic.Service" scope="class">
///     <args>
///       <arg name="application" type="WS.ServiceInterface" />
///       <arg name="auth_module" type="WS.Auth.AuthModuleInterface" />
///     </args>
///     <body>
  static public function Service(WS_ServiceInterface $application, WS_Auth_AuthModuleInterface $auth_module) {
    return new WS_Auth_Basic_Service($application, $auth_module);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="WS.Auth.Basic.Service" extends="WS.Auth.Service">
class WS_Auth_Basic_Service extends WS_Auth_Service {

///   <protocol name="performing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    if (!isset($env->auth))
      $env->auth = Core::object(array(
        'user'   => null,
        'module' => $this->auth_module));

    if (isset($env->request->headers['Authorization'])) {
      list($login, $password) = $this->parse_credentials($env->request->headers['Authorization']);
      $env->auth->user = $this->auth_module->authenticate($login, $password);
    }

    try {
      $response = $this->application->run($env);
    } catch (WS_Auth_UnauthenticatedException $e) {
      $response = Net_HTTP::Response(Net_HTTP::UNAUTHORIZED)->
                    header('WWW-Authenticate', 'Basic realm="'.$e->realm.'"');
    } catch (WS_Auth_ForbiddenException $e) {
      $response = Net_HTTP::Response(Net_HTTP::FORBIDDEN);
    }

    return $response;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="parse_credentials" returns="array" access="protected">
///     <args>
///       <arg name="string" type="string" />
///     </args>
///     <body>
  protected function parse_credentials($string) {
    return ($m = Core_Regexps::match_with_results(
      '{(.+):(.+)}',
      Core_Strings::decode64(
        Core_Strings::replace($string, 'Basic ', '')))) ?
          array($m[1], $m[2]) :
          array(null, null);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>
