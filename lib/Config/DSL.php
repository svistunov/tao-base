<?php
/// <module name="Config.DSL" version="0.2.0" maintainer="timokhin@techart.ru">
///   <brief>Модуль построения конфигурационных настроек с помощью DSL</brief>
Core::load('DSL');

/// <class name="Config.DSL" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
///   <depends supplier="Config.DSL.Builder" stereotype="creates" />
class Config_DSL implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="building">
///   <brief>Фабричный метод, возвращающий объект класса Config.DSL.Builder</brief>
///   <method name="Builder" returns="Config.DSL.Builder" scope="class">
///     <body>
  static public function Builder() { return new Config_DSL_Builder(); }
///     </body>
///   </method>

///   <method name="load" returns="Config_DSL_Object">
///   <brief>Сокращение для self::Builder()->load($file)->object</brief>
///     <args>
///       <arg name="file" type="string" brief="путь к файлу" />
///     </args>
///     <body>
  static public function load($file) { return self::Builder()->load($file)->object; }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Config.DSL.Builder" extends="DSL.Builder">
///   <brief>DSL.Builder для построения конфигурационных настроек</brief>
///   <implements interface="Core.CallInterface" />
class Config_DSL_Builder extends DSL_Builder {

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="parent" type="Config.DSL.Builder" default="null" brief="предок" />
///       <arg name="object" type="stdClass" default="null" brief="объект" />
///     </args>
///     <body>
  public function __construct(Config_DSL_Builder $parent = null, stdClass $object = null) {
    parent::__construct($parent, Core::if_null($object, new stdClass()));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="load" returns="Config.DSL.Builder">
///   <brief>Загружает конфигурационные настроеки из файла</brief>
///     <args>
///       <arg name="file" type="string" brief="путь к файлу" />
///     </args>
///     <body>
  public function load($file) {
    ob_start();
    include($file);
    ob_end_clean();
    return $this;
  }
///     </body>
///   </method>

///   <method name="begin" returns="Config.DSL.Builder">
///     <brief>Порождает новый Config.DSL.Builder с текущим объектом ввиде предка</brief>
///     <args>
///       <arg name="name" type="string" brief="имя устанавливаемого свойства" />
///     </args>
///     <body>
  public function begin($name) {
    return new Config_DSL_Builder($this, $this->object->$name = new stdClass());
  }
///       </body>
///     </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <brief>Доступ на чтение к свойствам объекта</brief>
///     <details>
///     <p>Если имя свойства начинается с begin, то порождает новый Config.DSL.Builder.</p>
///     </details>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __get($property) {
    return (strpos($property, 'begin_') === 0) ?
      $this->begin(substr($property, 6)) :
      parent::__get($property);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="mixed">
///     <brief>Перехватывает вызов метода</brief>
///     <args>
///       <arg name="method" type="string" brief="имя метода" />
///       <arg name="args"  type="array" brief="массив аргументов метода" />
///     </args>
///     <body>
  public function __call($method, $args) {
    $this->object->$method = $args[0];
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <composition>
///   <source class="Config.DSL.Builder" role="builder" />
///   <target class="stdClass" role="object" />
/// </composition>

/// <composition>
///   <source class="Config.DSL.Builder" role="builder" />
///   <target class="Config.DSL.Builder" role="parent" />
/// </composition>

/// </module>
?>