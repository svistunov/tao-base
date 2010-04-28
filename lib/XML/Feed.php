<?php
/// <module name="XML.Feed" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('XML','Net.Agents.HTTP');

/// <class name="XML.Feed" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class XML_Feed implements Core_ModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

  static private $parser;

///   <protocol name="creating">

///   <method name="initialize" scope="class">
///     <body>
  static public function initialize() {
    self::$parser = new XML_Feed_Parser();
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="parse" returns="XML.Feed" scope="class">
///     <args>
///       <arg name="xml" type="string" />
///     </args>
///     <body>
  static public function parse($xml) { return self::$parser->parse($xml); }
///     </body>
///   </method>

///   <method name="fetch" returns="XML.Feed" scope="class">
///     <args>
///       <arg name="xml" type="string" />
///       <arg name="agent" type="Net.HTTP.AgentInterface" />
///     </args>
///     <body>
  static public function fetch($url, Net_HTTP_AgentInterface $agent = null) {
    return self::$parser->fetch($url, $agent);
  }
///     </body>
///   </method>

///   <method name="load" returns="XML.Feed" scope="class">
///     <args>
///       <arg name="path" type="string" />
///     </args>
///     <body>
  static public function load($path) { return self::$parser->load($path); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Parser">
class XML_Feed_Parser {

///   <protocol name="performing">

///   <method name="fetch" returns="XML.Feed.Feed">
///     <args>
///     </args>
///     <body>
  public function fetch($url, Net_HTTP_AgentInterface $agent = null) {
    if (!isset($agent)) $agent = Net_HTTP::Agent();

    $r = $agent->send(Net_HTTP::Request($url));

    if ($r->status->is_success) return $this->parse($r->body);

    throw new XML_Feed_BadURLException($url);
  }
///     </body>
///   </method>

///   <method name="load" returns="XML.Feed.Feed">
///     <args>
///       <arg name="path" type="string" />
///     </args>
///     <body>
  public function load($path) {
    if ($xml = file_get_contents($path)) return $this->parse($xml);

    throw new XML_Feed_BadFileException($path);
  }
///     </body>
///   </method>

///   <method name="parse" returns="XML.Feed.Feed">
///     <args>
///       <arg name="xml" type="string" />
///     </args>
///     <body>
  public function parse($xml) {
    $document = XML::load(str_replace('xmlns=', 'ns=', $xml));
    return new XML_Feed_Feed($document, $this->detect_protocol_for($document));
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporing">

///   <method name="detect_protocol_for" returns="XML.Feed.Protocol" access="protected">
///     <args>
///       <arg name="document" type="DOMDocument" />
///     </args>
///     <body>
  protected function detect_protocol_for(DOMDocument $document) {
    $tag = $document->documentElement;
    $version = $tag->getAttribute('version');

    switch ($tag->tagName) {
      case 'rss':
        return new XML_Feed_RSS($document, 'rss '.($version ? $version : '2.0'));
      case 'rdf:RDF':
        return new XML_Feed_RSS($document, 'rss 1.0');
      case 'feed':
        return new XML_Feed_Atom($document, 'atom '.($version ? $version : '1.0'));
      default:
        throw new XML_Feed_UnsupportedProtocol($tag->tagName);
    }
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Exception" extends="Core.Exception">
///   <brief>Базовый класс исключений модуля</brief>
class XML_Feed_Exception extends Core_Exception {}
/// </class>

/// TODO: Exceptions


/// <class name="XML.Feed.Protocol" stereotype="abstract">
abstract class XML_Feed_Protocol implements Core_PropertyAccessInterface {

  protected $xpath;
  protected $version;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="dom" type="DOMDocument" />
///       <arg name="version" type="string" />
///     </args>
///     <body>
  public function __construct(DOMDocument $document, $version) {
    $this->xpath = new DOMXPath($document);
    $this->version = $version;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="quering">

///   <method name="link_for" returns="string" stereotype="abstract">
///     <args>
///       <arg name="element" type="DOMElement" />
///     </args>
///     <body>
  abstract public function link_for(DOMNode $element);
///     </body>
///   </method>

///   <method name="title_for" returns="string" stereotype="abstract">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  abstract public function title_for(DOMNode $element);
///     </body>
///   </method>

///   <method name="description_for" returns="string" stereotype="abstract">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  abstract public function description_for(DOMNode $element);
///     </body>
///   </method>

///   <method name="get_entries" returns="DOMNodeList" stereotype="abstract">
///     <body>
  abstract public function get_entries();
///     </body>
///   </method>

///   <method name="get_feed" returns="DOMElement" stereotype="abstract">
///     <body>
  abstract public function get_feed();
///     </body>
///   </method>

///   <method name="get" returns="string">
///     <args>
///       <arg name="query"   type="string" />
///       <arg name="element" type="DOMElement" />
///       <arg name="index"   type="int" default="0" />
///     </args>
///     <body>
  abstract public function get($query, DOMElement $element, $index = 0);
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="xpath" returns="mixed" access="protected">
///     <args>
///     </args>
///     <body>
  protected function xpath($query, DOMElement $element = null, $index = 0) {
    $r = $element ? $this->xpath->query($query, $element) : $this->xpath->query($query);
    return $index === null ? $r : $r->item($index);
  }
///     </body>
///   </method>

///   <method name="has_child_nodes_for" returns="boolean" access="protected">
///     <args>
///       <arg name="element" />
///     </args>
///     <body>
  protected function has_child_nodes_for($element) {
    return ($element instanceof DOMElement) && $this->xpath('./*', $element);
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
      case 'version':
        return $this->$property;
      default:
        throw new Core_MissingPropertyException($property);
    }
  }
///     </body>
///   </method>

///   <method name="__set" returns="XML.Feed.Protocol">
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
      case 'version':
        return isset($this->version);
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
}
/// </class>


/// <class name="XML.Feed.RSS" extends="XML.Feed.Protocol">
class XML_Feed_RSS extends XML_Feed_Protocol {

///   <protocol name="quering">

///   <method name="link_for" returns="string">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  public function link_for(DOMNode $element) { return $this->xpath('./link', $element)->nodeValue; }
///     </body>
///   </method>

///   <method name="title_for" returns="string">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  public function title_for(DOMNode $element) { return $this->xpath('./title', $element)->nodeValue; }
///     </body>
///   </method>

///   <method name="description_for" returns="string">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  public function description_for(DOMNode $element) { return $this->xpath('./description', $element)->nodeValue; }
///     </body>
///   </method>

///   <method name="get_entries" returns="DOMNodeList">
///     <body>
  public function get_entries() { return $this->xpath->query('//item'); }
///     </body>
///   </method>

///   <method name="get_feed" returns="DOMNodeList">
///     <body>
  public function get_feed() { return $this->xpath->query('//channel')->item(0); }
///     </body>
///   </method>

///   <method name="get" returns="mixed">
///     <args>
///       <arg name="query" type="string" />
///       <arg name="element" type="DOMElement" />
///       <arg name="index" type="int|null" default="0" />
///     </args>
///     <body>
  public function get($query, DOMElement $element, $index = 0) {
    return ($this->has_child_nodes_for($e = $this->xpath($query, $element, $index))) ?
             $e : ((($r = $e->nodeValue) && preg_match('{date$}i', $query)) ? Time::DateTime($r) : $r);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Atom" extends="XML.Feed.Protocol">
class XML_Feed_Atom extends XML_Feed_Protocol {

///   <protocol name="quering">

///   <method name="link_for" returns="string" stereotype="abstract">
///     <args>
///       <arg name="element" type="DOMElement" />
///     </args>
///     <body>
  public function link_for(DOMNode $element) {
    $e = $this->xpath("./link[rel='alternate']", $element);
    if (!$e) $e = $this->xpath('./link', $element);
    return $this->xpath('/*')->getAttribute('xml:base').
           (Core_Strings::starts_with($h = $e->getAttribute('href'), '/') ? substr($h, 1) : $h);
  }
///     </body>
///   </method>


///   <method name="title_for" returns="string">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  public function title_for(DOMNode $element) {
    return $this->node_value_for($this->xpath('./title', $element));
  }
///     </body>
///   </method>

///   <method name="description_for" returns="string">
///     <args>
///       <arg name="element" type="DOMNode" />
///     </args>
///     <body>
  public function description_for(DOMNode $element) {
    foreach (array('content', 'subtitle', 'info') as $name)
      if ($n = $this->xpath($name, $element)) return $this->node_value_for($n);
  }
///     </body>
///   </method>

///   <method name="get_entries" returns="DOMNodeList">
///     <body>
  public function get_entries() { return $this->xpath('//entry', null, null); }
///     </body>
///   </method>

///   <method name="get_feed" returns="DOMNode">
///     <body>
  public function get_feed() { return $this->xpath('/*'); }
///     </body>
///   </method>

///   <method name="get" returns="mixed">
///     <args>
///       <arg name="query" type="string" />
///       <arg name="element" type="DOMElement" />
///       <arg name="index" type="int" default="0" />
///     </args>
///     <body>
  public function get($query, DOMElement $element, $index = 0) {
    if ($this->has_child_nodes_for($e = $this->xpath($query, $element, $index))) return $e;

    return (($r = $this->node_value_for($e)) && Core_Strings::ends_with($query, 'ed')) ?
      Time::DateTime($r) : $r;
  }
///     </body>
///   </method>

///   <method name="node_value_for" returns="mixed" access="protected">
///     <args>
///       <arg name="element" />
///     </args>
///     <body>
  protected function node_value_for($element) {
    if (!($element instanceof DOMElement)) return '';
    if ($element->getAttribute('type') == 'xhtml' || $element->getAttribute('type') == 'application/xhtml+xml') {
      $div = $this->xpath('./div', $element);
      $div->removeAttribute('ns');
      return $element->ownerDocument->saveXML($div);
    }
    return $element->nodeValue;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Element">
///   <implements interface="Core.PropertyAccessInterface" />
///   <implements interface="Core.EqualityInterface" />
class XML_Feed_Element implements Core_PropertyAccessInterface, Core_EqualityInterface {

  protected $element;
  protected $protocol;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="element" type="DOMElement" />
///       <arg name="protocol" type="XML.Feed.Protocol" />
///     </args>
///     <body>
  public function __construct(DOMElement $element, XML_Feed_Protocol $protocol) {
    $this->element  = $element;
    $this->protocol = $protocol;
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
      case 'link':
      case 'title':
      case 'description':
        $m = $property.'_for';
        return $this->protocol->$m($this->element);
      default:
        return property_exists($this, $property) ?
          $this->$property :
          $this->get(Core_Strings::to_camel_case($property, true));
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
      case 'link':
      case 'title':
      case 'description':
        $m = $property.'_for';
        return $this->protocol->$m($this->element) !== null;
      default:
        return isset($this->$property) ||
               $this->get(Core_Strings::to_camel_case($property, true)) !== null;
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

///   <method name="get" returns="mixed">
///     <args>
///       <arg name="query" type="string" />
///       <arg name="index" type="int" default="0" />
///     </args>
///     <body>
  public function get($query, $index = 0) {
    return ($e = $this->protocol->get($query, $this->element, $index)) instanceof DOMElement ?
             new XML_Feed_Element($e, $this->protocol) : $e;
  }
///     </body>
///   </method>

///   <method name="equals" returns="boolean">
///     <args>
///     </args>
///     <body>
  public function equals($to) {
    return ($to instanceof self) && $this->element->isSameNode($to->element);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Feed" extends="XML.Feed.Element">
class XML_Feed_Feed extends XML_Feed_Element
  implements Core_IndexedAccessInterface, IteratorAggregate, Core_CountInterface {

  protected $document;
  protected $entries;

///   <protocol name="creating">

///   <method name="__construct">
///     <args>
///       <arg name="element" />
///       <arg name="protocol" type="XML.Feed.Protocol" />
///     </args>
///     <body>
  public function __construct(DOMDocument $document, XML_Feed_Protocol $protocol) {
    parent::__construct($protocol->get_feed(), $protocol);
    $this->entries  = $this->protocol->get_entries();
    $this->document = $document;
  }
///     </body>
///   </method>

///   </protocol>


///   <protocol name="counting" interface="Core.CountInterface">

///   <method name="count" returns="int">
///     <body>
  public function count() { return $this->entries->length; }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="indexing" interface="Core.IndexedAccesInterface">

///   <method name="offsetGet" returns="XML.Feed.Entry">
///     <args>
///       <arg name="index" type="int" />
///     </args>
///     <body>
  public function offsetGet($index) {
    return ($e = $this->entries->item($index)) ? new XML_Feed_Entry($e, $this->protocol) : null;
  }
///     </body>
///   </method>

///   <method name="offsetSet">
///     <args>
///       <arg name="index" type="int" />
///       <arg name="value" />
///     </args>
///     <body>
  public function offsetSet($index, $value) { throw new Core_ReadOnlyObjectException($this); }
///     </body>
///   </method>

///   <method name="offsetExists" returns="boolean">
///     <args>
///       <arg name="index" type="int" />
///     </args>
///     <body>
  public function offsetExists($index) { return $index > 0 && $index < $this->entries->length; }
///     </body>
///   </method>

///   <method name="offsetUnset">
///     <args>
///       <arg name="index" />
///     </args>
///     <body>
  public function offsetUnset($index) { throw new Core_ReadOnlyObjectException($this); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="iterating" interface="IteratorAggregate">

///   <method name="getIterator" returns="XML.Feed.Iterator">
///     <body>
  public function getIterator() { return new XML_Feed_Iterator($this); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>


/// <class name="XML.Feed.Entry" extends="XML.Feed.Element">
class XML_Feed_Entry extends XML_Feed_Element {}
/// </class>


/// <class name="XML.Feed.Iterator">
///   <implements interface="Iterator" />
class XML_Feed_Iterator implements Iterator {

   protected $index = -1;
   protected $current;
   protected $feed;

///   <protocol name="creating">

   public function __construct(XML_Feed_Feed $feed) { $this->feed = $feed; }

///   </protocol>

///   <protocol name="iterating">

///   <method name="rewind">
///     <body>
   public function rewind() {
     $this->index = -1;
     $this->current = $this->next();
   }
///     </body>
///   </method>

///   <method name="current" returns="XML.Feed.Entry">
///     <body>
   public function current() { return $this->current; }
///     </body>
///   </method>

///   <method name="key" returns="int">
///     <body>
   public function key() { return $this->index; }
///     </body>
///   </method>

///   <method name="next" returns="XML.Feed.Entry">
///     <body>
   public function next() {
     $this->index++;
     return $this->current = $this->feed[$this->index];
   }
///     </body>
///   </method>

///   <method name="valid" returns="boolean">
///     <body>
   public function valid() { return $this->index >= 0 && $this->index < count($this->feed); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>