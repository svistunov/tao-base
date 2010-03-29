<?php
/// <module name="WS.REST" version="0.2.0" maintainer="timokhin@techart.ru">
Core::load('WS', 'WS.REST.URI');

/// <class name="WS.REST" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
///   <depends supplier="WS.REST.Application" stereotype="creates" />
///   <depends supplier="WS.REST.Resource" stereotype="creates" />
///   <depends supplier="WS.REST.Method" stereotype="creates" />
class WS_REST implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.2.1';
///   </constants>

///   <protocol name="building">

///   <method name="Dispatcher" scope="class" returns="WS.REST.Dispatcher">
///     <args>
///       <arg name="mappings" type="array" />
///       <arg name="default" type="string" default="''" />
///     </args>
///     <body>
  public static function Dispatcher(array $mappings, $default = '') { return new WS_REST_Dispatcher($mappings, $default); }
///     </body>
///   </method>

///   <method name="Application" scope="class" returns="WS.REST.Application">
///     <body>
  public static function Application() { return new WS_REST_Application(); }
///     </body>
///   </method>

///   <method name="Resource" scope="class" returns="WS.REST.Resource">
///     <args>
///       <arg name="classname" type="string" />
///       <arg name="path" type="string" default="''" />
///     </args>
///     <body>
  public static function Resource($classname, $path = '') {
    return new WS_REST_Resource($classname, $path);
  }
///     </body>
///   </method>

