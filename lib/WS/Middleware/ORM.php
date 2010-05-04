<?php
/// <module name="WS.Middleware.ORM" version="0.2.2" maintainer="timokhin@techart.ru">
///   <brief>Сервис подключения к базе данных с использованием модуля DB.ORM</brief>
///   <details>
///     <p>Сервис обеспечивает все последующие сервисы деревом мапперов, обеспечивающих объектно-ориентированный
///        доступ к реляционной базе данных с использованием классов модуля DB.ORM.</p>
///     <p>Все последующие сервисы в цепочке обработки запроса могут получить доступ к корневому мапперу дерева с помощью объекта
///        окружения: <pre>$env->db</pre>. Строка DSN для подключения к серверу БД может быть указана непосредственно при создании объекта
///        сервиса, или загружена конфигурационным сервисом аналогично модулю WS.Middleware.DB.</p>
///     <p>Подразумевается, что в качестве корневого маппера используется объект класса DB.ORM.ConnectionMapper, поэтому модуль может
///        быть использован только в случаях использования одной базы данных и соответственно одного подключения. В случае, если дерево
///        мапперов использует несколько объектов подключения, реализация аналогичного модуля необходимо выполнить самостоятельно.</p>
///   </details>

Core::load('DB.ORM', 'WS');

/// <class name="WS.Middleware.ORM" stereotype="module">
///   <brief>Класс модуля</brief>
///   <implements interface="Core.ModuleInterface" />
class WS_Middleware_ORM implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.2.2';
///   </constants>

///   <protocol name="building">

///   <method name="Service" returns="WS.Middleware.ORM.Service" scope="class">
///     <brief>Создает объект класса WS.Middleware.ORM.Service</brief>
///     <args>
///       <arg name="application" type="WS.ServiceInterface" brief="объект приложения" />
///       <arg name="session" type="DB.ORM.ConnectionMapper" brief="корневой маппер дерева мапперов" />
///       <arg name="dsn" type="string" default="''" brief="строка DSN для подключения к базе данных" />
///     </args>
///     <body>
  static public function Service(WS_ServiceInterface  $application, DB_ORM_ConnectionMapper $session, $dsn = '') {
    return new WS_Middleware_ORM_Service($application, $session, $dsn);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="WS.Middleware.ORM.Service" extends="WS.MiddlewareService">
///   <brief>Сервис, обеспечивающий объектно-ориентированный интерфейс к реляционной базе данных</brief>
class WS_Middleware_ORM_Service extends WS_MiddlewareService {

  protected $session;
  protected $dsn;
  protected $logger;

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="application" type="WS.ServiceInterface" brief="объект приложения" />
///       <arg name="session" type="DB.ORM.ConnectionMapper" brief="корневой маппер дерева мапперов" />
///       <arg name="dsn" type="string" default="''" brief="строка DSN для подключения к базе данных" />
///     </args>
///     <body>
  public function __construct(WS_ServiceInterface $application, DB_ORM_ConnectionMapper $session, $dsn = '') {
    parent::__construct($application);
    $this->session = $session;
    $this->dsn = $dsn;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="logger" returns="WS.Middleware.ORM.Service">
///     <brief>Позволяет указать объект ведения протокола запросов</brief>
///     <args>
///       <arg name="logger" type="Dev.DB.Log.Logger" brief="объект ведения протокола" />
///     </args>
///     <body>
  public function logger(Dev_DB_Log_Logger $logger) {
   $this->logger = $logger;
   return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="run" returns="mixed">
///     <brief>Выполняет обработку запроса</brief>
///     <args>
///       <arg name="env" type="WS.Environment" brief="объект окружения" />
///     </args>
///     <body>
  public function run(WS_Environment $env) {
    $connection = DB::Connection($this->dsn ? $this->dsn : $env->config->db->dsn);

    if (isset($env->config) && $l = $env->config->db->log) {
      Core::load('Dev.DB.Log');
      $this->logger = Dev_DB_Log::Logger(IO_FS::FileStream($l, 'a'));
    }

    if ($this->logger) {
      $connection->listener($this->logger);
      $this->logger->write('url', $env->request->uri);
    }

    $env->db = $this->session->connect($connection);

    try {
      $result = $this->application->run($env);
    } catch (Exception $e) {
      $error = $e;
    }
    $connection->disconnect();

    if ($error) throw $error;

    return $result;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>
