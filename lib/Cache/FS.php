<?php
/// <module name="Cache.FS" version="0.2.0" maintainer="svistunov@techart.ru">
///   <brief>Модуль для кэширования данных ввиде файлов</brief>
Core::load('IO.FS', 'Time');

/// <class name="Cache.FS" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
///   <depends supplier="Cache.FS.Backend" stereotype="creates" />
class Cache_FS implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.2.0';
  const DEFAULT_TIMEOUT = 60;
///   </constants>

///   <protocol name="building">

///   <method name="Backend">
///   <brief>Возвращает объект класса Cache_FS_Backend</brief>
///     <args>
///       <arg name="dsn" type="string" brief="строка подключения" />
///     </args>
///     <body>
  public function Backend($dsn) { return new Cache_FS_Backend($dsn); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Cache.FS.Backend" extends="Cache.Backend">
///   <brief>Класс реализующий файловое кэширование</brief>
class Cache_FS_Backend extends Cache_Backend {

  private $path;
///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="dsn" type="string" brief="строка подключения вида: fs://путь/к/каталогу " />
///       <arg name="timeout" default="Cache_FS::DEFAULT_TIMEOUT" type="int" brief="время в течении которого хранится значение в кэше (сек)" />
///     </args>
///     <body>
  public function __construct($dsn, $timeout = Cache_FS::DEFAULT_TIMEOUT) {
    $m1 = Core_Regexps::match_with_results('{^fs://(.*)}', $dsn);
    if (!$m1) throw new Cache_BadDSNException($dsn);
    $this->path = rtrim($m1[1], '/');
    if (!IO_FS::exists($this->path))
      throw new Cache_Exception('Could not connect: directory not exist');
    $this->timeout = $timeout;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="processing">

///   <method name="get" returns="mixed">
///     <brief>Возвращает значение по ключу, если значение не установлено возвращает $default</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///       <arg name="default" default="null" brief="значение по умолчанию" />
///     </args>
///     <body>
 public function get($key, $default = null) {
    try {
      if (!$this->has($key)) return $default;
      return unserialize($this->get_content($key));
    } catch(Exception $e) {
      return $default;
    }
  }
///     </body>
///   </method>

///   <method name="set" returns="boolean">
///     <brief>Устанавливает значение по ключу с заданным таймаутом или с таймаутом по умолчанию</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///       <arg name="value" brief="значение" />
///       <arg name="timeout" type="int" brief="время в течении которого хранится значение в кэше (сек)" />
///     </args>
///     <body>
  public function set($key, $value, $timeout = null) {
    try {
      $s = IO_FS::FileStream($this->path($key), 'w+')->text();
      $s->write_line(Core_Strings::format('%10d', (time()+Core::if_null($timeout, $this->timeout))))->
        write(serialize($value))->
        close();
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
///     </body>
///   </method>

///   <method name="delete" returns="boolean">
///     <brief>Удалят значение из кэша</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///     </args>
///     <body>
  public function delete($key) {
    return IO_FS::rm($this->path($key));
  }
///     </body>
///   </method>

///   <method name="has" returns="boolean">
///     <brief>Проверяет есть ли занчение с ключом $key в кэше</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///     </args>
///     <body>
  public function has($key) {
    if (!IO_FS::exists($this->path($key))) return false;
    $timestamp = $this->get_timestamp($key);
      if ($timestamp <= time()) {
        $this->delete($key);
        return false;
      }
    return true;
  }
///     </body>
///   </method>

///   <method name="path" returns="string">
///     <brief>Возвращает путь к файлу, в котором храниться значение с ключом $key </brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///     </args>
///     <body>
  public function path($key) {
    $md5 = md5($key);
    $dir = $this->path.'/'.Core_Strings::substr($md5, 0, 2);
    if (!IO_FS::exists($dir)) IO_FS::mkdir($dir);
    return $dir."/{$md5}";
  }
///     </body>
///   </method>

///   <method name="get_timestamp" returns="int" access="protected">
///     <brief>Возвращает timestamp записанный в фаил, содержащий значение с ключом $key</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///     </args>
///     <body>
  protected function get_timestamp($key) {
    return (int) file_get_contents($this->path($key), false, null, 0, 10);
  }
///     </body>
///   </method>

///   <method name="get_content" returns="string" access="protected">
///     <brief>Извлекает из файла значение по ключу $key</brief>
///     <args>
///       <arg name="key" type="string" brief="ключ" />
///     </args>
///     <body>
  protected function get_content($key) {
    return file_get_contents($this->path($key), false, null, 11);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>