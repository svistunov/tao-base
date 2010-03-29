<?php
/// <module name="CLI.Application" version="0.2.0" maintainer="timokhin@techart.ru">
///   <brief>Простейшая структура CLI-приложения</brief>
///   <details>
///     <p>Модуль определяет простейшую структуру CLI-приложения, обладающего следующей
///        функциональностью:</p>
///     <ul>
///       <li>поддержка набора опций командной строки, преобразование значений опций в конфигурацию
///           приложения;</li>
///       <li>поддержка стандартного ключа справки (-h) и вывод списка поддерживаемых опций;</li>
///       <li>выполнение основного кода приложения;</li>
///       <li>перехват исключений, вывод информации об ошибки в stderr, завершение работы
///           программы с аварийным статусом.</li>
///     </ul>
///     <p>Для создания приложения необходимо унаследовать свой класс приложения от абстрактного
///        базового класса CLI.Application.Base. Точкой входа в приложение является метод main()
///        класса приложения, однако пользовательский код имеет смысл размещать в методе run(),
///        который вызывается из main().</p>
///     <p>Поддерживаемые параметры командной строки и текст описания программы необходимо указать
///        в методе setup() с помощью соответствующих методов, не забыв при этом вызвать
///        родительский метод.</p>
///     <p>Обработка ошибок выполняется методов handle_error(), который по умолчанию выводит
///        сообщение об ошибке в stderr()  и завершает выполнение с аварийным статусом.</p>
///     <p>Как правило, класс приложения реализуется внутри запускаемого модуля, поддерживающего
///        интерфейс CLI_RunInterface. В этом случае создание экземпляра объекта приложения и вызов
///        его метода main() лучше всего выполнять внутри статического метода main() запускаемого
///        модуля.</p>
///     <code><![CDATA[
/// static public function main(array $args) { return self::Application()->main($args); }
///     ]]></code>
///   </details>
// TODO: CLI.Application.AbstractApplication -> CLI.Application.Base
Core::load('CLI', 'CLI.GetOpt', 'IO');

/// <class name="CLI.Application" stereotype="module">
///   <brief>Класс модуля</brief>
class CLI_Application implements Core_ModuleInterface {
///   <constants>
  const MODULE  = 'CLI.Application';
  const VERSION = '0.2.1';
///   </constants>
}
/// </class>


/// <class name="CLI.Application.Exception" extends="CLI.Exception" stereotype="exception">
///   <brief>Базовый класс исключений</brief>
class CLI_Application_Exception extends CLI_Exception {}
/// </class>


/// <class name="CLI.Application.Base" stereotype="abstract">
///   <brief>Базовый класс CLI-приложения</brief>
///   <details>
///     <p>Производные классы могут использовать следующие protected-свойства:</p>
///     <dl>
///       <dt>getopt</dt><dd>объект класса CLI.GetOpt.Parser, используемый для разбора опций
///                          командной строки;</dd>
///        <dt>options</dt><dd>массив значений установленных опций командной строки.</dd>
///     </dl>
///     <p>Для настройки опций командной строки и текста описания программы необходимо
///        переопределить метод setup(). Встроенная реализация добавляет в список опций стандартный
///        ключ -h, выводящий справочную информацию о поддерживаемых опциях, поэтому логично
///        вызывать родительскую реализацию в производном классе.</p>
///   </details>
abstract class CLI_Application_AbstractApplication {

  private $usage_text;

