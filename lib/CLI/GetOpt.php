<?php
/// <module name="CLI.GetOpt" version="0.2.1" maintainer="timokhin@techart.ru">
///   <brief>Разбор опций командной строки</brief>
///   <details>
///     <p>Встроенная поддержка работы с опциями командной строки обладает рядом ограничений,
///        в следующих версиях PHP эти возможности должны быть расширены, а пока используем
///        собственный модуль.</p>
///     <p>Модуль обеспечивает следующую функциональность:</p>
///     <ul>
///       <li>работа как с односимвольными, так и с длинными именами опций;</li>
///       <li>хранение описаний опций;</li>
///       <li>приведение типов для значений опций;</li>
///       <li>значения по умолчанию для опций, не указанных в командной строке.</li>
///     </ul>
///     <p>Для разбора опций используется объект класса CLI.GetOpt.Parser. Настройка парсера
///        выполняется путем указания списка поддерживаемых опций, при этом каждая опция
///        характеризуется следующими атрибутами:</p>
///     <ul>
///       <li>имя опции;</li>
///       <li>однобуквенный ключ опции;</li>
///       <li>длинный ключ опции;</li>
///       <li>тип опции;</li>
///       <li>значение опции (для опций, не задающих значение явно);</li>
///       <li>текстовое описание;</li>
///     </ul>
///     <p>Кроме того, для каждой опции может быть указано значение по умолчанию.</p>
///     <p>Результатом выполнения разбора является массив, содержащий значения опций. Массив
///        формируется следующим образом:</p>
///     <ul>
///       <li>если соответствующая опция присутствует в командной строке и явно задано ее значение
///           (например --limit=100), используется это значение, приведенное к типу опции;</li>
///       <li>если соответствующая опций присутствует в командной строке и ее значение не указано
///           (например --version), используется значение опции;</li>
///       <li>если опция вообше отсутствует в командной строке, используется ее значение по
///           умолчанию, если таковое было указано в настройках парсера.</li>
///     </ul>
///     <p>Например:</p>
///     <code><![CDATA[
/// $config = CLI_GetOpt::Parser()->
///   option('module', '-m', '--module', 'string', null, 'Path to module')->
///   option('show_help', '-h', '--help', 'boolean', true, 'Shows usage info')->
///   defaults(array('show_help' => false))->
///   parse($args);
///     ]]></code>
///     <p>Обратите внимание, что указания значения опции show_help true говорит о том, что если эта
///        опция присутствует в командной строке, ее значение необходимо выставить в true, в то же
///        время значение по умолчанию для нее равно false, так как если опция не указана,
///        показывать справочную информацию не нужно. Подобная логика может быть использована, а
///        частности, для любых опций, представляющих собой логические флаги.</p>
///     <p>Для выполнения разбора необходимо применить метод parse() к массиву параметров командной
///        строки. После выполнения разбора значения опций будут доступны в массиве-результате
///        выполнения метода parse(). При этом из исходного массива аргументов будут удалены все
///        все элементы, имеющие отношения к именованным параметрам, и таким образом, в ней
///        останутся только позиционные параметры.</p>
///   <p>Модуль также предоставляет упрощенный интерфейс, позволяющий выполнить создание парсера,
///      его конфигурирование и, собственно, разбор, за один вызов:</p>
///   <code><![CDATA[
/// $config = CLI_GetOpt::parse(
///   $args,
///   array(
///     array('module', '-m', '--module', 'string', null, 'Path to module'),
///     array('show_help', '-h' , '--help', 'boolean', true, 'Show usage info')),
///   array('show_help' => false));
///   ]]></code>
///   <p>При этом неявно создается экземпляр парсера, доступный в дальнейшем с помощью вызова
///      CLI_GetOpt::instance().</p>
/// </details>


/// <class name="CLI.GetOpt" stereotype="module">
///   <brief>Класс модуля</brief>
///   <implements interface="Core.ModuleInterface" />
///   <depends supplier="CLI.GetOpt.Parser" stereotype="creates" />
class CLI_GetOpt implements Core_ModuleInterface {
///   <constants>
  const MODULE  = 'CLI.GetOpt';
  const VERSION = '0.2.1';

  const USAGE_FORMAT = "%6s%s %-20s  %s\n";
///   </constants>