///   <method name="Method" scope="class" returns="WS.REST.Method">
///     <args>
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public static function Method($name) { return new WS_REST_Method($name); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="WS.REST.Exception" extends="WS.Exception">
class WS_REST_Exception extends WS_Exception {}
/// </class>

/// <class name="WS.REST.Dispatcher">
///   <implements interface="WS.ServiceInterface" />
class WS_REST_Dispatcher implements WS_ServiceInterface {

  protected $mappings = array();
  protected $default  = '';

///   <protocol name="creating">

///   <method name="__constructor">
///     <args>
///       <arg name="mappings" type="array" />
///       <arg name="default" type="string" default="''" />
///     </args>
///     <body>
  public function __construct(array $mappings, $default = '') {
    $this->mappings($mappings);
    if ($default) $this->default = $default;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="mappings" returns="WS.REST.Dispatcher">
///     <args>
///       <arg name="mappings" type="array" />
///     </args>
///     <body>
  public function mappings(array $mappings) {
    foreach ($mappings as $k => $v) $this->map($k, $v);
    return $this;
  }
///     </body>
///   </method>

///   <method name="map" returns="WS.REST.Dispatcher">
///     <args>
///       <arg name="path" type="string" />
///       <arg name="classname" type="string" />
///     </args>
///     <body>
  public function map($path, $app) {
    $this->mappings[$path] = $app;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    list($prefix, $app) = array('', $this->default);

    $request = $env->request;
    $env = $env->spawn();

    foreach ($this->mappings as $k => $v)
      if (($k == '/' && preg_match('{^(/(?:index.[a-zA-Z0-9]+)?$)}', $env->request->path, $m) ||
          (preg_match("{^/$k(/.*)}", $env->request->path, $m)))) {
        $request = clone $request;
        $request->path($m[1]);
        $env->request($request);
        list($prefix, $app) = array($k, $v);
        break;
      }

    return $app ?
      $this->load_application($app, $prefix)->run($env) :
      Net_HTTP::Response(Net_HTTP::NOT_FOUND);
  }
///     </body>
///   </method>

///   </protocol>

///   <method name="setup_env" returns="WS.Environment" access="protected">
///     <args>
///       <arg name="env" type="WS.Environment" />
///       <arg name="prefix" type="string" />
///     </args>
///     <body>
  protected function setup_env(WS_Environment $env) { return $env->app($this); }
///     </body>
///   </method>


///   <protocol name="supporting">

///   <method name="load_application" returns="WS.REST.Application" access="protected">
///     <args>
///       <arg name="app" type="string|array" />
///     </args>
///     <body>
  protected function load_application($app, $prefix = '') {
    $app = (array) $app;
    $class_name = array_shift($app);

    Core::load(($class_name[0] == '-') ?
       $class_name = substr($class_name, 1) :
       Core_Types::module_name_for($class_name));

       $class_name = Core_Types::real_class_name_for($class_name);
       $instance = new $class_name($prefix, $app);

       if ($instance instanceof WS_REST_Application)
         return $instance;
       else
         throw new WS_REST_Exception('Incompatible application class: '.Core_Types::virtual_class_name_for($class_name));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="WS.REST.Application">
///   <implements interface="WS.ServiceInterface" />
///   <implements interface="Core.PropertyAccessInterface" />
///   <depends supplier="WS.REST.URI.MatchResults" stereotype="uses" />
class WS_REST_Application
  implements WS_ServiceInterface, Core_PropertyAccessInterface {

  const LOOKUP_LIMIT = 20;
  const DEFAULT_CONTENT_TYPE = 'text/html';

  protected $resources = array();
  protected $classes   = array();

  protected $media_types = array(
    'html' => 'text/html',
    'js'   => 'text/javascript',
    'json' => 'application/json',
    'xml'  => 'application/xml',
    'rss'  => 'application/xhtml+xml');

  protected $default_format = 'html';

  protected $prefix = '';

  protected $options = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  public function __construct($prefix = '', array $options = array()) {
    $this->prefix = $prefix;
    $this->options = $this->default_options();
    $this->setup($options);
  }
///     </body>
///   </method>

///   <method name="default_options" returns="array" access="protected">
///     <body>
  protected function default_options() { return array(); }
///     </body>
///   </method>

///   <method name="setup" access="protected">
///     <args>
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  protected function setup(array $options = array()) {}
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="media_type" returns="WS.REST.Application">
///     <args>
///       <arg name="format" type="string" />
///       <arg name="content_type" type="string" />
///     </args>
///     <body>
  public function media_type($format, $content_type, $is_default = false) {
    $this->media_types[$format] = $content_type;
    if ($is_default) $this->default_format = $format;
    return $this;
  }
///     </body>
///   </method>

///   <method name="media_type_for" returns="string">
///     <args>
///       <arg name="format" type="string" />
///     </args>
///     <body>
  public function media_type_for($format) { return $this->media_types[$format]; }
///     </body>
///   </method>

///   <method name="resource" returns="WS.REST.Application">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="resource" type="WS.REST.Resource" />
///     </args>
///     <body>
  public function resource($name, WS_REST_Resource $resource) {
    $this->resources[$name] = $resource;
    $this->classes[Core_Types::real_class_name_for($resource->classname)] = $resource;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run" returns="mixed">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $this->before_run($env->app($this));


    list($uri, $extension) = $this->canonicalize($env->request->path);

    $accept_formats = $this->parse_formats($env->request);

    list($target_resource, $target_method, $target_instance) = array(null, null, null);

    // Le ballet de la Merlaison, mouvement 1
    foreach ($this->resources as $resource)
      if (($match = $resource->path->match($uri)) ) {
        $target_resource = $resource;
        $uri = $match->tail;
        break;
      }

    // Le ballet de la Merlaison, mouvement 2
    if ($target_resource) {
      $target_instance = $this->instantiate($target_resource, $env, $match->parms);
      $this->after_instantiate($target_instance);
      for ($i = 0; $target_resource && ($i < self::LOOKUP_LIMIT); $i++) {
        foreach ($target_resource->methods as $method) {
          if (($uri && $method->path && ($match = $method->path->match($uri))) || (!$uri && !$method->path)) {
            if ($method->http_mask) {
              if (($method->http_mask & $env->request->method) &&
                  !$match->tail &&
                  (($format = $this->can_produce($target_resource, $method, $accept_formats, $extension)) !== false)) {
                $target_method = $method;
                break;
              }
            } else {
              if ($target_instance = $this->execute($target_instance, $method->name, $env, $match->parms)) {
                $this->after_instantiate($target_instance);
                $target_resource = $this->lookup_resource_for($target_instance);
                $uri = $match->tail;
              } else {
                $target_resource = null;
              }
              break;
            }
          }
        }
        if ($target_method) break;
      }
    }

    return ($target_resource && $target_method) ?
      (Core::with(($target_instance && $target_method) ?
        ( ($result = $this->execute(
             $target_instance,
             $target_method->name,
             $env,
             $match ? $match->parms : array(), $format)) instanceof Net_HTTP_Response ?
          $result : Net_HTTP::Response($result) )  :
        Net_HTTP::Response(Net_HTTP::NOT_FOUND))->
          content_type(isset($format) ? $format : self::DEFAULT_CONTENT_TYPE)) :
       Net_HTTP::Response(Net_HTTP::NOT_FOUND);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="can_produce">
///     <args>
///       <arg name="resource" type="WS.REST.Resource" />
///       <arg name="method" type="WS.REST.Method" />
///       <arg name="accept_formats" type="array" />
///       <arg name="exstension" type="string" />
///     </args>
///     <body>
    protected function can_produce(WS_REST_REsource $resource, WS_REST_Method $method, $accept_formats, $extension) {
    $formats = array_merge($resource->formats, $method->formats);
    if ($extension)
      return in_array($extension, $formats) ? $this->media_types[$extension] : false;

    foreach ($accept_formats as $accept_type => $q) {
      foreach ($formats as $format) {
        if (isset($this->media_types[$format])) {
          $type = $this->media_types[$format];
          if (preg_match('{^'.str_replace('*', '.+', str_replace('+', '\+', $type)).'$}', $accept_type)) return $accept_type;
          if (preg_match('{^'.str_replace('*', '.+', str_replace('+', '\+', $accept_type)).'$}', $type)) return $type;
        }
      }
    }

    if (in_array($this->default_format, $formats)) return $this->media_types[$this->default_format];
    return false;
  }
///     </body>
///   </method>

///   <method name="before_run" access="protected">
///     <args>
///       <arg name="env" type="WS.Environment" />
///     </args>
///     <body>
  protected function before_run(WS_Environment $env) { }
///     </body>
///   </method>

///   <method name="after_instantiate" access="protected">
///     <args>
///       <arg name="resource" />
///     </args>
///     <body>
  protected function after_instantiate($resource) { }
///     </body>
///   </method>

///   <method name="parse_formats">
///     <args>
///       <arg name="request" type="" />
///     </args>
///     <body>
  protected function parse_formats($request) {
    $formats = array();
    if (!$request->headers->accept) return array();
    foreach (explode(',', $request->headers->accept) as $index => $accept) {
      if (strpos($accept, '*/*') !== false) continue;
      $split = preg_split('/;\s*q=/', $accept);
      if (count($split) > 0) $formats[trim($split[0])] = (float) Core::if_not_set($split, 1, 1.0);
    }
    arsort($formats);
    return $formats;
  }
///     </body>
///   </method>

///   <method name="canonicalize" returns="array" access="protected">
///     <args>
///       <arg name="uri" type="string" />
///       <arg name="default_format" type="string" default="'html'" />
///     </args>
///     <body>
  protected function canonicalize($uri) {
    switch (true) {
      case $uri[strlen($uri) -1] == '/':
        return array("{$uri}index", null);
      case preg_match('{\.([a-zA-z0-9]+)$}', $uri, $m):
        return array(preg_replace('{\.'.$m[1].'$}', '', $uri), $m[1]);
      default:
        return array("$uri/index", null);
    }

  }
///     </body>
///   </method>


///   <method name="lookup_resource_for" returns="WS.REST.Resource">
///     <args>
///       <arg name="object" />
///     </args>
///     <body>
  protected function lookup_resource_for($object) {
    foreach (Core_Types::class_hierarchy_for($object) as $classname) {
      if (isset($this->classes[$classname])) return $this->classes[$classname];
    }
    return null;
  }
///     </body>
///   </method>

///   <method name="execute" returns="mixed" access="protected">
///     <args>
///       <arg name="instance" type="object" />
///       <arg name="method" type="string" />
///       <arg name="env" type="WS.Environment" />
///       <arg name="parms" type="array" />
///     </args>
///     <body>
  protected function execute($instance, $method, WS_Environment $env, array $parms, $format = null) {
    $reflection = new ReflectionMethod($instance, $method);
    return $reflection->invokeArgs($instance, $this->make_args($reflection->getParameters(), $env, $parms, $format));
  }
///     </body>
///   </method>

///   <method name="instantiate" returns="mixed">
///     <args>
///       <arg name="classname" type="string" />
///       <arg name="env" type="WS.Environment" />
///       <arg name="parms" type="array" />
///     </args>
///     <body>
  protected function instantiate(WS_REST_Resource $resource, WS_Environment $env, array $parms) {
    Core::load($resource->is_module ?
      $resource->classname :
      Core_Types::module_name_for($resource->classname));

    $r = new ReflectionClass(Core_Types::real_class_name_for($resource->classname));

    return ($c = $r->getConstructor()) ?
      $r->newInstanceArgs($this->make_args($c->getParameters(), $env, $parms)) :
      $r->newInstance();
  }
///     </body>
///   </method>

///   <method name="make_args" returns="array">
///     <args>
///       <arg name="args"  type="array" />
///       <arg name="env"   type="WS.Environment" />
///       <arg name="parms" type="array" />
///     </args>
///     <body>
  protected function make_args(array $args, WS_Environment $env, array $parms, $format = null) {
    $vals = array();
    foreach ($args as $arg) {
      $name = $arg->getName();
      switch ($name) {
        case 'application': $vals[] = $this;                     break;
        case 'env':         $vals[] = $env;                      break;
        case 'format':      $vals[] = $format;                   break;
        case 'parameters':
        case 'parms':       $vals[] = $env->request->parameters; break;
        case 'request':     $vals[] = $env->request;             break;
        default:
          if (isset($parms[$name]))                $vals[] = $parms[$name];
          elseif (isset($env->request[$name]))     $vals[] = $env->request[$name];
          elseif ($arg->isDefaultValueAvailable()) $vals[] = $arg->getDefaultValue();
          else                                     $vals[] = null;
      }
    }
    return $vals;
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
    switch (true) {
      case property_exists($this, $property): return $this->$property;
      case method_exists($this, $m = 'get_'.$property): return $this->$m();
      case array_key_exists($property, $this->options): return $this->options[$property];
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
    throw property_exists($this, $property) ||
          method_exists($this, 'get_'.$property) ||
          array_key_exists($property, $this->options) ?
      new Core_ReadOnlyPropertyException($property) :
      new Core_MissingPropertyException($property);
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __isset($property) {
    return property_exists($this, $property) ||
           method_exists($this, 'isset_'.$property) ||
           method_exists($this, 'get_'.$property) ||
           isset($this->options[$property]);
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <args>
///       <arg name="property" type="string" />
///     </args>
///     <body>
  public function __unset($property) {
    throw property_exists($this, $property) ||
          method_exists($this, 'get_'.$property) ||
          array_key_exists($property, $this->options) ?
      new Core_ReadOnlyPropertyException($property) :
      new Core_MissingPropertyException($property);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <composition>
///   <source class="WS.REST.Application" role="Application" multiplicity="1" />
///   <target class="WS.REST.Resource" role="Resources" multiplicity="N" />
/// </composition>

/// <class name="WS.REST.Resource">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="IteratorAggregate" />
///   <implements interface="Core.EqualityInterface" />
class WS_REST_Resource implements
  Core_PropertyAccessInterface, IteratorAggregate, Core_EqualityInterface {

  protected $classname;
  protected $is_module = false;
  protected $path;
  protected $methods     = array();
  protected $formats     = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="classname" type="string" />
///       <arg name="path" type="string" default="''" />
///     </args>
///     <body>
  public function __construct($classname, $path = '') {
    $this->classname = (($this->is_module = $classname[0] == '-') ? substr($classname, 1) : $classname);
    $this->path = WS_REST_URI::Template($path);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="produces" returns="WS.REST.Resource" varargs="true">
///     <body>
  public function produces() {
    foreach (Core::normalize_args(func_get_args()) as $format) $this->formats[] = (string) $format;
    return $this;
  }
///     </body>
///   </method>

///   <method name="method" returns="WS.REST.Resource">
///     <args>
///       <arg name="method" type="WS.REST.Method" />
///     </args>
///     <body>
  public function method(WS_REST_Method $method) {
    $this->methods[] = $method;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="iterating" interface="IteratorAggregate">

///   <method name="getIterator" returns="ArrayIterator">
///     <body>
  public function getIterator() { return new ArrayIterator($this->methods); }
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
      case 'path':
      case 'classname':
      case 'methods':
      case 'formats':
      case 'is_module':
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
      case 'path':
      case 'classname':
      case 'methods':
      case 'formats':
      case 'is_module':
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

///   <protocol name="quering">
///   <method name="equals" returns="boolean">
///     <args>
///       <arg name="to" />
///     </args>
///     <body>
  public function equals($to) {
    return get_class($this) == get_class($to) &&
      $this->classname == $to->classname &&
      $this->is_module == $to->is_module &&
      Core::equals($this->path, $to->path) &&
      Core::equals($this->methods, $to->methods) &&
      Core::equals($this->formats, $to->formats);
  }
///     </body>
///   </method>
///</protocol>
}
/// </class>

/// <aggregation>
///   <source class="WS.REST.Resource" role="resource" multiplicity="1" />
///   <target class="WS.REST.URI.Template" role="path" multiplicity="1" />
/// </aggregation>

/// <composition>
///   <source class="WS.REST.Resource" role="Resource" multiplicity="1" />
///   <target class="WS.REST.Method" role="Methods" multiplicity="N" />
/// </composition>


/// <class name="WS.REST.Method">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="Core.EqualityInterface>" />
class WS_REST_Method
  implements Core_PropertyAccessInterface, Core_EqualityInterface {

  protected $name;
  protected $http_mask = 0;
  protected $path = null;
  protected $formats = array();


///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public function __construct($name) { $this->name = $name; }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="produces" returns="WS.REST.Method" varargs="true">
///     <body>
  public function produces() {
    foreach (Core::normalize_args(func_get_args()) as $format) $this->formats[] = (string) $format;
    return $this;
  }
///     </body>
///   </method>

///   <method name="http" returns="WS.REST.Method">
///     <args>
///       <arg name="mask" type="int" />
///     </args>
///     <body>
  public function http($mask) {
    $this->http_mask = (int)$mask;
    return $this;
  }
///     </body>
///   </method>

///   <method name="path" returns="WS.REST.Method">
///     <args>
///       <arg name="path" type="string" />
///     </args>
///     <body>
  public function path($path) {
    $this->path = ($path instanceof WS_REST_URI_Template) ?
      $path :
      WS_REST_URI::Template((string) $path);
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
      case 'name':
      case 'http_mask':
      case 'path':
      case 'formats':
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
  public function __set($property, $value) {
    switch ($property) {
      case 'name':
      case 'formats':
        throw new Core_ReadOnlyPropertyException($property);
      case 'http_mask':
        {$this->http_mask = (int) $value; return $this;}
      case 'path':
        $this->path($value);
        return $this;
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
      case 'name':
      case 'http_mask':
      case 'path':
      case 'formats':
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
  public function __unset($property) {
    switch ($property) {
      case 'name':
      case 'http_mask':
      case 'path':
      case 'formats':
        throw new Core_UndestroyablePropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">
///   <method name="equals" returns="boolean">
///     <args>
///       <arg name="to" />
///     </args>
///     <body>
  public function equals($to) {
    return get_class($this) === get_class($to) &&
      $this->name == $to->name &&
      $this->http_mask == $to->http_mask &&
      Core::equals($this->path, $to->path) &&
      Core::equals($this->formats, $to->formats);
  }
///     </body>
///   </method>
///</protocol>

}
/// </class>

/// <aggregation>
///   <source class="WS.REST.Method" role="method" multiplicity="1" />
///   <target class="WS.REST.URI.Template" role="path" multiplicity="0..1" />
/// </aggregation>


/// </module>
?>