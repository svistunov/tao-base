<?php
/// <module name="Templates.HTML" version="0.2.2" maintainer="timokhin@techart.ru">
///   <brief>HTML шаблоны</brief>

Core::load('Templates', 'Object', 'Cache');

/// <class name="Templates.HTML" stereotype="module">
///   <implements interface="Core.ConfigurableModuleInterface" />
///   <depends supplier="Templates.HTML.Template" stereotype="creates" />
///   <depends supplier="Templates" stereotype="uses" />
class Templates_HTML implements Core_ConfigurableModuleInterface  {
///   <constants>
  const VERSION = '0.2.2';
///   </constants>

  static protected $helpers;

  static protected $options = array(
    'assets' => '/assets' );

///   <protocol name="creating">

///   <method name="initialize" scope="class">
///     <brief>Инициализация модуля</brief>
///     <args>
///       <arg name="options" type="array" default="array()" brief="массив опций" />
///     </args>
///     <body>
  static public function initialize(array $options = array()) {
    self::$helpers = Object::Aggregator()->fallback_to(Templates::helpers());
    self::options($options);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="options" returns="mixed" scope="class">
///     <brief>Устанавливает опции</brief>
///     <args>
///       <arg name="options" type="array" default="array()" brief="массив опций" />
///     </args>
///     <body>
  static public function options(array $options = array()) {
    if (count($options)) Core_Arrays::update(self::$options, $options);
    return self::$options;
  }
///     </body>
///   </method>

///   <method name="option" returns="mixed">
///     <brief>Устанавливает/возвращает опцию</brief>
///     <args>
///       <arg name="name" type="string" brief="имя опции" />
///       <arg name="value" default="null" brief="значение" />
///     </args>
///     <body>
  static public function option($name, $value = null) {
    $prev = isset(self::$options[$name]) ? self::$options[$name] : null;
    if ($value !== null) self::options(array($name => $value));
    return $prev;
  }
///     </body>
///   </method>

///   <method name="use_helpers" scope="class">
///     <brief>Регистрирует хелпер</brief>
///     <body>
  static public function use_helpers() {
    $args = Core::normalize_args(func_get_args());
    foreach ($args as $k => $v)
      if ($v instanceof Templates_HelperInterface) self::$helpers->append($v);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">

///   <method name="helpers" returns="Object.Aggregator">
///     <brief>Возвращает делигатор хелперов</brief>
///     <body>
  static public function helpers() { return self::$helpers; }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="Template" returns="Templates.HTML.Template" scope="class">
///     <brief>Фабричный метод, возвращает объект класса Templates.HTML.Template</brief>
///     <args>
///       <arg name="name" type="string" brief="имя шаблона" />
///     </args>
///     <body>
  static public function Template($name) { return new Templates_HTML_Template($name); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="Templates.HTML.Template" extends="Templates.NestableTemplate">
///   <brief>Класс HTML шаблона</brief>
///   <implements interface="Core.IndexedAccessInterface" />
///   <depends supplier="Templates.MissingTemplateException" stereotype="throws" />
class Templates_HTML_Template
  extends Templates_NestableTemplate
  implements Core_IndexedAccessInterface {

  private $content;

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="name" type="string" brief="имя шаблона" />
///     </args>
///     <body>
  public function __construct($name) {
    parent::__construct($name);
    $this->content = new ArrayObject();
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="begin" returns="Templates.Text.Template">
///     <brief>Начинает запись блока</brief>
///     <args>
///       <arg name="block" brief="имя блока" />
///     </args>
///     <body>
  public function begin($block) {
    ob_start();
    return $this;
  }
///     </body>
///   </method>

///   <method name="end" returns="Templates.Text.Template">
///     <brief>Заканчивает запись блока и сохраняет его</brief>
///     <args>
///       <arg name="block" brief="имя блока" />
///       <arg name="prepend" type="добавить или заменить текущий блок" />
///     </args>
///     <body>
  public function end($block, $append = true) {
    $this->content[$block] = ($append ? $this->content[$block] : '').ob_get_clean();
    return $this;
  }
///     </body>
///   </method>

///   <method name="content" returns="Templates.HTML.Template">
///     <brief>Создает блок и заполняет его контентом</brief>
///     <args>
///       <arg name="name"    type="string" brief="имя блока" />
///       <arg name="content" type="string" brief="контент/содержимое блока" />
///       <arg name="prepend" type="boolean" default="true" brief="добавить в начало или в конец блока $name" />
///     </args>
///     <body>
  public function content($name, $content, $prepend = true) {
    if ($prepend)
      $this->content[$name] = $content.$this->content[$name];
    else
      $this->content[$name] .= $content;
    return $this;
  }
///     </body>
///   </method>

///   <method name="prepend_to" returns="Templates.HTML.Template">
///     <brief>Добавить контент к концу блоку</brief>
///     <args>
///       <arg name="name"    type="string" brief="имя блока" />
///       <arg name="content" type="string" brief="контент" />
///     </args>
///     <body>
  public function prepend_to($name, $content) { return $this->content($name, $content, true); }
///     </body>
///   </method>

///   <method name="append_to" returns="Templates.HTML.Template">
///     <brief>Добавить контент к началу блоку</brief>
///     <args>
///       <arg name="name"    type="string" brief="имя блока" />
///       <arg name="content" type="string" brief="контент" />
///     </args>
///     <body>
  public function append_to($name, $content) { return $this->content($name, $content, false); }
///     </body>
///   </method>

///   <method name="if_empty" returns="Templates.HTML.Template">
///     <brief>Добавляет контент к блоку, если этот блок пустой</brief>
///     <args>
///       <arg name="block" type="string" brief="имя блока" />
///       <arg name="content" type="string" brief="контент" />
///     </args>
///     <body>
  public function if_empty($block, $content) {
    if (!isset($this->content[$block])) $this->content[$block] = $content;
    return $this;
  }
///     </body>
///   </method>

///   <method name="title" returns="Templates.HTML.Template">
///     <brief>Зополняет блок title, если он пустой</brief>
///     <args>
///       <arg name="content" type="string" brief="контент" />
///     </args>
///     <body>
  public function title($content) { return $this->if_empty('title', htmlspecialchars($content)); }
///     </body>
///   </method>

///   <method name="description" returns="Templates.HTML.Template">
///     <brief>Зополняет блок description, если он пустой</brief>
///     <args>
///       <arg name="content" type="string" />
///     </args>
///     <body>
  public function description($content) { return $this->if_empty('description', htmlspecialchars($content)); }
///     </body>
///   </method>

///   <method name="partial" returns="string">
///     <body>
  public function cached_partial(Cache_Backend $cache, $name, $params = array(), $key = null, $timeout = 60) {
    //TODO: более продвинутая генерация ключа
    $key = $key ? $key : 'templates://html/'.$this->name.'/'.$name;
    if ($cache->has($key))
      return $cache->get($key);
    else {
      $res = $this->partial($name, $params);
      $cache->set($key, $res, $timeout);
      return $res;
    }
  }
///     </body>
///   </method>

///   <method name="partial" returns="string">
///     <brief>Возвращает результат шаблона с именем $__name</brief>
///     <details>
///       Кроме имени шаблона могут переданны переменные/параметры, которые будут досутпны в шаблоне $__name
///     </details>
///     <args>
///       <arg name="name" type="string" brief="имя шаблона" />
///     </args>
///     <body>
  public function partial($__name, $__params = array()) {
    extract(array_merge($this->get_parms(), $__params));

    if (IO_FS::exists($__path = $this->get_partial_path($__name))) {
       ob_start();
       include($__path);
       return ob_get_clean();
    } else
      throw new Templates_MissingTemplateException($__path);
  }
///     </body>
///   </method>

///   <method name="compose" returns="string">
///     <brief>Компанует вместе несколько вызовов partial или/и строки</brief>
///     <details>
///       В метод может быть переданно любое количество параметров
///       Если параметр - массив, то этот массив подается на вход методу partial и результат комбинируется с другими вызовами или строками
///       Если параметр - строка, то она проста добавляется к результату
///     </details>
///     <body>
  public function compose() {
    $r = '';
    $args = func_get_args();
    foreach ($args as $part) {
      $r .= is_array($part) ?
        call_user_func_array(array($this, 'partial'), $part) :
        (string) $part;
    }
    return $r;
  }
///     </body>
///   </method>

///   <method name="tag" returns="string">
///     <brief>Формирует html-таг</brief>
///     <args>
///       <arg name="name" type="string" brief="название" />
///       <arg name="attrs" type="array" default="array()" brief="массив атрибутов" />
///       <arg name="close" type="boolean" default="true" brief="закрывать или нет таг" />
///     </args>
///     <body>
  public function tag($name, array $attrs = array(), $close = true) {
    $tag = '<'.((string) $name);

    foreach ($attrs as $k => $v)
      $tag .= ($v === true ? " $k " : ( $v === false ? '' :  " $k=\"".htmlspecialchars((string) $v).'"'));

    return $tag .= (boolean) $close ? ' />' : '>';
  }
///     </body>
///   </method>

///   <method name="content_tag" returns="string">
///     <brief>Формирует таг с контеном</brief>
///     <args>
///       <arg name="name" type="string" brief="название" />
///       <arg name="content" type="string" brief="контетн" />
///       <arg name="attrs" type="array" default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function content_tag($name, $content, array $attrs = array()) {
    $tag = '<'.((string) $name);

    foreach ($attrs as $k => $v)
      $tag .= ($v === true ? " $k " : ( $v === false ? '' :  " $k=\"".htmlspecialchars($v).'"'));

    return $tag .= '>'.((string) $content).'</'.((string) $name.'>');
  }
///     </body>
///   </method>

///   <method name="link_to" returns="string">
///     <brief>Формирует таг a</brief>
///     <args>
///       <arg name="url" type="string" brief="адрес ссылки" />
///       <arg name="content" type="string" brief="контент/содержимое тага" />
///       <arg name="attrs" type="array" default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function link_to($url, $content, array $attrs = array()) {
    return $this->content_tag('a', $content, array_merge($attrs, array('href' => $url)));
  }
///     </body>
///   </method>

///   <method name="mail_to" returns="string">
///     <brief>Формирует mailto ссылку</brief>
///     <args>
///       <arg name="address"  type="string" brief="адрес" />
///       <arg name="body" type="string" brief="содержимое/контент тага" />
///       <arg name="attributes" type="array" default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function mail_to($address, $body = '', array $attributes = array()) {
    return $this->link_to("mailto:$address", ($body ? $body : $address), $attributes);
  }
///     </body>
///   </method>

///   <method name="button_to" returns="string">
///     <brief>Формирует html-форму с кнопкой, отправляющей запрос по указанному адресу</brief>
///     <details>
///       Если массив атрибутов содержит 'confirm' параметр, тогда к кнопке будет добавлено оnclick событие, выводящее окно подтверждения действия.
///     </details>
///     <args>
///       <arg name="url"        type="string"/>
///       <arg name="text"       type="string" brief="Текст кнопки" />
///       <arg name="method"     type="string" default="'get'" brief="метод формы" />
///       <arg name="attributes" type="array"  default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function button_to($url, $text, $method = 'get', array $attributes = array()) {
    $confirmation = Core_Arrays::pick($attributes, 'confirm');
    return $this->
      content_tag('form',
        (($method == 'get' || $method == 'post') ? '' :
          $this->tag('input', array(
            'type' => 'hidden', 'name' => '_method', 'value' => $method ))).
        $this->content_tag('button', $text, array(
          'type' => 'submit',
          'onclick' => $confirmation ? 'return '.$this->make_confirmation($confirmation).';' : false,
        ) + $attributes),
        array(
          'action' => $url,
          'method' => ($method == 'get' || $method == 'post' ? $method : 'post')));
  }
///     </body>
///   </method>

///   <method name="form_button_to" returns="string">
///     <brief>Формирует кнопку, отправляющую запрос по указанному адресу</brief>
///     <args>
///       <arg name="url"        type="string" />
///       <arg name="text"       type="string" brief="текст кнопки" />
///       <arg name="method"     type="string" default="'get'" brief="метод отправки (get|put|post|delete)" />
///       <arg name="attributes" type="array"  default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function form_button_to($url, $text, $method = 'get', array $attributes = array()) {
    $confirmation = Core_Arrays::pick($attributes, 'confirm');
    return $this->tag('input', array(
      'value'   => $text,
      'type'    => 'submit',
      'onclick' => "this.form.action='$url';".
                   'this.form.method=\''.($method == 'get' || $method == 'post' ? $method : 'post').'\';'.
                   "this.form.elements._method.value='$method';".
                   'return '.$this->make_confirmation($confirmation).';') +
      $attributes);
  }
///     </body>
///   </method>

///   <method name="image">
///     <brief>Формирует img таг</brief>
///     <args>
///       <arg name="url" type="string" brief="url к картинке" />
///       <arg name="attrs" type="array" default="array()" brief="массив атрибутов" />
///     </args>
///     <body>
  public function image($url, array $attrs = array()) {
    return $this->tag('img', array_merge($attrs, array('src' => $url)));
  }
///     </body>
///   </method>

///   <method name="js_link" returns="string">
///     <brief>Формирует таг script ссылающийся на указанный js-скрипт</brief>
///     <args>
///       <arg name="path" type="string" brief="url путь к js файлу" />
///     </args>
///     <body>
  public function js_link($path) {
    return $this->content_tag('script',  '', array(
        'src' => strpos($path, 'http://') === 0 ? $path : Templates_HTML::option('assets')."/js/$path"))."\n";
  }
///     </body>
///   </method>

///   <method name="css_link" returns="string">
///     <brief>Формирует link таг ссылающийся на указанный css-файл</brief>
///     <args>
///       <arg name="path" type="string" />
///     </args>
///     <body>
  public function css_link($path) {
    return $this->tag('link',
        array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen', 'href' => Templates_HTML::option('assets')."/css/$path"))."\n";
  }
///     </body>
///   </method>

///   <method name="js" returns="Templates.HTML.Template">
///     <brief>Формирует script таги ссылающиеся на указанные файлы и добавляет результат к блоку 'js'</brief>
///     <details>
///       В качестве параметра может быть передан массив путей к файлам или же просто пути перечисленный через запятую
///     </details>
///     <body>
  public function js() {
    $s = '';
    foreach (Core::normalize_args(func_get_args()) as $v)  $s .= $this->js_link($v);
    return $this->prepend_to('js', $s);
  }
///     </body>
///   </method>

///   <method name="css" returns="Templates.HTML.Template">
///     <brief>Формирует link таги ссылающиеся на указанные файлы и добавляет результат к блоку 'css'</brief>
///     <details>
///       В качестве параметра может быть передан массив путей к файлам или же просто пути перечисленный через запятую
///     </details>
///     <body>
  public function css() {
    $s = '';
    foreach (Core::normalize_args(func_get_args()) as $v) $s .= $this->css_link($v);
    return $this->prepend_to('css', $s);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing" interface="Core.IndexedPropertyAccessInterface">

///   <method name="offsetGet" returns="mixed">
///     <brief>Возвращает содержтмое блока с именем $index</brief>
///     <args>
///       <arg name="index" type="string" brief="имя блока" />
///     </args>
///     <body>
  public function offsetGet($index) { return isset($this->content[$index]) ? $this->content[$index] : ''; }
///     </body>
///   </method>

///   <method name="offsetSet" returns="mixed">
///     <brief>Устанавливает содержимое блока</brief>
///     <args>
///       <arg name="index" type="string" brief="имя блока" />
///       <arg name="value" brief="значение" />
///     </args>
///     <body>
  public function offsetSet($index, $value) { $this->content[$index] = (string) $value; return $this; }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <brief>Проверяет существует ли блок</brief>
///     <args>
///       <arg name="index" type="string" brief="имя блока" />
///     </args>
///     <body>
  public function offsetExists($index) { return isset($this->content[$index]); }
///     </body>
///   </method>

///   <method name="offsetUnset">
///     <brief>Удаляет блок</brief>
///     <args>
///       <arg name="index" type="string" brief="имя блока" />
///     </args>
///     <body>
  public function offsetUnset($index) { unset($this->content[$index]); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="make_confirmation" returns="string">
///     <brief>Формирует js confirm вызов</brief>
///     <args>
///       <arg name="text" type="string" />
///     </args>
///     <body>
  protected function make_confirmation($text) {
    return $text ? "confirm('".Core_Strings::replace($text, "'", "\'")."')": 'true';
  }
///     </body>
///   </method>


///   <method name="get_path" returns="string">
///     <brief>Возвращает пусть к шаблону</brief>
///     <body>
  protected function get_path() { return parent::get_path().'.phtml'; }
///     </body>
///   </method>

///   <method name="get_partial_path" returns="string" access="protected">
///     <brief>Возвращает путь до partial шаблона</brief>
///     <args>
///       <arg name="name" type="string" brief="имя шаблона" />
///     </args>
///     <body>
  protected function get_partial_path($name) {
    return $name[0] == '/' ?
      Templates::option('templates_root').$name.'.phtml' :
      preg_replace('{/[^/]+$}', '', parent::get_path()).'/'.$name.'.phtml';
  }
///     </body>
///   </method>

///   <method name="get_helpers" returns="Object.Aggregator" access="protected">
///     <brief>Возвращает делегатор хелперов текущего шаблона</brief>
///     <body>
  protected function get_helpers() {
    return Core::if_null($this->helpers,
      $this->container ? $this->container->get_helpers() : Templates_HTML::helpers());
  }
///     </body>
///   </method>

///   <method name="load" access="protected" returns="Templates.HTML.Template">
///     <brief>Инклюдит указанный фаил, создавая необходимые переменные</brief>
///     <body>
  protected function load($__path) {
    extract($this->get_parms());
    if (IO_FS::exists($__path))
      include($__path);
    else
      throw new Templates_MissingTemplateException($__path);
    return $this;
  }
///     </body>
///   </method>

///   <method name="render_nested" returns="string">
///     <brief>Возвращает конечный результат</brief>
///     <args>
///       <arg name="content" type="ArrayObject" default="null" brief="контент, содержащий блоки" />
///     </args>
///     <body>
  protected function render_nested(ArrayObject $content = null) {
    if ($content) $this->content = $content;
    ob_start();
    $this->load($this->path)->content['body'] = ob_get_clean();
    return $this->container ?
      $this->container->render_nested($this->content) :
      $this->content['body'];
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>