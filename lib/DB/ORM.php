<?php
/// <module name="DB.ORM" version="0.2.1" maintainer="timokhin@techart.ru ">
///   <brief>Объектно-ориентированный интерфейс к реляционной базе данных</brief>
Core::load('DB.ORM.SQL', 'DB', 'Data.Pagination', 'Validation', 'Object');

/// <class name="DB.ORM" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
///   <brief>Модуль DB.ORM</brief>
class DB_ORM implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.2.1';
///   </constants>

///   <protocol name="building">

///   <method name="MapperSet" returns="DB.ORM.MapperSet" scope="class">
///     <body>
  static public function MapperSet(DB_ORM_Mapper $parent = null) {
    return new DB_ORM_MapperSet($parent);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>


/// <class name="DB.ORM.Exception" extends="Core.Exception" stereotype="exception">
///   <brief>Базовый класс исключений</brief>
class DB_ORM_Exception extends Core_Exception {}
/// </class>


/// <interface name="DB.ORM.ImmutableMapperInterface">
interface DB_ORM_ImmutableMapperInterface {

///   <protocol name="configuring">

///   <method name="immutable">
///     <body>
  public function immutable();
///     </body>
///   </method>

///   </protocol>
}
/// </interface>


/// <class name="DB.ORM.Mapper" stereotype="abstract">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="Core.CallInterface" />
///   <brief>Базовый класс маппера</brief>
///   <details>
///    <p>Базовый класс DB.ORM.Mapper определяет набор основных свойств, присущих всем мапперам.</p>
///    <p>Он определяет три основные операции: pmap (property map) и cmap (call map), позволяющие реализовывать
///       динамическое вычисление значений свойств и и результата вызовов методов объектов, а также операцию spawn(),
///       порождающую дочерний маппер.</p>
///    <p>Операция pmap, определяемая реализацией защищенного метода pmap, отвечает за вычисление значений динамических
///       свойств маппера. При обращении к свойству  $mapper->property вычисление организуется следующим образом:</p>
///    <ol>
///      <li>Если определен метод pmap_{property}, возвращается результат выполнения этого метода;</li>
///      <li>Если определен метод map_{property},  возвращается результат выполнения этого метода;</li>
///      <li>Если определено значение поля $this->{property}, возвращается значение этого свойства;</li>
///      <li>Возвращается значение свойства property родительского объекта, или null, если родительский объект не
///          определен.</li>
///    </ol>
///    <p>Таким образом, можно динамически переопределять свойства объекта, при этом свойства наследуются дочерними
///       элементами дерева от родительских.</p>
///    <p>Наследование свойств может быть очень полезным. Например, если мы сохраним объект класса DB.Connection в поле
///       корневого элемента дерева, то каждый маппер сможет получить значение этого поля как $this->connection. Таким
///       образом, каждый маппер может обратиться к базе данных, не задумываясь о своей родительской цепочке
///       мапперов.</p>
///    <p>Предположим теперь, что у нас есть две базы и два соединения. В этом случае, мы можем убрать свойство
///       connection из корневого элемента и сделать два дочерних маппера, создав свойство connection у каждого из них.
///       Для дочерних мапперов ничего не изменится -- они будут по-прежнему обращаться к свойству $this->connection.</p>
///    <p>Корневой элемент дерева мапперов, не имеющий родителей, доступен в любом маппере как свойство session.
///       Это название не случайно, так как корневой элемент может хранить общую информацию, специфичную для текущего
///       сеанса работы с базой, предоставляя ее дочерним мапперам. В частности, таким образом может храниться
///       информация о текущем авторизованном пользователе приложения.</p>
///    <p>Также всегда доступно свойство parent, возвращающее ссылку на родительский объект маппера.</p>
///    <p>Операция cmap, определяемая реализацией защищенного метода cmap, отвечает за диспетчеризацию вызовов методов
///       маппера. При обращении к методу маппера $mapper->method() вычисление организуется следующим образом:</p>
///       <ol>
///         <li>Если определен метод cmap_{method}, возвращается результат выполнения этого метода;</li>
///         <li>Если определен метод map_{method},  возвращается результат выполнения этого метода;</li>
///         <li>Генерируется исключение Core.MissingMethodException, то есть родительскому мапперу метод не
///             делегируется.</li>
///       </ol>
///    <p>И, наконец, операция spawn, реализуемая одноименным публичным методом, позволяет мапперу порождать дочерние
///       мапперы того же типа, что и родительский маппер. То есть, если у нас есть маппер $m класса StoryMapper, вызов
///       $m->spawn() вернет маппер класса StoryMapper, поле parent которого будет указывать на маппер $m.</p>
///    <p>Объекты класса поддерживают следующие обязательные свойства:</p>
///    <dl>
///      <dt>parent</dt><dd>ссылка на родительский маппер;</dd>
///      <dt>session</dt><dd>ссылка на корневой маппер дерева.</dd>
///    </dl>
///   </details>
abstract class DB_ORM_Mapper
  implements Core_PropertyAccessInterface,
             Core_CallInterface {

  protected $parent;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="parent" default="null" brief="родительский маппер" />
///     </args>
///     <brief>Конструктор</brief>
///     <body>
  public function __construct($parent = null) {  if ($parent) $this->child_of($parent); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="spawn" returns="DB.ORM.Mapper">
///     <brief>Порождает дочерний маппер</brief>
///     <details>
///       <p>Класс порождаемого маппера  совпадает с классом маппера, для которого вызывается метод.</p>
///     </details>
///     <body>
  public function spawn() { return Core::make(Core_Types::class_name_for($this), $this); }
///     </body>
///   </method>

///   <method name="downto" returns="mixed">
///     <args>
///       <arg name="path" type="string" />
///     </args>
///     <body>
  public function downto($path) {
    $r = $this;
    foreach (explode('/', $path) as $s) {
      switch (true) {
        case $s && is_numeric($s):
          $r = $r[$s];
          break;
        case $s && isset($r->$s):
          $r = $r->$s;
          break;
        default:
          return null;
      }
    }
    return $r;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="mapping">

///   <method name="can_cmap" returns="string">
///     <brief>Определяет, реализует ли данный маппер отображение метода $method</brief>
///     <args>
///       <arg name="method" type="string" brief="имя метода" />
///     </args>
///     <details>
///       <p>Если отображение поддерживается, возвращается имя метода, выполняющего отображение, в противном случае
///          возвращается false.</p>
///     </details>
///     <body>
  public function can_cmap($method) {
    return method_exists($this,    "cmap_$method") ? "cmap_$method" :
             (method_exists($this, "map_$method")  ? "map_$method"  : $this->__can_map($method, true));
  }
///     </body>
///   </method>

///   <method name="can_pmap" returns="string">
///     <brief>Определяет, реализует ли данный маппер отображение свойства $property</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Если отображение поддерживается, возвращается имя метода, выполняющего отображение, в противном случае
///          возвращается false.</p>
///     </details>
///     <body>
  public function can_pmap($property) {
    return method_exists($this,    "pmap_$property") ? "pmap_$property" :
             (method_exists($this, "map_$property")  ? "map_$property"  : $this->__can_map($property, false));
  }
///     </body>
///   </method>

///   <method name="can_map" returns="string" access="protected">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="is_call" type="boolean" default="false" />
///     </args>
///     <body>
  protected function __can_map($name, $is_call = false) { return false; }
///     </body>
///   </method>

///   <method name="__map" returns="mixed" access="protected">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="args" type="null|array" default="null" />
///     </args>
///     <body>
  protected function __map($name, $args = null) { return null; }
///     </body>
///   </method>

///   <method name="pmap" returns="mixed" access="protected">
///     <brief>Выполняет отображение свойства маппера</brief>
///     <args>
///       <arg name="name"    type="string" brief="имя свойства" />
///       <args name="method" type="string" brief="имя метода, выполняющего отображение свойства" />
///     </args>
///     <details>
///       <p>Гарантируется, что метод $method существует. Реализация в базовом классе просто возврашает результат
///          вызова этого метода.</p>
///     </details>
///     <body>
  protected function pmap($name, $method) { return is_string($method) ? $this->$method() : $this->__map($name); }
///     </body>
///   </method>

///   <method name="cmap" returns="mixed" access="protected">
///     <brief>Выполняет отображение вызова метода маппера</brief>
///     <args>
///       <arg name="name"   type="string" brief="имя отображаемого метода" />
///       <arg name="method" type="string" brief="имя метода, выполняющего отображение" />
///       <arg name="args"   type="array"  brief="аргументы вызываемого метода" />
///     </args>
///     <details>
///       <p>Гарантируется, что метод $method существует. Реализация в базовом классе просто возвращает результат
///          вызова этого метода.</p>
///     </details>
///     <body>
  protected function cmap($name, $method, array $args) { return is_string($method) ? call_user_func_array(array($this, $method), $args) : $this->__map($name, $args); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing" interface="Core.PropertyAccessInterface">

///   <method name="__get" returns="mixed">
///     <brief>возвращает значение свойства</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Поддерживаются стандартные свойства parent и session, а также свойства, вычисляемые с помощью операции
///          pmap.</p>
///     </details>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'parent':
        return $this->$property;
      case 'db':
      case 'session':
        return isset($this->parent) ? $this->parent->session : $this;
      default:
        if ($m  = $this->can_pmap($property))
          return $this->pmap($property, $m);
        else
          return (isset($this->$property)) ?
            $this->$property :
            (isset($this->parent) ? $this->parent->$property : null);
    }
  }
///     </body>
///   </method>

///   <method name="__set">
///     <brief>устанавливает значение свойства</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///       <arg name="value"  brief="устанавливаемое значение" />
///     </args>
///     <details>
///       <p>Все поддерживаемые свойства доступны только для чтения.</p>
///    </details>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'parent':
        $this->child_of($value);
        return $this;
      case 'db':
      case 'session':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw $this->can_pmap($property) ?
          new Core_ReadOnlyPropertyException($property) :
          new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <details>Проверяет наличие свойства</details>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Результат проверки существования динамических свойств определяется результатом выполнения метода
///          can_pmap()</p>
///     </details>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'parent':
      case 'db':
      case 'session':
        return true;
      default:
        return (boolean) $this->can_pmap($property);
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <brief>Удаляет свойство</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Ни одно поддерживаемое свойство не может быть удалено</p>
///     </details>
///     <body>
  public function __unset($property) {
    switch ($property) {
      case 'parent':
        unset($this->parent);
        break;
      case 'db':
      case 'session':
        throw new Core_UndestroyablePropertyException($property);
      default:
        throw $this->can_pmap($property) ?
          new Core_UndestroyablePropertyException($property) :
          new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="mixed">
///     <brief>Выполняет диспетчеризацию динамических вызовов</brief>
///     <args>
///       <arg name="method" type="string" brief="имя вызываемого метода" />
///       <arg name="args"   type="array" brief="список аргументов" />
///     </args>
///     <details>
///       <p>Выполнение испетчеризации определяется реализацией операции cmap.</p>
///     </details>
///     <body>
  public function __call($method, $args) {
    if ($m = $this->can_cmap($method))
      return $this->cmap($method, $m, $args);
    else
      throw new Core_MissingMethodException($method);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="child_of" returns="DB.ORM.Mapper" access="private">
///     <brief>Устанавливает значение ссылки на родителький маппер</brief>
///     <args>
///       <arg name="parent" type="DB.ORM.Mapper" brief="родительский маппер" />
///     </args>
///     <details>
///       <p>Метод гарантирует проверку типа передаваемого родительского объекта.</p>
///     </details>
///     <body>
  private function child_of(DB_ORM_Mapper $parent) {
    $this->parent = $parent;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="DB.ORM.MappingOptions">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="Core.IndexedAccessInterface" />
///   <brief>Коллекция значений различных опций, определяющих поведение маппера</brief>
///   <details>
///     <p>Основное назначение объектов этого класса -- хранение параметров формирования SQL запросов в SQL
///        мапперах. Класс предназначен для использования внутри модуля и вряд ли будет полезен в пользовательском
///        приложении.</p>
///     <p>Опции преобразования также образуют древовидную структуру: объект опций может быть дочерним по отношению к
///        родительскому объекту опций, при этом его значения опций могут вычисляться с учетом значения аналогичных
///        опций родительского объекта.</p>
///     <p>Опции могут иметь различную структуру, при этом хотелось бы обеспечить легкое формирование значений опций
///        наряду с единообразной схемой их хранения внутри коллекции. Поэтому для формирования значений используются
///        набор специальный набор методов, а для чтения -- доступ к индексируемым свойствам коллекции. Во избежание
///        ошибок формирования значений свойств установка значений через индексируемые свойства не поддерживается.</p>
///     <p>На данный момент поддерживаются следующие свойства:</p>
///     <dl>
///       <dt>classname</dt><dd>Имя класса, используемого для создания экземпляров результирующих объектов выборки</dd>
///       <dt>validator</dt><dd>Объект-валидатор, используемый для проверки сохраняемого объекта при update и insert</dd>
///       <dt>table</dt><dd>Имя таблицы БД</dd>
///       <dt>columns</dt><dd>Список колонок таблицы. На данный момент информация о типах не используется.</dd>
///       <dt>exclude</dt><dd>Список колонок, которые должны быть исключены из результатов выборки</dd>
///       <dt>key</dt><dd>Список колонок, образующих первичный ключ</dd>
///       <dt>explicit_key</dt>
///         <dd>По умолчанию, если первичный ключ содержит одну колонку, считается, что она
///             заполняется автоматически. Использование этой опции отключает эту настройку</dd>
///       <dt>calculate</dt><dd>Список вычисляемых полей, которые должны быть включены в результирующую выборку</dd>
///       <dt>having</dt><dd>Набор критериев для построения условия having</dd>
///       <dt>where</dt><dd>Набор критериев для построения условия where</dd>
///       <dt>order_by</dt><dd>Выражение для построения order by</dd>
///       <dt>group_by</dt><dd>Выражение для построения group by</dd>
///       <dt>range</dt><dd>Диапазон строк выборки (limit/offset)</dd>
///       <dt>index</dt><dd>Имя индекса, используемого при выполнении операции SELECT</dd>
///     </dl>
///   </details>
class DB_ORM_MappingOptions
  implements Core_PropertyAccessInterface,
             Core_IndexedAccessInterface {

  protected $parent;
  protected $options = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="mapper" type="DB.ORM.Mapper" brief="Объект-маппер, которому принадлежит коллекция" />
///     </args>
///     <details>
///       <p>Не смотря на то, что в качестве параметра в конструктор передается объект-маппер, сохраняемым в свойстве
///          родительским объектом является коллекция опций родительского объекта. При этом, если у непосредственного
///          родителя такого свойства нет, он делегирует вычисление свойства своему родителю и т.д. Непосредственная
///          ссылка на родительский маппер не сохраняется с целью избежать циклических ссылок, с которыми не умеет
///          работать сборщик мусора.</p>
///     </details>
///     <body>
  public function __construct(DB_ORM_Mapper $mapper) {
    $parent = isset($mapper->parent) ? $mapper->parent->options : null;
    $this->parent = ($parent instanceof DB_ORM_MappingOptions) ? $parent : null;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="mixed">
///     <brief>Обработка вызовов методов установки значений опций</brief>
///     <args>
///       <arg name="method" type="string" brief="имя вызываемого метода" />
///       <arg name="args"   type="array"  brief="список аргументов вызваемого метода" />
///     </args>
///     <details>
///       <p>Класс не предназначен для использования в клиентских программах, поэтому просто перечислим методы
///          установки значений опций. Более подробная информация приведена в описании класса DB.ORM.SQLMapper</p>
///       <dl>
///         <dt>classname($name)</dt><dd>имя классов результирующих объектов выборки</dd>
///         <dt>validator($validator)</dt><dd>объект валидации для сохраняемых объектов</dd>
///         <dt>table($name)</dt><dd>имя таблицы</dd>
///         <dt>columns(array $names)</dt>список колонок таблицы<dd></dd>
///         <dt>exclude(array $names)</dt><dd>список колонок, которые необходимо исключить из результата запроса</dd>
///         <dt>key(array $names)</dt><dd>список колонок, формирующих первичный ключ</dd>
///         <dt>explicit_key($boolean_flag)</dt><dd>признак, отменяющий автоматическое формирование первичного ключа из
///             одной колонки</dd>
///         <dt>calculate(array $columns)</dt><dd>список вычисляемых полей, включаемых в результат запроса</dd>
///         <dt>having($condition)</dt><dd>выражение для формирования условия having</dd>
///         <dt>where($condition)</dt><dd>выражение для формирование условия where</dd>
///         <dt>order_by($expression)</dt><dd>выражение для формирования условия order_by</dd>
///         <dt>group_by($expression)</dt><dd>выражение для формирования условия group_by</dd>
///         <dt>join($type, $table, $expr)</dt><dd>описание join типа $type с таблицей $table и условием $expr</dd>
///         <dt>range($limit, $offset)</dt><dd>описание диапазона записей результата</dd>
///         <dt>index($name)</dt><dd>имя индекса, используемого при выполнении выборки</dd>
///       </dl>
///     </details>
////    <body>
  public function __call($method, $args) {
    switch ($method) {
      case 'classname':
        return $this->option($method, (string) $args[0]);
      case 'column':
        $this->array_option('columns', (string) $args[0]);
        if (isset($args[1])) $this->array_option('defaults', $args[1], $args[0]);
        return $this;
      case 'validator':
        return $this->use_validator($args[0]);
      case 'table':
        return $this->option($method, explode(' ', (string) $args[0]));
      case 'columns':
      case 'exclude':
      case 'key':
        foreach ((array) $args[0] as $v) $this->array_option($method, (string) $v);
        return $this;
      case 'defaults':
        foreach ((array) $args[0] as $k => $v) $this->array_option($method, $v, $k);
        return $this;
      case 'lookup_by':
        return $this->option($method, (string) $args[0]);
      case 'explicit_key':
        return $this->option($method, (boolean) $args[0]);
      case 'calculate':
        foreach ((array) $args[0] as $k => $v)
          $this->array_option($method, $v,
            is_int($k) ?
              (preg_match('{[0-9a-zA-Z]+\.([0-9a-zA-Z]+)}', $v, $m) ? $m[1] : $v) :
              $k);
        return $this;
      case 'having':
      case 'where':
        return $this->array_option($method, (string) $args[0]);
      case 'order_by':
      case 'group_by':
        return $this->option($method, (string) $args[0]);
      case 'join':
        return $this->array_option('join', array((string) $args[0], (string) $args[1], (array) $args[2]));
      case 'range':
        return $this->option('range', array((int) $args[0], isset($args[1]) ? (int) $args[1] : 0));
      case 'index':
        return $this->option('index', (string) $args[0]);
      default:
        throw new Core_MissingMethodException($method);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing">

///   <method name="offsetGet" returns="mixed">
///     <brief>возвращает значение опции</brief>
///     <args>
///       <arg name="index" type="string" brief="имя опции" />
///     </args>
///     <body>
///     <details>
///       <p>Помимо опций, перечисленных в описании метода __call, достпны еще две опции, значения которыъ вычисляются
///          на основании значений других опций:</p>
///       <dl>
///         <dt>result</dt><dd>список всех колонок результирующего набора</dd>
///         <dt>aliased_table</dt><dd>имя исходной таблицы вместе с ее псевдонимом</dd>
///       </dl>
///     </details>
  public function offsetGet($index) {
    $parent = $this->parent;
    switch ($index) {
      case 'table':
      case 'classname':
      case 'validator':
      case 'order_by':
      case 'group_by':
      case 'key':
      case 'explicit_key':
      case 'range':
      case 'index':
      case 'lookup_by':
        return $this->has_option($index) ?
          $this->options[$index] :
          ($parent ? $parent[$index] : null);
      case 'aliased_table':
        return implode(' ', $this['table']);
      case 'table_prefix':
        return isset($this->options['table']) ?
          (isset($this->options['table'][1]) ? $this->options['table'][1] : $this->options['table'][0]) :
          ($parent ? $parent[$index] : null);
      case 'defaults':
      case 'columns':
      case 'join':
      case 'where':
      case 'having':
      case 'calculate':
      case 'exclude':
        return $parent ?
          array_merge($parent[$index],
            $this->has_option($index) ? $this->options[$index] : array()) :
            ($this->has_option($index) ? $this->options[$index] : array());
      case 'result':
        $r = array();
        $t = $this['table'];
        foreach ($this['columns'] as $c)
          $r[$c] = (isset($t[1]) ? $t[1] : $t[0]).'.'.$c;
        foreach ($this['calculate'] as $k => $v) $r[$k] = $v;
        foreach ($this['exclude'] as $c) unset($r[$c]);
        return $r;
      default:
        throw new Core_MissingIndexedPropertyException($index);
    }
  }
///     </body>
///   </method>

///   <method name="offsetSet">
///     <brief>Запрещает установку каких-либо опций через интерфейс индексированного доступа</brief>
///     <args>
///       <arg name="index" type="string" brief="имя опции" />
///       <arg name="value" type="string" brief="значение опции" />
///     </args>
///     <details>
///       <p>Для установки свойств опций необходимо использовать соответствующие методы</p>
///     </details>
///     <body>
  public function offsetSet($index, $value) {
    switch ($index) {
      case 'table':
      case 'classname':
      case 'validator':
      case 'table':
      case 'classname':
      case 'columns':
      case 'defaults':
      case 'order_by':
      case 'group_by':
      case 'key':
      case 'explicit_key':
      case 'range':
      case 'index':
      case 'join':
      case 'where':
      case 'having':
      case 'calculate':
      case 'exclude':
      case 'aliased_table':
      case 'table_prefix':
      case 'result':
      case 'lookup_by':
        throw new Core_ReadOnlyIndexedPropertyException($index);
      default:
        throw new Core_MissingIndexedPropertyException($index);
    }
  }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <brief>Проверяет факт установки значения опции</brief>
///     <args>
///       <arg name="index" type="string" brief="имя опции" />
///     </args>
///     <details>
///       <p>В проверке участвуют также опции родительских объектов</p>
///     </details>
///     <body>
  public function offsetExists($index) {
    switch ($index) {
      case 'table':
      case 'classname':
      case 'validator':
      case 'columns':
      case 'defaults':
      case 'order_by':
      case 'group_by':
      case 'key':
      case 'explicit_key':
      case 'range':
      case 'index':
      case 'join':
      case 'where':
      case 'having':
      case 'calculate':
      case 'exclude':
      case 'lookup_by':
        return $this->has_option($index) || ($this->parent && isset($this->parent[$index]));
      case 'aliased_table':
      case 'table_prefix':
        return isset($this['table']);
      case 'result':
        return isset($this['columns']) || isset($this['calculate']);
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="offsetUnset">
///     <brief>Удаляет значение опции</brief>
///     <args>
///       <arg name="index" type="string" brief="имя опции" />
///     </args>
///     <details>
///       <p>Позволяет удалить значение любой опции, кроме aliased_table и result. Удаляются только опции,
///          принадлежащие данному объекту, соответствующие опции родительского объекта не учитываются.</p>
///     </details>
///     <body>
  public function offsetUnset($index) {
    switch ($index) {
      case 'table':
      case 'classname':
      case 'validator':
      case 'columns':
      case 'defaults':
      case 'order_by':
      case 'group_by':
      case 'key':
      case 'explicit_key':
      case 'range':
      case 'index':
      case 'join':
      case 'where':
      case 'having':
      case 'calculate':
      case 'exclude':
      case 'lookup_by':
        unset($this->options[$index]);
        break;
      case 'aliased_table':
      case 'table_prefix':
      case 'result':
        throw new Core_UndestroyableIndexedPropertyException($index);
      default:
        throw new Core_MissingIndexedPropertyException($index);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="has_option" returns="boolean" access="protected">
///     <brief>Возвращает признак наличия опции в данном объекте без учета опций родителького объекта</brief>
///     <args>
///       <arg name="name" type="string" brief="имя опции" />
///     </args>
///     <body>
///     <details>
///       <p>Этот вызов аналогичен __isset, но работает только с массивом опций данного объекта</p>
///     </details>
  protected function has_option($name) { return array_key_exists($name, $this->options); }
///     </body>
///   </method>

///   <method name="option">
///     <brief>Устанавливает значение опции</brief>
///     <args>
///       <arg name="name" type="string" brief="имя опции" />
///       <arg name="value" brief="значение опции" />
///     </args>
///     <body>
  protected function option($name, $value) {
    $this->options[$name] = $value;
    return $this;
  }
///     </body>
///   </method>

///   <method name="array_option" returns="DB.ORM.MapperOptions" access="protected">
///     <brief>Уставливает значение для опции, допускающей набор значений</brief>
///     <args>
///       <arg name="name" type="string" brief="имя опции" />
///       <arg name="value" brief="значение опции "/>
///       <arg name="idx" type="int" default="null" brief="индекс опции" />
///     </args>
///     <details>
///       <p>Если индекс опции не указан, очередное значение просто добавляется в массив значений опции</p>
///     </details>
///     <body>
  protected function array_option($name, $value, $idx = null) {
    is_null($idx) ?
      $this->options[$name][]     = $value :
      $this->options[$name][$idx] = $value;
    return $this;
  }
///     </body>
///   </method>

///   <method name="use_validator" returns="DB.ORM.MapperOptions">
///     <brief>Устанавливает значение опции validator с проверкой типа аргумента</brief>
///     <args>
///       <arg name="validator" type="Validation.Validator" brief="объект-валидатор" />
///     </args>
///     <body>
  private function use_validator(Validation_Validator $validator) { return $this->option('validator', $validator); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">

///   <method name="get_parent" returns="DB.ORM.MapperOptions">
///     <brief>Возвращает родительский объект опций</brief>
///     <body>
  public function get_parent_() {
    $options = isset($this->mapper->parent) ? $this->mapper->parent->options : null;
    return ($options instanceof DB_ORM_MappingOptions) ? $options : null;

  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="accessing" interface="Core.PropertyAccessInterface">

///   <method name="__get" returns="mixed">
///     <brief>Возвращает значение свойства объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Поддерживаются следующие свойства:</p>
///       <dl>
///         <dt>options</dt><dd>массив значений опций</dd>
///         <dt>parent</dt><dd>родительская коллекция опций</dd>
///       </dl>
///       <p>Важно помнить, что родительским объектом является коллекция опций, а не маппер!</p>
///     </details>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'options':
      case 'parent':
        return $this->$property;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <brief>Запрещает изменение значений свойств объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///       <arg name="value" brief="значение свойства" />
///     </args>
///     <details>
///       <p>Значение свойства parent не может быть изменено после создания объекта, для изменения значений опций
///          необходимо использовать соответствующие методы.</p>
///     </details>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'parent':
      case 'options':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__isset" returns="boolean">
///     <brief>Проверяет, установлено ли свойство объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства"/>
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'options':
      case 'parent':
        return isset($this->$property);
      default:
        return false;
    }
  }
///     </body>
///   </method>

///   <method name="__unset">
///     <brief>Запрещает удаление свойств объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __unset($property) {
    switch ($property) {
      case 'parent':
      case 'options':
        throw new Core_UndestroyablePropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="DB.ORM.SQLMapper" extends="DB.ORM.Mapper">
///   <implements interface="IteratorAggregate" />
///   <implements interface="Core.IndexedAccessInterface" />
///   <implements interface="Core.CallInterface" />
///   <brief>SQL-маппер</brief>
///   <details>
///     <p>Объекты этого класса обеспечивают связь между объектами бизнес-логики и таблицами реляционной базы, реализуя
///        в сильно упрощенном виде паттерн data mapper.</p>
///     <p></p>
///   </details>
class DB_ORM_SQLMapper extends DB_ORM_Mapper
  implements IteratorAggregate,
             Core_IndexedAccessInterface,
             Core_CountInterface,
             DB_ORM_ImmutableMapperInterface {

  protected $options;

  protected $mode = '';

  protected $is_immutable = false;

  protected $cache = array();
  protected $binds = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="parent" default="null" brief="родительский маппер" />
///     </args>
///     <body>
  public function __construct($parent = null) {
    parent::__construct($parent);

    $this->options = new DB_ORM_MappingOptions($this);
    if (!($this->parent && (get_class($this) == get_class($this->parent)))) $this->setup();
  }
///     </body>
///   </method>

///   <method name="setup" access="protected">
///     <brief>Внутренний метод инициализации</brief>
///     <details>
///       <p>Метод предназначен для настройки необходимых опций при создании экземпляра объекта.</p>
///       <p>Внимание! Если класс маппера совпадает с классом родительского маппера, метод вызван не будет, так как
///          значения соответствующих опций и так будут наследоваться от родительского маппера. В противном случае
///          выполнение операции spawn приводило бы к дублированию соответствующих опций.</p>
///     </details>
///     <body>
  protected function setup() {}
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="mode" returns="DB.ORM.SQLMapper">
///     <args>
///     </args>
///     <body>
  public function mode($mode) {
    $this->mode = (string) $mode;
    return $this;
  }
///     </body>
///   </method>

///   <method name="immutable" returns="DB.ORM.SQLMapper">
///     <brief>Переводит объект в неизменяемое состояние</brief>
///     <details>
///       <p>Объект, находящийся в неизменяемом состоянии</p>
///     </details>
///     <body>
  public function immutable() {
    $this->is_immutable = true;
    return $this;
  }
///     </body>
///   </method>

///   <method name="preload" returns="DB.ORM.SQLMapper">
///     <brief>Загружает все записи таблицы в кеш маппера</brief>
///     <details>
///       <p>Этот метод может быть полезен для мапперов справочных таблиц, содержащих небольшое количество строк,
///          на которые ссылается большое количество объектов других типов.</p>
///     </details>
///     <body>
  public function preload() {
    $keys = $this->options['key'];
    $composite = count($keys) > 1;

    foreach ($this->select() as $item) {
      if ($composite) {
        $ids = array();
        foreach ($keys as $v) $ids[] = $row[$v];
        $id = implode(',', $ids);
      } else $id = $item[$keys[0]];
      $this->cache($id, $item);
    }
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="make_entity" returns="mixed">
///     <brief>Создает объект сущности класса, имя которого указано в опции classname</brief>
///     <details>
///       <p>В случае, если соответствующая опция не указана, возвращается пустой объект типа stdClass.</p>
///     </details>
///     <body>
  public function make_entity() {
    $args = func_get_args();
    $entity = isset($this->options['classname']) ?
      Core::amake($this->options['classname'], $args) :
      Core::object();

    $array_access = ($entity instanceof ArrayAccess);

    foreach ($this->options['defaults'] as $k => $v)
      if ($array_access) $entity[$k] = $v;
      else               $entity->$k = $v;

    return $entity;
  }
///     </body>
///   </method>

///   <method name="query" returns="DB.Cursor">
///     <brief>Создает курсор для SELECT-запроса маппера и выполняет запрос</brief>
///     <details>
///       <p>Метод строит SQL-выражение SELECT в соответствии с опциями маппера и родительских мапперов, создает
///          соответствующий объект курсора класса DB.Cursor и выполняет запрос, не производя выборку результата.</p>
///     </details>
///     <body>
  public function query($execute = true) {
    $c = $this->make_cursor($this->sql()->select($this->mode))->
      bind($this->__get('binds'));
    $this->mode = '';
    return $execute ? $c->execute() : $c;
  }
///     </body>
///   </method>

///   <method name="select" returns="mixed">
///     <brief>Выполняет SELECT-запрос и выбирает все строки результирующего набора</brief>
///     <details>
///       <p>Метод строит SQL-выражение SELECT в соответствии с опцими маппера и родительских мапперов, выполняет запрос
///          и производит выборку результатов.</p>
///       <p>Например:</p>
///       <pre>
///         $stories = $db->news->stories->select();
///       </pre>
///     </details>
///     <body>
  public function select() { return $this->query()->fetch_all(); }
///     </body>
///   </method>

///   <method name="select_first" returns="mixed">
///     <brief>Выполняет SELECT-запрос и выбирает первую строку результирующего выражения</brief>
///     <details>
///       <p>Метод строит SQL-выражение SELECT в соответствии с опцими маппера и родительских мапперов, выполняет запрос
///          и производит выборку результатов.</p>
///     </details>
///     <body>
  public function select_first() { return $this->query()->fetch(); }
///     </body>
///   </method>

///   <method name="select_for" returns="mixed">
///     <brief>Выполняет SQL-запрос с дополнительным WHERE-условием и выбирает все строки результирующего набора</brief>
///     <details>
///       <p>Аналог метода select(), позволяющий задать дополнительное WHERE-условие без создания дочернего маппера.</p>
///       <p>Первый аргумент метода должен содержать выражение  с условием выборки, остальные -- значения параметров</p>
///       <p>Например:</p>
///       <pre><![CDATA[
///         $published_stories = $db->news->stories->select_for('pub_date < :date', Time::now());
///       ]]></pre>
///     </details>
///     <body>
  public function select_for() {
    $args = func_get_args();
    list($m, $this->mode) = array($this->mode, '');
    return $this->spawn()->
      where(array_shift($args), Core::normalize_args($args))->
      mode($m)->
      select();
  }
///     </body>
///   </method>

///  <method name="select_first_for" returns="mixed">
///     <brief>Выполняет SELECT-запрос c дополнительным WHERE-условием и выбирает первую строку результата</brief>
///     <details>
///       <p>Полный аналог метода select_for(), но с возможностью указания дополнительного WHERE-условия без создания
///          дочернего маппера</p>
///       <p>Например:</p>
///       <pre><![CDATA[
///         $last_story = $db->news->stories->select_first_for('pub_date < : date', Time::now());
///       ]]></pre>
///     </details>
///     <body>
  public function select_first_for() {
    $args = func_get_args();
    list($m, $this->mode) = array($this->mode, '');
    return $this->spawn()->
      where(array_shift($args), Core::normalize_args($args))->
      mode($m)->
      select_first();
  }
///    </body>
///  </method>

///   <method name="stat" returns="int">
///     <body>
  public function stat($just_count = false) {
    list($m, $this->mode) = array($this->mode, '');
    return Core::with_index($this->make_cursor($this->sql()->stat($just_count, $m), false)->
      bind($this->__get('binds'))->
        execute()->
        fetch(), 'count');
  }
///     </body>
///   </method>

///   <method name="stat_all" returns="mixed">
///     <args>
///       <arg name="fetch_first" type="boolean" default="false" />
///     </args>
///     <body>
  public function stat_all($fetch_first = false) {
    list($m, $this->mode) = array($this->mode, '');
    $r = $this->make_cursor($this->sql()->stat(false, $m), false)->
      bind($this->__get('binds'))->
      execute();
    return ($fetch_first ? $r->fetch() : $r->fetch_all());
  }
///     </body>
///   </method>

///    <method name="filter" returns="DB.ORM.SQLMapper">
///      <args>
///        <arg name="args" type="args" />
///      </args>
///      <body>
  public function filter($args) {
    switch (true) {
      case is_string($args): return $this->where($args);
      default:
        return $this->spawn()->apply_filter($args);
    }
  }
///      </body>
///    </method>

///   <method name="find" returns="mixed">
///     <brief>Выполняет поиск объекта в таблице по значению первичного ключа</brief>
///     <details>
///       <p>Значение ключа передается в качестве аргумента, если ключа составной, передается массив или просто
///          список аргументов, содержащий значение каждого поля ключа. Если соответствующая запись отсутствует в
///          таблице, возвращается значение null.</p>
///       <p>Например:</p>
///       <pre>
///         $story = $db->news->stories->find($id);
///       </pre>
///     </details>
///     <body>
  public function find() {
    $binds = array();
    $values = Core::normalize_args(func_get_args());

    foreach ($this->options['key'] as $idx => $key)
      $binds[$key] = isset($values[$key]) ? $values[$key] : (isset($values[$idx]) ? $values[$idx] : 0);

    if (($entity = $this->make_cursor($this->sql()->find())->
      bind(array_merge($this->__get('binds'), $binds))->
      execute()->
      fetch()) && method_exists($entity, 'after_find')) $entity->after_find();

    return $entity;
  }
///     </body>
///   </method>

///   <method name="lookup" returns="mixed">
///     <args>
///       <arg name="value" />
///     </args>
///     <body>
  public function lookup($value) {
    if (($entity = isset($this->options['lookup_by']) ?
      $this->spawn()->where($this->options['table_prefix'].'.'.$this->options['lookup_by'].'=:__val', $value)->select_first() :
      null) && method_exists($entity, 'after_find')) $entity->after_find();

    return $entity;
  }
///     </body>
///   </method>

///   <method name="update" returns="boolean">
///     <brief>Обновляет информацию об объекте бизнес-логики в таблице базе данных</brief>
///     <args>
///       <arg name="e" type="mixed" brief="сохраняемый объект" />
///     </args>
///     <details>
///       <p>Этот метод предназначен для случаев, когда необходимо сохранить измененные данные одного объекта
///          бизнес-логики.</p>
///       <p>Сохранение объекта производится следующим образом:</p>
///       <ol>
///         <li>Если для данного или родительского маппера установлен валидатор, выполняется проверка сохраняемого
///             объекта этим валидатором. Если объект не является валидным, сохранение не выполняется;</li>
///         <li>Если сохраняемый объект реализует метод before_save, этот метод выполняется, и дальнейшее сохранение
///             производится только в том случае, если он вернул истинное значение;</li>
///         <li>Если сохраняемый объект реализует метод before_update, этот метод выполняется, и дальнейшее сохранение
///             производится только в том случае, если он вернул истинное значение;</li>
///         <li>Формируется SQL-выражение UPDATE, строится и выполняется соответствующий курсор;</li>
///         <li>Если запись в таблицу была выполнена успешно и для сохраняемого объекта определен метод after_update,
///             этот метод выполняется;</li>
///         <li>Если предыдущая операция (сохранение или вызов after_update) завершилась успешно и у сохраняемого
///             объекта определен метож after_save, вызывается этот метод.</li>
///       </ol>
///       <p>Метод возвращает true, если все перечисленные операции были выполнены успешно и false в противном
///          случае.</p>
///       <p>Например:</p>
///       <pre>
///         $story = $db->news->stories->find($id);
///         $story->title = 'News story title';
///         $db->news->stories->update($story);
///       </pre>
///     </details>
///     <body>
  public function update($e) {
    list($m, $this->mode) = array($this->mode, '');
    return
      $this->validate($e) &&
      $this->callback($e, 'before_save') && $this->callback($e, 'before_update') &&
      ($this->make_cursor($this->sql()->update($m))->
        bind($e)->
        execute()->is_successful) &&
      $this->callback($e, 'ater_update') && $this->callback($e, 'after_save');
  }
///     </body>
///   </method>

///   <method name="update_all" returns="boolean">
///     <brief>Изменяет значение отдельных колонок в таблице для набора записей</brief>
///     <args>
///       <arg name="values" type="array" values="набор значений" />
///     </args>
///     <details>
///       <p>Метод соответствует обычной семантике SQL UPDATE и предназначен для случаев, когда необходимо поменять
///          какие-либо значения у набора записей в таблице. Подмножество записей определяется WHERE-выражением,
///          формируемым цепочкой мапперов.</p>
///       <p>Операция обновления оперирует колонками таблицы без использования объектов бизнес-логики, соответственно,
///          валидатор не используется и методы вида before_save/after_save тоже.</p>
///       <p>Параметр values представляет собой массив, ключи которого представляют собой имена полей, а значения --
///          соответствующие новые значения этих полей.</p>
///       <p>Метод возвращает true в случае успешного выполнения операции и false в противном случае.</p>
///       <p>Пример использования:</p>
///       <pre>
///         $db->news->stories->where('state = 1')->update_all(array('state' => 2));
///       </pre>
///     </details>
///     <body>
  public function update_all(array $values) {
    list($m, $this->mode) = array($this->mode, '');
    return $this->make_cursor(
      $this->sql()->update_all($m)-> set(array_keys($values)))->
      bind(array_merge($this->__get('binds'), $values))->
      execute()->is_successful;
  }
///     </body>
///   </method>

///   <method name="insert" returns="int">
///     <brief>Добавляет информацию об объекте бизнес логики в таблицу базы данных</brief>
///     <args>
///       <arg name="e" type="mixed" brief="сохраняемый объект" />
///       <arg name="mode" type="string" default="''" brief="ljgjkyb" />
///     </args>
///     <details>
///       <p>Добавление производится следующим образом:</p>
///       <ol>
///         <li>Если для данного или родительского маппера установлен валидатор, выполняется проверка добавляемого
///             объекта этим валидатором. Если объект не является валидным, добавление не выполняется;</li>
///         <li>Если добавляемый объект реализует метод before_save, этот метод выполняется, и дальнейшее сохранение
///             производится только в том случае, если он вернул истинное значение;</li>
///         <li>Если сохраняемый объект реализует метод before_insert, этот метод выполняется, и дальнешее сохранение
///             производится только в том случае, если он вернул истинное значение;</li>
///         <li>Формируется SQL-выражение INSERT, строится и выполняется соответствующий курсор;</li>
///         <li>Если запись в таблицу была выполнена успешно и для сохраняемого объекта определен метод after_insert,
///             этот метод выполняется;</li>
///         <li>Если предыдущая операция (сохранение или вызов after_insert) завершилась успешно и у сохраняемого
///             объекта определен метод after_save, вызывается этот метод.</li>
///       </ol>
///       <p>Метод возвращает true, если все перечисленные операции были выполнены успешно и false в противном
///          случае.</p>
///       <p>В случае, если первичный ключ генерируется автоматически, сгенерированное значение присваивается
///          соответствующему свойству сохраняемого объекта.</p>
///       <p>Пример использования:</p>
///       <pre>
///        $story = new Story();
///        ...
///        $db->news->stories->insert($story);
///        print "New id is ".$story->id;
///       </pre>
///     </details>
///     <body>
  public function insert($e) {
    list($m, $this->mode) = array($this->mode, '');
    $rc =
      $this->validate($e) &&
      $this->callback($e, 'before_save') && $this->callback($e, 'before_insert') &&
      $this->make_cursor($this->sql()->insert($m))->
        bind($e)->
          execute();
    if ($rc) {
      $id = (int) $this->connection->last_insert_id();
      if ($id && (count($this->options['key']) == 1) && !$this->options['explicit_key'])
        $e[$this->options['key'][0]] = $id;
    }
    return
      $rc && $this->callback($e, 'after_insert') && $this->callback($e, 'after_save');
  }
///     </body>
///   </method>

///   <method name="delete" returns="boolean">
///     <brief>Удаляет информацию об объекте бизнес-логики из таблицы базы данных</brief>
///     <args>
///       <arg name="e" type="mixed" brief="удаляемый объект" />
///     </args>
///     <details>
///       <p>Удаление производится следующим образом:</p>
///       <ol>
///         <li>Если удаляемый объект реализует метод before_delete, этот метод выполняется и дальнейшее удаление
///             производится только  в том случае, если он вернул истинное значение;</li>
///         <li>Формируется SQL-выражение DELETE, строится и выполняется соответствующий курсор;</li>
///         <li>Если удаление было успешным и удаляемый объект реализует метод after_delete, вызывается этот
///             метод</li>
///       </ol>
///       <p>Метод возвращает true, если все перечисленные операции были выполнены успешно и false в противном
///          случае.</p>
///       <p>Например:</p>
///       <pre>
///         $story = $db->news->stories->find($id);
///         $db->news->stories->delete($story);
///       </pre>
///     </details>
///     <body>
  public function delete($e) {
    list($m, $this->mode) = array($this->mode, '');
    return
      $this->callback($e, 'before_delete') &&
      $this->make_cursor($this->sql()->delete($m))->
        bind($e)->
        execute()->is_successful &&
      $this->callback($e, 'after_delete');
  }
///     </body>
///   </method>

///   <method name="delete_all" returns="boolean">
///     <brief>Удаляет набор записей из таблицы</brief>
///     <details>
///     <p>Метод соответствует обычной семантике SQL DELETE. Удаляемый набор записей определяется WHERE-выражением,
///        определяемым цепочкой мапперов.</p>
///     <p>Поскольку объекту бизнес-логики при выполнении операции не используются, методы этих объектов before_delete и
///        after_delete не задействуются.</p>
///     </details>
///     <body>
  public function delete_all() {
    list($m, $this->mode) = array($this->mode, '');
    return $this->make_cursor($this->sql()->delete_all($mode))->
      bind($this->__get('binds'))->
      execute()->is_successful;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="counting" interface="Core.CountInterface">

///   <method name="count" returns="int">
///     <body>
  public function count() { return $this->stat(true); }
///     </body>
///   </method>

///   </protocol>


///   <protocol name="mapping">

///   <method name="cmap" returns="mixed">
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args" type="array" default="array()" />
///     </args>
///     <body>
  protected function cmap($name, $method, array $args = array()) {
    return is_string($method) ? call_user_func_array(array($this->spawn(), $method), $args) : $this->__map($name, $args);
  }
///     </body>
///   </method>

///   <method name="pmap" returns="mixed">
///     <args>
///       <arg name="method" type="string" />
///     </args>
///     <body>
  protected function pmap($name, $method) {
    return is_string($method) ? call_user_func_array(array($this->spawn(), $method), array()) : $this->__map($name);
  }
///     </body>
///   </method>

///   </protocol>


///   <protocol name="building">

///   <method name="spawn_for" returns="DB.ORM.SQLStatement">
///     <brief>Порождает дочерний маппер с заданным where-условием</brief>
///     <details>
///       <p>Первый параметр метода должен содержать текст where-условия,
///          остальные параметры -- значения аргументов</p>
///       <p>Например:</p>
///       <pre><![CDATA[
///         $db->news->stories->spawn_for('pub_date < ?', Time::now());
///       ]]></pre>
///     </details>
///     <body>
  public function spawn_for() {
    $args = func_get_args();
    return $this->spawn()->where(array_shift($args), Core::normalize_args($args));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="paginate_with" returns="DB.ORM.SQLMapper">
///     <brief>Порождает дочерний маппер, c диапазоном записей, соответствущим диапазону объекта-пейджера</brief>
///     <args>
///       <arg name="pager" type="Data.Pagination" />
///     </args>
///     <body>
  public function paginate_with(Data_Pagination_Pager $pager) {
    return $this->spawn()->range($pager->items_per_page, $pager->current->offset);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="iterating" interface="IteratorAggregate">

///   <method name="getIterator" returns="Iterator">
///     <brief>Возвращает итератор по записям маппера</brief>
///     <body>
  public function getIterator() { return $this->query(false)->getIterator(); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///    <method name="apply_filter" returns="DB.ORM.SQLMapper" access="protected">
///      <args>
///        <arg name="args" />
///      </args>
///      <body>
  protected function apply_filter($args) { return $this; }
///      </body>
///    </method>

///   <method name="validate" returns="boolean" access="protected">
///     <args>
///       <arg name="entity" />
///     </args>
///     <body>
  protected function validate($entity) {
    return isset($this->options['validator']) ?
      Core::with_index($this->options, 'validator')->validate($entity) :
      true;
  }
///     </body>
///   </method>

///   <method name="callback" returns="mixed" access="protected">
///     <args>
///       <arg name="entity" />
///       <arg name="name" type="string" />
///     </args>
///     <body>
  protected function callback($entity, $name) {
    return is_object($entity) && method_exists($entity, $name) ?
      $entity->$name() : true;
  }
///     </body>
///   </method>

///   <method name="make_cursor" returns="DB.Cursor" access="protected">
///     <args>
///       <arg name="sql" type="string" />
///     </args>
///     <body>
  protected function make_cursor($sql, $typed = true) {
    $cursor = $this->connection->prepare($sql);
    if ($typed) $cursor->as_object(Core::if_null($this->options['classname'], DB::option('collection_class')));
    return $cursor;
  }
///     </body>
///   </method>

///   <method name="sql" returns="DB.ORM.SQL.Statement" access="protected">
///     <brief>Возвращает объект-генератор SQL-выражений, соотаетствующий опциям маппера</brief>
///     <body>
  public function sql() { return new DB_ORM_SQLBuilder($this->options); }
///     </body>
///   </method>

///   <method name="cache" returns="DB.ORM.Mapper" access="protected">
///     <brief>Кеширует бизнес-объект во внутреннем кеше маппера</brief>
///     <args>
///       <arg name="index" type="int" />
///       <arg name="object" type="object" />
///     </args>
///     <body>
  protected function cache($index, $object) {
    $this->cache[$index] = $object;
    return $this;
  }
///     </body>
///   </method>

///   <method name="collect_binds" returns="DB.ORM.SQLMapper" access="protected">
///     <brief>Формирует общий список значений параметров с учетом значений параметров родительских мапперов</brief>
///     <args>
///       <arg name="expr" type="string" />
///       <arg name="parms" type="mixed" />
///     </args>
///     <body>
  protected function collect_binds($expr, $parms) {
    $adapter = $this->connection->adapter;

    if ($adapter->is_castable_parameter($parms)) $parms = $adapter->cast_parameter($parms);

    if ($match = Core_Regexps::match_all('{(?::([a-zA-Z_0-9]+))}', $expr)) {
      foreach ($match[1] as $no => $name) {
        if (Core_Types::is_array($parms) || $parms instanceof ArrayAccess)
          $this->binds[$name] = $adapter->cast_parameter(isset($parms[$name]) ? $parms[$name] : $parms[$no]);
        elseif (is_object($parms))
          $this->binds[$name] = $adapter->cast_parameter($parms->$name);
        else
          $this->binds[$name] = $adapter->cast_parameter($parms);
      }
    }

    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="classname" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает имя класса отображаемых объектов бизнес-логики</brief>
///     <args>
///       <arg name="name" type="string" brief="имя класса" />
///     </args>
///     <body>
///     </body>
///   </method>

///   <method name="validator" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает используемый валидатор</brief>
///     <args>
///       <arg name="validator" type="Validation.Validator" brief="валидатор" />
///     </args>
///     <body>
///     </body>
///   </method>

///   <method name="table" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает имя таблицы, с которой работает маппер</brief>
///     <args>
///       <arg name="tablename" type="string" brief="имя таблицы" />
///     </args>
///     <details>
///       <p>Аргумент метода может содержать также псевдоним таблицы, отделенный от ее полного имени единственным
///       пробелом.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="columns" returns="DB.ORM.SQLMapper">
///     <brief>Определяет список столбцов таблицы, с которой работает маппер.</brief>
///     <details>
///       <p>По умолчанию, перечисленные поля используются при формировании SQL-выражений SELECT, UPDATE и INSERT.</p>
///       <p>Список может быть задан как в виде массива, так и просто перечислением аргументов.</p>
///       <p>Значение опции переопределяет значение соответствующей опции родительского маппера.</p>
///       <p>Например:</p>
///       <pre>
///         $this->columns('id', 'category_id', 'title', 'body', 'pub_date');
///       </pre>
///     </details>
///     <args>
///     </args>
///   </method>

///   <method name="calculate" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает список вычисляемых полей результирующего набор</brief>
///     <args>
///       <arg name="columns" type="array" />
///     </args>
///     <details>
///       <p>Аргумент метода представляет собой массив, ключи которого соответствуют именам вычисляемых полей, а
///          значения представляют собой выражения, используемые для их вычисления.</p>
///       <p>Значение опции дополняет значение соответствующей опции родительского маппера.</p>
///       <p>Например:</p>
///       <pre>
///         $this->calculate(array(
///          'num_of_items' => 'COUNT(DISTINCT item_id)',
///          'next_day'     => 'DATE_ADD(date_starts,INTERVAL 1 DAY)'));
///       </pre>
///       <p>Значение опции дополняет значение соответствующей опции родительского маппера.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="exclude" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает список полей, которые должны быть исключены из результирующего набора</brief>
///     <details>
///       <p>Как правило, эта опция используется для изменения набора полей, определяемого родительским маппером.</p>
///       <p>Например, если маппер <pre>$db->stories</pre> определяет поля id, pub_date, title, body, а для списка
///          10 последних статей нам не нужны полные тексты статей, мы можем создать дочерний маппер, не выполняющий
///          выборку текстов:</p>
///       <pre>
///         class StoriesMapper {
///           ...
///           function map_latest() { return $this->exclude('body'); }
///           ...
///         }
///       </pre>
///       <p>Список может быть задан как в виде массива, так и просто перечислением аргументов.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="key" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает набор полей, используемых в качестве первичного ключа.</brief>
///     <details>
///       <p>Настоятельно не рекомендуется использовать составные ключи, так как это существенно усложняет архитектуру
///          приложения. Оптимыльный ключи в случае использования MySQL -- одиночное автоинкрементное поле.</p>
///       <p>Значение опции переопределяет значение соответствующей опции родительского маппера.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="explicit_key" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает необходимость явного задания значения для одиночного первичного ключа</brief>
///     <args>
///       <arg name="explicit" type="boolean" default="true" />
///     </args>
///     <details>
///       <p>По умолчанию считается, что ключ, состоящий из единственного поля, заполняется автоматически. В большинстве
///          случаев это наиболее разумный выбор, однако иногда может возникнуть необходимость в явном заполнении ключа.
///          Для этого необходимо указать опцию explicit_key:</p>
///       <pre>
///         $mapper->explicit_key();
///       </pre>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="join" returns="DB.ORM.SQLMapper">
///     <brief>Описывает SQL-выражение join, используемое при выборке</brief>
///     <args>
///       <arg name="type"      type="string" brief="тип join-а" />
///       <arg name="table"     type="string" brief="имя таблицы" />
///       <arg name="condition" type="string" brief="условие join-а" />
///     </args>
///     <details>
///       <p>Аргумент type указывает вид объединения. В зависимости от используемой базы данных допустимы различные
///          виды объединений, например: left, right, inner, outer и т.д.</p>
///       <p>Добавление выражения join не добавляет в результирующий набор поля присоединяемой таблицы. Для добавления
///          полей необходимо воспользоваться опцией calculate.</p>
///       <p>Например:</p>
///       <pre>
///          $stories->join('right', 'tags', 'tags.id = news_tag_refs.tag_id')->
///            where('story_id = :id', $story)->
///            calculate('id', 'name', 'title', 'num_of_refs');
///       </pre>
///       <p>Значение опции дополняет значение соответствующей опции родительского маппера.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="where" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает WHERE-условие выборки</brief>
///     <args>
///       <arg name="expr" type="string|array" brief="Текст WHERE-выражения" />
///       <arg name="parms" default="array" brief="Значения параметров условия" />
///     </args>
///     <details>
///       <p>Метод позволяет определять условия, по которым производится выборка отображаемых объектов при этом
///          можно задавать набор условий, последовательно вызывая метод с различными параметрами.</p>
////      <p>Первый аргумент метода должен содержать WHERE-условие. Условие может быть задано либо в виде строки,
///          либо в виде массива строк, в противном случае результирующее условие получается как объединение всех
///          условий в массиве с помощью оператора AND. Условия, определяемые несколькими последовательными вызовами
///          метода также объединяются оператором AND.</p>
///       <p>Условия выборки могут содержать маркеры для подстановки параметров запроса (placeholder).</p>
///       <p>Второй аргумент содержит значения параметров условия. Параметры добавляются в общий список параметров
///          маппера и подставляются в SQL-выражения непосредственно перед выполнением. Список параметров может быть
///          задан в виде массива или объекта. Если параметр один, можно просто указать его значение в качестве
///          аргумента.</p>
///       <p>Значение опции дополняет значение соответствующей опции родительского маппера, при этом условия
///          родительских мапперов объединяются с условиями дочернего маппера с помощью операции AND.</p>
///       <p>Например:</p>
///       <pre><![CDATA[
///         function map_for_date(Time_DateTime $date) {
///           return $this->where('pub_date < :date', $date)
///         }
///       ]]></pre>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="having" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает HAVING-условие выборки</brief>
///     <args>
///       <arg name="expr" type="string|array" brief="Текст HAVING-выражения" />
///       <arg name="parms" default="array" brief="Значение параметров условия" />
///     </args>
///     <details>
///       <p>Метод полностью аналогичен методу where.</p>
///     </details>
///   </method>

///   <method name="order_by" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает SQL-выражение ORDER BY</brief>
///     <details>
///       <p>Эта опция переопределяет соответствующую опцию родительского маппера, а не дополняет ее.</p>
///       <p>Например:</p>
///       <pre>
///         $mapper->order_by('pub_date DESC, id DESC');
///       </pre>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="group_by" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает SQL-выражение GROUP BY</brief>
///     <details>
///       <p>Эта опция переопределяет соответствующую опцию родительского маппера, а не дополняет ее.</p>
///       <p>Например:</p>
///       <pre>
///         $mapper->group_by('category_id');
///       </pre>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="range" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает диапазон выборки результирующего набора</brief>
///     <args>
///       <arg name="limit"  type="int" brief="количество выбираемых записей" />
///       <arg name="offset" type="int" default="0" brief="смещение от начала результирующего набора" />
///     </args>
///     <details>
///       <p>Позволяет указать количество выбираемых записей и смещение от начала результирующего набора.
///          Эта опция переопределяет соответствующую опцию родительского набора.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   <method name="index" returns="DB.ORM.SQLMapper">
///     <brief>Устанавливает явное имя индекса, используемого при выборке</brief>
///     <args>
///       <arg name="name" type="string" brief="имя индекса" />
///     </args>
///     <details>
///       <p>Имя индекса используется при формировании текста SELECT-запросов.</p>
///       <p>Эта опция переопределяет соответствующую опцию родительского набора.</p>
///     </details>
///     <body>
///     </body>
///   </method>

///   </protocol>


///   <protocol name="calling">

///   <method name="__call">
///     <brief>Выполняет диспетчеризацию вызово динамических методов</brief>
///     <args>
///       <arg name="method" type="string" />
///       <arg name="args"   type="array" />
///     </args>
///     <brief>
///       <p>Метод обрабатывает вызовы методов установки опций маппера. С деталями можно ознакомиться в описаниях
///          соответствующих методов.</p>
///     </brief>
///     <body>
  public function __call($method, $args) {
    switch ($method) {
      case 'classname':
      case 'column':
      case 'validator':
      case 'table':
      case 'calculate':
      case 'order_by':
      case 'group_by':
      case 'range':
      case 'explicit_key':
      case 'lookup_by':
      case 'index':
      case 'defaults':
        $this->is_immutable ?
          $this->spawn()->__call($method, $args) :
          $this->options->__call($method, $args);
        return $this;
      case 'key':
      case 'columns':
      case 'exclude':
        $this->is_immutable ?
          $this->spawn()->__call($method, $args) :
          $this->options->$method(Core::normalize_args($args));
        return $this;
      case 'having':
      case 'where':
        if ($this->is_immutable) return $this->spawn()->__call($method, $args);

        list($expr, $parms) = $args;

        if (is_array($expr)) $expr = '('.implode(') AND (', $expr).')';
        if ($parms !== null) $this->collect_binds($expr, $parms);

        $this->options->$method($expr);
        return $this;
      case 'join':
        if ($this->is_immutable) return $this->spawn()->__call($method, $args);
        $type  = array_shift($args);
        $table = array_shift($args);
        $this->options->$method($type, $table, Core::normalize_args($args));
        return $this;
      default:
        return parent::__call($method, $args);
    }
  }
///     </body>
///   </method>

///   </protocol>


///   <protocol name="indexing" interface="Core.IndexedAccessInterface">

///   <method name="offsetGet" returns="mixed">
///     <brief>Загружает отображаемый объект и сохраняет его в кэше маппера</brief>
///     <args>
///       <arg name="index" brief="значение первичного ключа записи, соответствующей объекту" />
///     </args>
///     <details>
///       <p>Интерфейс индексируемого доступа предназначен загрузки объектов с использованием кэширования.</p>
///       <p>Иначе говоря, вызов $mapper[$id] эквивалентен вызову $mapper->find($id), за исключением того, что в первом
///          случае загруженный объект будет сохранен в кэше маппера. Поэтому, повторное обращение $mapper[$id] при
///          том же значении id вернет соответствующий объект без обращения к базе.</p>
///       <p>Этот мезанизм может быть использован для реализации загрузки объекта в случае связи "один ко многим".
///          Например, пусть у нас есть статьи и рубрики, причем каждая статья принадлежит рубрике, что определяется
///          полем category_id в таблице статей. Предположим, что метод DB::db() возвращает нам корневой элемент нашего
///          дерева мапперов. В этом случае в объекте предметной области класса Story  мы можем реализовать следующий
///          метод:</p>
///       <pre>
///         class Story {
///           function get_category() {
///             return DB::db()->categories[$this['category_id']];
///           }
///         }
///       </pre>
///     </details>
///     <body>
  public function offsetGet($index) {
    switch (true) {
      case is_numeric($index):
        if (!isset($this->cache[$index]) && ($e = $this->find($index))) $this->cache[$index] = $e;
        return $this->cache[$index];
      default:
        if (($e = $this->lookup((string) $index)) && ($key = $this->options['key'][0])) $this->cache[$e->$key] = $e;
        return $e;
    }
  }
///     </body>
///   </method>

///   <method name="offsetSet" returns="mixed">
///     <brief>Запрещает явную запись объектов в кэш.</brief>
///     <args>
///       <arg name="index" brief="значение первичного ключа записи, соответствующей объекту" />
///       <arg name="value" brief="значение свойства" />
///     </args>
///     <details>
///       <p>Объект может быть помещен в кэш только при выполнении операции find, инициированной доступом к
///          индексированному свойству. Однако он может быть явно удален из кэша с помощью операции unset.</p>
///     </details>
///     <body>
  public function offsetSet($index, $value) {
    throw isset($this->cache[$index]) ?
      new Core_ReadOnlyIndexedPropertyException($index) :
      new Core_MissingIndexedPropertyException($index);
  }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
//      <brief>Проверяет наличие объекта в кэше маппера</brief>
///     <args>
///       <arg name="index" brief="значение первичного ключа записи, соответствующей объекту" />
///     </args>
///     <body>
  public function offsetExists($index) { return isset($this->cache[$index]); }
///     </body>
///   </method>

///   <method name="offsetUnset">
///     <brief>Удаляет объект из кэша маппера</brief>
///     <args>
///       <arg name="index" brief="значение первичного ключа записи, соответствующей объекту" />
///     </args>
///     <details>
///       <p>Повторное обращение по индексу, соответствующему удаленному объекту, приведет к новому обращению к
///          базе данных.</p>
///     </details>
///     <body>
  public function offsetUnset($index) { unset($this->cache[$index]);  }
///     </body>
///   </method>

///   </protocol>


///   <protocol name="accessing">

///   <method name="__get" returns="mixed">
///     <brief>Возвращает значение свойства объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <details>
///       <p>Поддерживаются следующие свойства:</p>
///       <dl>
///         <dt>options</dt>
///         <dd>объект класса DB.ORM.MapperOptions, содержащий значения опций маппера;</dd>
///         <dt>cache</dt>
///         <dd>кэш отображаемых объектов;</dd>
///         <dt>binds</dt>
///         <dd>список значений параметров отображаемых объектов.</dd>
///       </dl>
///     </details>
///     <body>
  public function __get($property) {
    switch ($property) {
      case 'options':
        return $this->$property;
      case 'cache':
        return new ArrayObject($this->cache);
      case 'binds':
        return array_merge((array)$this->parent->__get('binds'), $this->binds);
      default:
        return parent::__get($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="mixed">
///     <brief>Запрещает изменение свойств объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///       <arg name="value"    type="значение свойства" />
///     </args>
///     <body>
  public function __set($property, $value) {
    switch ($property) {
      case 'options':
      case 'cache':
      case 'binds':
        throw new Core_ReadOnlyIndexedPropertyException($property);
      default:
        return parent::__set($property, $value);
    }
  }
///     </body>
///   </method>

///   <method name="__isset" returns="mixed">
///     <brief>Проверяет установку свойства объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __isset($property) {
    switch ($property) {
      case 'options':
      case 'cache':
      case 'binds':
        return true;
      default:
        return parent::__isset($property);
    }
  }
///     </body>
///   </method>

///   <method name="__unset" returns="mixed">
///     <brief>Запрещает удаление свойства объекта</brief>
///     <args>
///       <arg name="property" type="string" brief="имя свойства" />
///     </args>
///     <body>
  public function __unset($property) {
    switch ($property) {
      case 'options':
      case 'cache':
      case 'binds':
        throw new Core_UndestroyablePropertyException($property);
      default:
        return parent::__unset($property);
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="DB.ORM.SQLBuilder">
///   <brief>Генерирует SQL-выражения различных типов в соответствии с переданными опциями</brief>
///   <details>
///     <p>Объекты этого классы предназначены для работы совместно с объектами класса DB.ORM.SQLMapper и вряд ли будут использоваться самостоятельно.</p>
///   </details>
class DB_ORM_SQLBuilder {

  protected $options;

///   <protocol name="creating">

///   <method name="__construct">
///     <brief>Конструктор</brief>
///     <args>
///       <arg name="options" type="DB.ORM.MappingOptions" brief="опции генерации SQL-запросов" />
///     </args>
///     <body>
  public function __construct(DB_ORM_MappingOptions $options) { $this->for_options($options); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="for_options" returns="DB.ORM.SQLBuilder">
///     <brief>Устанавливает опции генерации SQL-запросов</brief>
///     <args>
///       <arg name="options" type="DB.ORM.MappingOptions" />
///     </args>
///     <body>
  public function for_options(DB_ORM_MappingOptions $options) {
    $this->options = $options;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="generating">

///   <method name="select" returns="DB.ORM.SQL.Select">
///     <brief>Генерирует SQL-выражение SELECT, выбирающее вся строки</brief>
///     <body>
  public function select($mode = '') {
    $sql = $this->select_with_options(
      $this->options['result'],
      array('join', 'where', 'group_by', 'having', 'order_by', 'index'), $mode);

    return ($r = $this->options['range']) ? $sql->range($r[0], $r[1]) : $sql;
  }
///     </body>
///   </method>

///   <method name="find" returns="DB.ORM.SQL.Select">
///     <brief>Генерирует SQL-выражение SELECT, выбирающее одну строку по первичному ключу</brief>
///     <body>
  public function find() {
    $table  = $this->options['table'];
    $prefix = isset($table[1]) ? $table[1] : $table[0];

    $keys   = array();
    foreach ($this->options['key'] as $v) $keys[] = "$prefix.$v = :$v";

    return $this->select_with_options(
      $this->options['result'],
      array('join', 'where', 'group_by', 'having', 'order_by', 'index'))->
        where($keys)->range(1);
  }
///     </body>
///   </method>

///   <method name="stat" returns="DB.ORM.SQL.SelectStatement">
///     <brief>Генерирует SQL-выражение SELECT, подсчитывающее вычисляемыю статистику</brief>
///     <details>
///       <p>В случае, если установлен параметр just_count, вычисляется выражение COUNT(*), в противном
///          случае -- набор вычисляемых полей.</p>
///     </details>
///     <args>
///       <arg name="just_count" type="boolean" default="false"  brief="признак вычисления COUNT()" />
///     </args>
///     <body>
  public function stat($just_count = false, $mode = '') {

    return $just_count ?
      $this->select_with_options(
        'COUNT(*) count',
        array('join', 'where', 'group_by', 'having', 'index'),
        $mode) :
      $this->select_with_options(
        Core::if_not_set($this->options, 'calculate', 'COUNT(*) count'),
        array('join', 'where', 'group_by', 'having', 'order_by', 'index'),
        $mode);
  }
///     </body>
///   </method>

///   <method name="insert" returns="DB.ORM.SQL.Insert">
///     <brief>Генерирует SQL-выражение INSERT</brief>
///     <args>
///       <arg name="mode" type="string" default="''" />
///     </args>
///     <body>
  public function insert($mode = '') {
    $o = $this->options;

    $auto = (count($o['key']) == 1 && !$o['explicit_key']) ? $o['key'][0] : false;

    $cols = array();
    foreach ($o['columns'] as $v) if (!($auto && $v == $auto)) $cols[] = $v;

    return DB_ORM_SQL::Insert($cols)->mode($mode)->into($o['table'][0]);
  }
///     </body>
///   </method>

///   <method name="delete" returns="DB.ORM.SQL.Delete">
///     <brief>Генерирует SQL-выражение DELETE для удаления одной строки по первичному ключу</brief>
///     <body>
  public function delete($mode = '') {
    $keys = array();
    foreach ($this->options['key'] as $v) $keys[] = "$v = :$v";

    return DB_ORM_SQL::Delete($this->options['table'][0])->where($keys)->mode($mode);
  }
///     </body>
///   </method>

///   <method name="delete_all" returns="DB.ORM.SQL.Delete">
///     <brief>Генерирует SQL-выражение DELETE для удаления всех строк</brief>
///     <body>
  public function delete_all($mode = '') {
    $sql = DB_ORM_SQL::Delete($this->options['table'][0])->mode($mode);
    return isset($this->options['where']) ? $sql->where($this->options['where']) : $sql;
  }
///     </body>
///   </method>

///   <method name="update" returns="DB.ORM.SQL.Update">
///     <brief>Генерирует SQL-выражение UPDATE для обновления одной записи по первичному ключу</brief>
///     <body>
  public function update($mode = '') {
    $keys = array();
    foreach ($this->options['key'] as $v) $keys[] = "$v = :$v";

    return DB_ORM_SQL::Update($this->options['aliased_table'])->
      mode($mode)->
      set($this->options['columns'])->
      where($keys);
  }
///     </body>
///   </method>

///   <method name="update_all" returns="DB.ORM.SQL.Update">
///     <brief>Генерирует SQL-выражение UPDATE для обновления всех записей</brief>
///     <body>
  public function update_all($mode = '') {
    return DB_ORM_SQL::Update($this->options['aliased_table'])->
      mode($mode)->
      where($this->options['where']);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="select_with_options" returns="DB.ORM.SQL.Statement" access="protected">
///     <brief>Формирует SQL-выражение SELECT с указанными опциями</brief>
///     <args>
///       <arg name="what" />
///       <arg name="options" type="array" default="array()" />
///     </args>
///     <body>
  protected function select_with_options($what, array $options = array(), $mode = '') {
    $sql = DB_ORM_SQL::Select($what)->mode($mode)->from($this->options['aliased_table']);

    foreach ($options as $opt) {
      switch ($opt) {
        case 'join':
          foreach ($this->options['join'] as $j) $sql->join($j[0], $j[1], $j[2]);
          break;
        case 'where':
        case 'group_by':
        case 'having':
        case 'order_by':
        case 'index':
          if (isset($this->options[$opt])) $sql->$opt($this->options[$opt]);
          break;
      }
    }
    return $sql;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="DB.ORM.MapperSet" extends="DB.ORM.Mapper">
///   <brief>Базовый класс мапперов, предназначенных для группировки прочих мапперов</brief>
///   <details>
///     <p>Группирующий маппер предназначен для логической группировки дочерних мапперов. Например, если
///        за работу с новостями отвечают мапперы categories, news и tags, их можно сгруппировать в маппер news:</p>
///      <pre>
///        $db->news->categories->select();
///        $db->news->stories()->for_category($category)->select();
///      </pre>
///     <p>Группировка реализуется определением соответствующих функций, возвращающих экземпляр дочернего маппера.
///        При этом результаты операции pmap кешируются, а результаты операции cmap не кешируются. Иначе говоря,
///        при условии реализации метода map_stories, $db->news->stories всегда будет возвращать один и тот же экземпляр маппера,
///        а $db->news->stories() -- все время разный.</p>
///   </details>
class DB_ORM_MapperSet extends DB_ORM_Mapper {

  private $cache   = array();
  private $mappers = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="parent" type="DB.ORM.Mapper" default="null" />
///     </args>
///     <body>
  public function __construct(DB_ORM_Mapper $parent = null) {
    parent::__construct($parent);
    $this->setup();
  }
///     </body>
///   </method>

///   <method name="setup" access="protected" returns="DB.ORM.MapperSet">
///     <body>
  protected function setup() { return $this; }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="submappers" returns="DB.ORM.MapperSet">
///     <args>
///       <arg name="mappers" type="array" />
///     </args>
///     <body>
  public function submappers(array $mappers, $prefix = '') {
    foreach ($mappers as $k => $v)
      $this->mappers[is_numeric($k) ? strtolower($v) : $k] = "$prefix$v";
    return $this;
  }
///     </body>
///   </method>

///   <method name="with_submappers" returns="DB.ORM.MapperSet">
///     <args>
///       <arg name="mappers" type="array" />
///     </args>
///     <body>
// TODO: deprecated
  public function with_submappers(array $mappers, $prefix = '') {
    return $this->submappers($mappers, $prefix);
  }
///     </body>
///   </method>

///   <method name="submapper" returns="DB.ORM.MapperSet">
///     <args>
///       <arg name="mapper" type="string" />
///       <arg name="module" type="string" />
///     </args>
///     <body>
  public function submapper($mapper, $module) {
    $this->mappers[(string) $mapper] = (string) $module;
    return $this;
  }
///     </body>
///   </method>

///   <method name="with_submapper" returns="DB.ORM.MapperSet">
///     <args>
///       <arg name="mapper" type="string" />
///       <arg name="module" type="string" />
///     </args>
///     <body>
// TODO: deprecated
  public function with_submapper($mapper, $module) {
    return $this->submapper($mapper, $module);
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="mapping">

///   <method name="pmap" returns="mixed" access="protected">
///     <brief>Реализация операции pmap: отображение свойства маппера</brief>
///     <details>
///       <p>Результат выполнения этой операции кешируется.</p>
///     </details>
///     <args>
///       <arg name="name" type="string" />
///       <arg name="method" type="string" />
///     </args>
///     <body>
  protected function pmap($name, $method) {
    if (!isset($this->cache[$name]))
      $this->cache[$name] = parent::pmap($name, $method);
    return $this->cache[$name];
  }
///     </body>
///   </method>

///   <method name="__can_map" returns="boolean">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="is_call" type="boolean" default="false" />
///     </args>
///     <body>
    public function __can_map($name, $is_call = false) { return isset($this->mappers[$name]); }
///     </body>
///   </method>

///   <method name="__map" returns="DB.ORM.Mapper">
///     <args>
///       <arg name="name" type="string" />
///       <arg name="args" default="null" />
///     </args>
///     <body>
    public function __map($name, $args = null) { return $this->load($this->mappers[$name]); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="load_mappers_from" returns="DB.ORM.MapperSet" scope="class">
///     <args>
///       <arg name="module" type="string" />
///     </args>
///     <body>
  protected function load_mappers_from($module) {
    Core::load($module);
    return call_user_func(array(Core_Types::real_class_name_for($module), 'mappers'), $this);
  }
///     </body>
///   </method>

///   <method name="load" returns="DB.ORM.Mapper">
///     <args>
///       <arg name="mapper" type="string" />
///     </args>
///     <body>
  protected function load($mapper) {
    if (preg_match('{^(?:(.+)\.)?([a-zA-Z0-9]+|\*)$}', $mapper, $m)) {
      if ($m[1]) Core::load($m[1]);
      return ($m[2] == '*') ?
        call_user_func(array(Core_Types::real_class_name_for($m[1]), 'mappers'), $this) :
        Core::make($mapper, $this);
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="DB.ORM.ConnectionMapper" extends="DB.ORM.Mapper" stereotype="abstract">
///   <implements interface="Core.PropertyAccessInterface" />
///   <brief>Группирующий маппер, хранящий объект подключения в серверу БД</brief>
///   <details>
///     <p>Этот типа мапперов -- наиболее подходящая кандидатура для корневого маппера иерархии.</p>
///     <p>Свойство connection доступно всем дочерним маппером через механизм наследования свойств.</p>
///     <p>Возможно также использование нескольких объектов этого класса, если различные ветви дерева
///        мапперов используют различные базы данных и объекты подключения к ним.</p>
///     <p>Установка соединения выполняется явно путем передачи объекта класса DB.Connection или строки DSN.
///        Таким образом, появляется возможность разделить процессы создания маппера и подключения к базе данных.</p>
///   </details>
abstract class DB_ORM_ConnectionMapper extends DB_ORM_MapperSet {

  protected $connection;

///   <protocol name="configuring">

///   <method name="connect" returns="DB.ORM.ConnectionMapper">
///     <brief>Устанавливает объект подключения к базе данных или создает на основании DSN</brief>
///     <args>
///       <arg name="connection" type="string|DB.Connection" brief="объект подключения или DSN подключения" />
///     </args>
///     <body>
  public function connect($connection) {
    $this->connection = ($connection instanceof DB_Connection) ?
      $connection :
      DB::Connection((string)$connection);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <interface name="DB.ORM.EntityInterface">
interface DB_ORM_EntityInterface extends Core_PropertyAccessInterface, Core_IndexedAccessInterface {}
/// </interface>


/// <interface name="DB.ORM.AttrEntityInterface">
interface DB_ORM_AttrEntityInterface extends DB_ORM_EntityInterface, Object_AttrListInterface {}
/// </interface>

/// <class name="DB.ORM.Entity" stereotype="abstract">
///   <implements interface="Core.PropertyAccessInterface" />
///   <brief>Базовый класс объектов бизнес-логики</brief>
///   <details>
///     <p>Все объекты бизнес логики удобнее всего наследовать именно от этого класса.</p>
///     <p>Объекты этого класса реализают два параллельных интерфейса доступа к данным:
///        через свойства и через индексированные свойства. При этом доступ через свойства,
///        обеспечивает работу с объектным представлениями значений свойств (взгляд на объект
///        со стороны клиентского приложения), а доступ через индексированные свойства -- со скалярным
///        представлением атрибутов, хранящихся в полях записи таблицы (взгляд на объект со стороны базы
///        данных).</p>
///     <p>Свойства, содержащие даты как объекты типа Time.DateTime, считаются скалярными, так как
///        их преобразованием занимается непосредственно модуль работы с базой данных DB.</p>
///     <p>Доступ к объектным представлениям свойств может быть переопределен путем реализации методов
///        get_{property} и set_{property}. Если соответствующий метод не определен, будет возвращено скалярное
///        значение атрибута, или null, если атрибут отсутствует.</p>
///     <p>Доступ к скалярным представлениям через индексированные свойства также может быть переопределен
///        путем реализации методов row_get_{property) и row_set_{property}.</p>
///     <p>Кроме того, список атрибутов доступен как предопределенное свойство attrs. При этом набор атрибутов не
///        фиксирован, так как различные запросы могут возвращать не все поля, описывающие объект, или наоборот,
///        добавлять в него дополнительные поля.</p>
///   </details>
abstract class DB_ORM_Entity
  implements DB_ORM_EntityInterface, Core_CallInterface {

  protected $attrs = array();

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="attrs" type="array" default="array()" />
///     </args>
///     <body>
  public function __construct(array $attrs = array()) { $this->setup()->assign($attrs); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="configuring">

///   <method name="setup" access="protected" returns="DB.ORM.Entity">
///     <body>
  protected function setup() { return $this; }
///     </body>
///   </method>

///   <method name="defaults" returns="DB.ORM.Entity" access="protected">
///     <args>
///       <arg name="values" type="array" />
///     </args>
///     <body>
  protected function defaults(array $values) {
    foreach ($values as $k => $v) $this[$k] = $v;
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="changing">

///   <method name="assign" access="protected" returns="DB.ORM.Entity">
///     <body>
  public function assign(array $attrs) {
    foreach ($attrs as $k => $v) $this->__set($k, $v);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing" interface="Core.PropertyAccessInterface">
///   <method name="offsetGet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetGet($index) {
    switch (true) {
      case method_exists($this, $name = "row_get_$index"):
        return $this->$name();
      case $index === '__class':
        return Core_Types::real_class_name_for($this);
      default:
        return $this->attrs[(string) $index];
    }
  }
///     </body>
///   </method>

///   <method name="offsetSet" returns="mixed">
///     <args>
///       <arg name="index" type="string" />
///       <arg name="value" />
///     </args>
///     <body>
  public function offsetSet($index, $value) {
    switch (true) {
      case method_exists($this, $name = "row_set_$index"):
        $this->$name($value);
        break;
      case $index === '__class':
        break;
      default:
        $this->attrs[(string) $index] = $value;
    }
    return $this;
  }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetExists($index) {
    return (isset($this->attrs[$index]) ||
            method_exists($this, "row_get_$index"));
  }
///     </body>
///   </method>

///   <method name="offsetUnset" returns="boolean">
///     <args>
///       <arg name="index" type="string" />
///     </args>
///     <body>
  public function offsetUnset($index) { throw new Core_ReadOnlyObjectException($this); }
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
      case 'attrs':
      case 'attributes':
        return $this->attrs;
      default:
        if (method_exists($this, $method = "get_$property"))
          return $this->$method();
        else
          return $this->attrs[$property];
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
      case 'attrs':
      case 'attributes':
        throw new Core_ReadOnlyPropertyException($property);
      default:
        if (method_exists($this, $method = "set_$property"))
          {$this->$method($value); return $this;}
        else
          return $this->attrs[$property] = $value;
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
      case 'attrs':
      case 'attributes':
        return true;
      default:
        return method_exists($this, "get_$property") || isset($this[$property]);
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
      case 'attrs':
      case 'attributes':
        throw new Core_UndestroyablePropertyException($property);
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="calling" interface="Core.CallInterface">

///   <method name="__call" returns="mixed">
///     <args>
///       <arg name="method" type="string" />
///     </args>
///     <body>
  public function  __call($method, $args) {
    $this->__set($method, $args[0]);
    return $this;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>