  static protected $parser;

///   <protocol name="building">

///   <method name="Parser" returns="CLI.GetOpt.Parser" scope="class">
///     <brief>Создает объект класса CLI.GetOpt.Parser</brief>
///     <body>
  static public function Parser() { return new CLI_GetOpt_Parser(); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="processing">

///   <method name="parse" returns="array" scope="class">
///     <brief>Вполняет разбор опций командной строки</brief>
///     <args>
///       <arg name="argv"     type="array" brief="исходные опции командной строки" />
///       <arg name="options"  type="массив описаний опций" />
///       <arg name="defaults" type="массив значений опций по умолчанию" />
///     </args>
///     <details>
///       <p>Метод обеспечивает простой процедурный интерфейс для разбора параметров командной
///          строки. Вызов метода приводит к созданию экземпляра парсера, доступ к которому можно
///          получить с помощью вызова CLI.GetOpt::instance().</p>
///     </details>
///     <body>
  static public function parse(array &$argv, array $options, array $defaults = array()) {
    self::$parser = self::Parser();

    foreach ($options as $v)
      if (Core_Types::is_array($v))
        self::$parser->option($v[0], $v[1], $v[2], $v[3], $v[4], $v[5], $v[6]);

    self::$parser->defaults($defaults);

    try {
      return self::$parser->parse($argv);
    } catch (CLI_GetOpt_Exception $e) {
      return null;
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">

///   <method name="instance" returns="CLI.GetOpt.Parse" scope="class">
///     <brief>Возвращает текущий парсер модуля</brief>
///     <details>
///       <p>Метод возвращает парсер, созданнй в результате последнего вызова
///          CLI.GetOpt::parse(), или null, если этот метод ни разу не был вызван.</p>
///     </details>
///     <body>
  static public function instance() { return self::$parser; }
///     </body>
///   </method>

///   <method name="usage_text" returns="string">
///     <brief>Возвращает текст справки по поддерживаемым ключам командной строки</brief>
///     <args>
///       <arg name="parser" type="CLI.GetOpt.Parser" default="null" brief="парсер" />
///     </args>
///     <details>
///       <p>Генерируемый список может быть использован для вывода справки при указании ключа
///          -h.</p>
///     </details>
///     <body>
  static public function usage_text(CLI_GetOpt_Parser $parser = null) {
    if (!($parser = $parser ? $parser : self::$parser)) return '';

    $result = '';
    foreach ($parser->options as $option) {
      $result .= sprintf(CLI_GetOpt::USAGE_FORMAT,
        $option->short,
        $option->short ? ',' : ' ',
        $option->long,
        $option->comment);
    }
    return $result;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="CLI.GetOpt.Exception" extends="Core.Exception" stereotype="exception">
///   <brief>Базовый класс исключений</brief>
class CLI_GetOpt_Exception extends Core_Exception {}
/// </class>


/// <class name="CLI.GetOpt.Parser">
///   <brief>Парсер опций командной строки</brief>
///   <implements interface="Core.PropertyAccessInterface" />
///   <depends supplier="CLI.GetOpt.Exception" stereotype="throws" />
///   <details>
///     <p>Свойства:</p>
///     <dl>
///       <dt>script</dt><dd>имя запускаемого скрипта (самый первый элемент строки аргументов);</dd>
///       <dt>options</dt><dd>список структур, описывающих опции.</dd>
///     </dl>
///   </details>
class CLI_GetOpt_Parser
  implements Core_PropertyAccessInterface {

  protected $script;
  protected $options;

  protected $defaults = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <body>
  public function __construct() { $this->options = Core::hash(); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="defaults" returns="GetOpt.Parser">
///     <brief>Устанавливает значения опций по умолчанию</brief>
///     <args>
///       <arg name="value" type="array" brief="массив значений" />
///     </args>
///     <details>
///       <p>Обратите внимание, что значение по умолчанию используется в случае, если опция не была указана в командной
///          строке. Если опция присутствует в командной строке, но ее значение не указано явно,
///          используется значение опции.</p>
///     </details>
///     <body>
  public function defaults(array $values) {
    foreach ($values as $k => $v) $this->defaults[$k] = $v;
    return $this;
  }
///     </body>
///   </method>

///   <method name="option" returns="CLI.GetOpt">
///     <brief>Определяет опцию</brief>
///     <args>
///       <arg name="name"  type="string" brief="имя опции" />
///       <arg name="short" type="string" brief="короткое имя опции" />
///       <arg name="long"  type="string" brief="длинное имя" />
///       <arg name="type"  type="string" brief="тип" />
///       <arg name="value" brief="значение" />
///       <arg name="comment" type="string" brief="комментарий" />
///     </args>
///     <details>
///       <p>Для приведения типов значений опций используется встроенная функция settype().
///          Соответственно, значение параметра $type должно быть допустимым именем типа для этой
///          функции.</p>
///     </details>
///     <body>
  public function option($name, $short, $long, $type, $value, $comment) {
    $this->options[] = Core::object(compact('name', 'short', 'long', 'type', 'value', 'comment'));
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="processing">

///   <method name="parse" returns="array">
///     <brief>Выполняет разбор опций</brief>
///     <args>
///       <arg name="source" type="array" brief="массив опций" />
///     </args>
///     <details>
///       <p>Резултатом выполнения является массив, содержащий значения опций. Из исходного массив
///          аргументов удаляются все элементы, для которых был выполнен разбор, таким образом,
///          остаются только позиционные аргументы.</p>
///     </details>>
///     <body>
  public function parse(array &$source) {
    $result = $this->defaults;
    $this->script = Core_Arrays::shift($source);

    while (count($source) > 0) {
      $arg = $source[0];
      if ($option_name = $this->option_name_for($arg)) {
        if ($option = $this->option_for($option_name))
          $result[$option->name] = $this->value_for($option, $arg);
        else
          throw new CLI_GetOpt_Exception("Unknown option: $option_name");
         Core_Arrays::shift($source);
      } else break;
    }
    return $result;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <brief>Возврашает значение свойства</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'options':
        return $this->options->getIterator();
      case 'script':
        return $this->script;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <brief>Устанавливает значение свойства</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///       <arg name="value" brief="значение" />
///     </args>
///     <details>
///       <p>Свойства объекта доступны только на чтение.</p>
///     </details>
///     <body>
  public function __set($property, $value) { throw new Core_ReadOnlyObjectException($this); }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <brief>Проверяет установку значения свойства</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'options':
      case 'script':
        return true;
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset" returns="mixed">
///   <brief>Удаляет свойство</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function  __unset($property) { throw new Core_ReadOnlyObjectException($property); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="option_name_for" returns="string">
///     <brief>Возвращает имя по значению опции командной строки</brief>
///     <args>
///       <arg name="arg" type="string" brief="неразобранная опция" />
///     </args>
///     <body>
  protected function option_name_for($arg) {
    if ($m = Core_Regexps::match_with_results('{^(--[a-zA-Z][a-zA-Z0-9-]*)(?:=.*)?$}', $arg))
      return $m[1];
    elseif ($m = Core_Regexps::match_with_results('{^(-[a-zA-Z0-9]).*}', $arg))
      return $m[1];
    else
      return null;
  }
///     </body>
///   </method>

///   <method name="option_for" returns="mixed">
///     <brief>Возвращает описание опции по ее имени</brief>
///     <args>
///       <arg name="name" type="string" brief="имя опции" />
///     </args>
///     <body>
  protected function option_for($name) {
    foreach ($this->options as $option)
      if ($option->short == $name || $option->long == $name) return $option;

    return null;
  }
///     </body>
///   </method>

///   <method name="value_for" returns="mixed" access="private">
///     <brief>Формирует значение опции</brief>
///     <args>
///       <arg name="option" type="object" brief="опция" />
///       <arg name="arg"    type="string" brief="неразобранное значение опции" />
///     </args>
///     <body>
  private function value_for($option, $arg) {
    if ((($value = ($arg[1] == '-') ?
      Core_Regexps::replace("{^{$option->long}=?}", '', $arg) :
      Core_Regexps::replace("{^{$option->short}}",  '', $arg))) === '')
          $value = $option->value;

    settype($value, $option->type);

    return $value;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>
/// <composition>
///   <source class="CLI.GetOpt.Parser" role="parser" multiplicity="1" />
///   <target class="CLI.GetOpt.Option" role="options" multiplicity="0..*" />
/// </composition>

/// </module>
?>