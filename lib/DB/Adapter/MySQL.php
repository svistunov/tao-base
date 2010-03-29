<?php
/// <module name="DB.Adapter.MySQL" version="0.2.0" maintainer="timokhin@techart.ru">
///   <brief>MySQL адаптер</brief>

Core::load('DB.Adapter.PDO', 'Time');

/// <class name="DB.Adapter.MySQL" stereotype="module">
class DB_Adapter_MySQL implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.2.0';
///   </constants>
}
/// </class>

/// <class name="DB.Adapter.MySQL.Connection" extends="DB.Adapter.PDO.Connection">
///   <brief>Класс подключения к БД</brief>
class DB_Adapter_MySQL_Connection extends DB_Adapter_PDO_Connection {

///   <protocol name="processing">

///   <method name="prepare" returns="DB.Adapter.MySQL.Cursor">
///     <brief>Подготавливает SQL-запрос к выполнению</brief>
///     <args>
///       <arg name="sql" type="string" brief="sql-запрос" />
///     </args>
///     <body>
  public function prepare($sql) {
    try {
      return new DB_Adapter_MySQL_Cursor($this->pdo->prepare($sql));
    } catch (PDOException $e) {
      throw new DB_ConnectionException($e->getMessage());
    }
  }
///     </body>
///   </method>

///   <method name="cast_parameter" returns="mixed">
///     <brief>Преобразует значение в пригодный вид для вставки в sql запрос</brief>
///     <args>
///       <arg name="value" brief="значение" />
///     </args>
///     <body>
  public function cast_parameter($value) {
    if ($value instanceof Time_DateTime)
      return $value->format(Time::FMT_DEFAULT);
    else
      return $value;
  }
///     </body>
///   </method>

///   <method name="is_castable_parameter" returns="boolean">
///     <brief>Проверяет требуется ли преобразовывать значение</brief>
///     <args>
///       <arg name="value" brief="значение" />
///     </args>
///     <body>
  public function is_castable_parameter($value) {
    return ($value instanceof Time_DateTime);
  }
///     </body>
///   </method>

///   <method name="after_connect">
///     <brief>Вызывается в DB.Connection после соединения</brief>
///     <body>
  public function after_connect() {
    $this->pdo->exec('SET NAMES '.DB::option('charset'));
  }
///     </body>
///   </method>

///   <method name="explain">
///     <brief>Выполняет EXPLAIN для анализа запроса. Возвращает массив строк</brief>
///     <args>
///       <arg name="sql" type="string" brief="sql-запрос" />
///       <arg name="binds" type="array" brief="массив параметров" />
///     </args>
///     <body>
  public function explain($sql, $binds) {
  $c = $this->prepare("EXPLAIN ($sql)");
    $c->execute($binds);
    $result = array();
    foreach ($c->fetch_all() as $v)
      $result[] = Core::object($v);
    return $result;
  }
///     </body>
///   </method>
///   </protocol>
}
/// </class>


/// <class name="DB.Adapter.MySQL.Cursor">
///   <brief>Класс курсора БД</brief>
class DB_Adapter_MySQL_Cursor extends DB_Adapter_PDO_Cursor {
///   <protocol name="processing">

///   <method name="cast_column" returns="mixed">
///     <brief>Преобразует значение полученное из БД в нужный формат, для работы с ним в php</brief>
///     <args>
///       <arg name="metadata" type="DB.ColumnMeta" brief="мета-данный колонки" />
///       <arg name="value" brief="значение" />
///     </args>
///     <body>
  public function cast_column(DB_ColumnMeta $metadata, $value) {
      switch ($metadata->type) {
      case 'datetime':
      case 'timestamp':
      case 'date':
        return is_null($value) ? null : Time::DateTime($value);
      case 'boolean':
        return $value ? true : false;
      case 'longlong':
      case 'int24':
      case 'integer':
      case 'long':
      case 'tiny':
      case 'short':
        return (int) $value;
      case 'float':
      case 'double':
        return (float) $value;
      default:
        return $value;
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>