  protected $getopt;
  protected $options = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <details>
///       <p>Создает экземпляр парсера параметров командной строки и вызывает метод setup().</p>
///     </details>
///     <body>
  public function __construct() {
    $this->getopt = CLI_GetOpt::Parser();
    $this->setup();
  }
///     </body>
///   </method>

///   <method name="setup" returns="CLI.Application.AbstractApplication" access="protected">
///   <brief>Выполняет конфигурирование приложения.</brief>
///     <body>
  protected function setup() {
    return $this->options(array(
      array('show_help', '-h', '--help', 'boolean', true, 'Shows help message')),
      array('show_help' => false));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="usage_text" returns="CLI.Application.AbstractApplication" access="protected">
///   <brief>Устанавливает текст описания приложения.</brief>
///     <args>
///       <arg name="text" type="string" brief="текст" />
///     </args>
///     <details>
///       <p>Метод предназначен для использования внутри метода setup().</p>
///     </details>
///     <body>
  protected function usage_text($text) { $this->usage_text = $text; return $this; }
///     </body>
///   </method>

///   <method name="options" returns="CLI.Application.AbstractApplication" access="protected">
///   <brief>Описавает опции приложения и значения по умолчанию</brief>
///     <args>
///       <arg name="options"  type="array" brief="список опций" />
///       <arg name="defaults" type="array" default="array()" brief="список значений по умолчанию" />
///     </args>
///     <details>
///       <p>Метод предназначен для упрощения конфигурирования приложения в методе setup().</p>
///       <p>Список опций представляет собой массив, каждый элемент которого в свою очередь является
///          массивом, описывающим опций в формате, определенном в модуле CLI.GetOpt.</p>
///       <p>Список значений по умолчанию позволяет установить значения опций, которые будут
///          использоваться в случае отсутствия явного указания значения в командной строке.</p>
///     </details>
///     <body>
  protected function options(array $options, array $defaults = array()) {
    foreach ($options as $v)
      $this->getopt->option($v[0], $v[1], $v[2], $v[3], $v[4], $v[5]);
    $this->getopt->defaults($defaults);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run" returns="int" stereotype="abstract">
///   <brief>Выполняет пользовательскую логику приложения</brief>
///     <args>
///       <arg name="argv" type="array" />
///     </args>
///     <body>
  abstract public function run(array $argv);
///     </body>
///   </method>

///   <method name="main">
///   <brief>Точка входа приложения</brief>
///     <args>
///       <arg name="argv" type="array" brief="массив параметров командной строки" />
///     </args>
///     <details>
///       <p>Метод выполняет следующую последовательность действий:</p>
///       <ol>
///         <li>выполняет разбор опций командной строки и заполняет массив options;</li>
///         <li>если была передана опция -h, выводит информацию об использовании и
///             завершает выполнение;</li>
///         <li>вызывает метод run(), передавая ему список аргументов командной строки, оставшихся
///             после работы парсера;</li>
///         <li>если при выполнении run() не возникло исключений -- завершает работу с кодом,
///             который вернул метод run();</li>
///         <li>если возникло исключение -- обрабатывает его путем вызова метода
///             handle_error().</li>
///       </ol>
///     </details>
///     <body>
  public function main(array $argv) {
    try {
      $this->options = $this->getopt->parse($argv);
      return $this->shutdown($this->options['show_help'] ? $this->show_usage() : $this->run($argv));
    } catch(Exception $e) {
      return $this->shutdown($this->handle_error($e));
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="shutdown">
///     <brief>Завершает выполнение</brief>
///     <args>
///       <arg name="status" type="int" />
///     </args>
///     <body>
  protected function shutdown($status) {
    exit((int) $status);
  }
///     </body>
///   </method>

///   <method name="show_usage" access="protected" returns="int">
///     <brief>Выводит в stdout текст с описанием программы</brief>
///     <details>
///       <p>Выводимый текст содержит собственно текст описания и список поддерживаемых опций
///          командной строки с описанием каждой опции.</p>
///     </details>
///    <body>
  protected function show_usage() {
    IO::stdout()->
      write($this->usage_text)->
      write(CLI_GetOpt::usage_text($this->getopt));
    return 0;
  }
///     </body>
///   </method>

///   <method name="handle_error" access="protected">
///     <brief>Выполняет обработку ошибок</brief>
///     <args>
///       <arg name="e" type="Exception" />
///     </args>
///     <details>
///       <p>Метод вызывается в случае генерации исключения. Реализация по умолчанию выводит текст
///         исключения в stderr и завершает программу со статусом завершения -1.</p>
///     </details>
///     <body>
  protected function handle_error(Exception $e) {
    try {
      IO::stderr()->format("Error: %s\n", $e->getMessage());
      return -1;
    } catch (Exception $e) {}
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>
/// <composition>
///   <source class="CLI.Application.AbstractApplication" role="application" multiplicity="1" />
///   <target class="CLI.GetOpt.Parser" role="options" multiplicity="1" />
/// </composition>

/// </module>
?>