<?php
/// <module name="SOAP" maintainer="svistunov@techart.ru" version="0.1.0">
Core::load('XML');

/// <class name="SOAP" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class SOAP implements Core_ModuleInterface {
///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="building">

///   <method name="Client">
///     <args>
///       <arg name="wsdl" type="string" />
///       <arg name="options" type="array" defaults="array()" />
///     </args>
///     <body>
  static public function Client($wsdl, $options = array()) {
    return new SOAP_Client($wsdl, $options);
  }
///     </body>
///   </method>

///   <method name="XmlFixer">
///     <body>
  static public function XmlFixer() {
    return new SOAP_XmlFixer();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="SOAP.Client" extends="SoapClient">
class SOAP_Client extends SoapClient {
  protected $last_request;
  protected $last_args;

///   <protocol name="performing">

///   <method name="__doRequest">
///     <args>
///       <arg name="request" type="string" />
///       <arg name="location" type="string" />
///       <arg name="action" type="string" />
///       <arg name="version" type="int" />
///     </args>
///     <body>
  public function __doRequest($request, $location, $action, $version) {
    $this->last_request = SOAP::XmlFixer()->
      fix_xml($request, $this->last_args);
    return parent::__doRequest($this->last_request,
      $location, $action, (int) $version);
  }
///     </body>
///   </method>

///   <method name="__sopaCall">
///     <args>
///       <arg name="function_name" type="string" />
///       <arg name="arguments" type="array" />
///       <arg name="options" type="array" />
///       <arg name="input_headers" type="array" />
///       <arg name="output_headers" type="array" />
///     </args>
///     <body>
  public function __soapCall($function_name, $arguments, $options = NULL,
    $input_headers = NULL, &$output_headers = NULL) {

    $this->last_args = $arguments;
    return parent::__soapCall($function_name, $arguments, $options,
      $input_headers, $output_headers);
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="SOAP.XmlFixer">
class SOAP_XmlFixer {

///   <protocol name="creating">

///   <method name="__construct">
///     <body>
  public function __construct() {
    if (version_compare(PHP_VERSION, '5.2.0', '<')) {
      trigger_error('The minimum required version is 5.2.0.', E_USER_ERROR);
    }
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="performing">

///   <method name="fix_xml">
///     <args>
///       <arg name="request" type="string" />
///       <arg name="args" type="array" />
///     </args>
///     <body>
  public function fix_xml($request, $args) {
    $request_dom = XML::Loader()->load($request);
    $xpath = new DOMXPath($request_dom);
    $arguments_dom_node = $xpath->query(
        "//*[local-name()='Envelope']/*[local-name()='Body']/*")->item(0);

    $this->fix_xml_node($arguments_dom_node, $args[0], $xpath);

    if (version_compare(PHP_VERSION, '5.2.3', '<'))
      $this->remove_empty_header_elements($xpath);
    return $request_dom->saveXML();
  }
///     </body>
///   </method>

///   <method name="fix_xml_nodes" access="private">
///     <args>
///       <arg name="node_list" type="DOMNodeList" />
///       <arg name="objects" type="array" />
///       <arg name="xpath" type="DOMXpath" />
///     </args>
///     <body>
  private function fix_xml_nodes(DOMNodeList $node_list, array $objects, DOMXPath $xpath) {
    if ($node_list->length != sizeof($objects))
      $node_list = $node_list->item(0)->childNodes;
    if ($node_list->length == sizeof($objects)) {
      $i = 0;
      foreach ($objects as $object) {
        $this->fix_xml_node($node_list->item($i), $object, $xpath);
        $i++;
      }
    }
  }
///     </body>
///   </method>

///   <method name="fix_xml_node" access="private">
///     <args>
///       <arg name="node" type="DOMNode" />
///       <arg name="object" />
///       <arg name="xpath" type="DOMXpath" />
///     </args>
///     <body>
  private function fix_xml_node(DOMNode $node, $object, DOMXPath $xpath) {
    if (version_compare(PHP_VERSION, '5.2.7', '<') && is_array($object))
      $this->add_xsi_type($node, $object);
    if (version_compare(PHP_VERSION, '5.2.3', '<') && !isset($object))
      $node->parentNode->removeChild($node);
    if (version_compare(PHP_VERSION, '5.2.2', '>=') && $node->hasAttribute('href'))
      $this->replace_element_reference($node, $xpath);
    if (true && $node->hasAttribute('xsi:type'))
      $this->redeclare_xsi_type_namespace_definition($node);

    if (is_array($object)) {
      foreach ($object as $var_name => $var_value) {
        $node_list = $xpath->query("*[local-name() = '" . $var_name . "']", $node);
        if (is_array($var_value))
          $this->fix_xml_nodes($node_list, $var_value, $xpath);
        else if ($node_list->length == 1)
          $this->fix_xml_node($node_list->item(0), $var_value, $xpath);
      }
    }
  }
///     </body>
///   </method>

///   <method name="add_xsi_type" access="private">
///     <args>
///       <arg name="dom_node" type="DOMNode" />
///       <arg name="object" />
///     </args>
///     <body>
  private function add_xsi_type(DOMNode $dom_node, $object) {
    $xsi_type_name = $object['__type'];
    if (isset($xsi_type_name) && $xsi_type_name != '') {
      $prefix = $dom_node->lookupPrefix($object['__ns']);
      $dom_node->setAttribute('xsi:type', (isset($prefix) ? $prefix . ':'  : '')
          . $xsi_type_name);
    }
  }
///     </body>
///   </method>

///   <method name="replace_element_reference" access="private">
///     <args>
///       <arg name="element_reference" type="DOMElement" />
///       <arg name="xpath" type="DOMXpath" />
///     </args>
///     <body>
  private function replace_element_reference(DOMElement $element_reference, DOMXPath $xpath) {
    $href = $element_reference->getAttribute('href');
    if (version_compare(PHP_VERSION, '5.2.2', '>=')
        && version_compare(PHP_VERSION, '5.2.4', '<')) {
      // These versions have a bug where href is generated without the # symbol.
      $href = '#' . $href;
    }
    $id = substr($href, 1);
    $referenced_elements = $xpath->query('//*[@id="' . $id . '"]');
    if ($referenced_elements->length > 0) {
      $referenced_element = $referenced_elements->item(0);
      for ($i = 0; $i < $referenced_element->childNodes->length; $i++) {
        $child_node = $referenced_element->childNodes->item($i);
        $element_reference->appendChild($child_node->cloneNode(true));
      }
      $element_reference->removeAttribute('href');
    }
  }
///     </body>
///   </method>

///   <method name="remove_empty_header_elements" access="private">
///     <args>
///       <arg name="xpath" type="DOMXpath" />
///     </args>
///     <body>
  private function remove_empty_header_elements(DOMXPath $xpath) {
    $request_header_dom = $xpath->query(
        "//*[local-name()='Envelope']/*[local-name()='Header']"
            . "/*[local-name()='RequestHeader']")->item(0);

    $child_nodes = $request_header_dom->childNodes;

    foreach ($child_nodes as $child_node) {
      if ($child_node->nodeValue == NULL) {
        $request_header_dom->removeChild($child_node);
      }
    }
  }
///     </body>
///   </method>

///   <method name="redeclare_xsi_type_namespace_definition" access="private">
///     <args>
///       <arg name="element" type="DOMElement" />
///     </args>
///     <body>
  private function redeclare_xsi_type_namespace_definition(DOMElement $element) {
    $type = $element->getAttribute('xsi:type');
    if (isset($type) && strpos($type, ':') !== false) {
      $parts = explode(':', $type, 2);
      $prefix = $parts[0];
      $uri = $element->lookupNamespaceURI($prefix);
      $element->setAttribute('xmlns:' . $prefix, $uri);
    }
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// </module>
?>
