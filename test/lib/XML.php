<?php
/// <module name="Test.XML" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'XML');

/// <class name="Test.XML" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_XML implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.XML.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.XML.Case" extends="Dev.Unit.TestCase">
class Test_XML_Case extends Dev_Unit_TestCase {
  protected $lodaer;
  protected $builder;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->lodaer = XML::Loader();
    $this->builder = XML::Builder()->
      begin_entry(array('xmlns' => 'http://www.w3.org/2005/Atom', 'xmlns:g' => 'http://base.google.com/ns/1.0'))->
        id('http://www.google.com/base/feeds/items/3488121687238395803')->
        link(array('rel' => 'self', 'type' => 'application/atom+xml', 'href' => 'http://www.google.com/base/feeds/items/3488121687238395803'))->
        updated('2009-02-03T14:21:29.000Z')->
        begin_author()->
          name('Jane Doe')->
          email('JaneDoe@gmail.com')->
        end->
        title(array('Marie-Louise\'s chocolate butter', 'type' => 'text'))->
        begin_content(array('type' => 'xhtml'))->
          b('Ingredients:11213')->
          begin_ul()->
            li('250g11 margarine,')->
            li('200g sugar,')->
            li('2 eggs, and')->
            li('approx. 8 tsp cacao.')->
          end->
          p('Mix everything. Heat while stirring, but do not allow the mix to boil. Put in a container and cool in fridge.')->
        end->
        link(array('rel' => 'edit', 'type' => 'application/atom+xml', 'href' => 'http://www.google.com/base/feeds/items/3488121687238395803'))->
      end;
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
   $dom = $this->lodaer->load(file_get_contents('test/data/XML/node.xml'));
   $dom->normalizeDocument();
   $dom2 = DOMDocument::loadXML($this->builder->as_string());
   $dom2->normalizeDocument();

   $this->assert_equal(str_replace("\n", '',$dom->saveXML()),
    str_replace("\n", '',$dom2->saveXML()));
  }
///     </body>
///   </method>

///   <method name="test_load_errors">
///     <body>
  public function test_load_errors() {
    $xml = <<<XML
<?xml version='1.0' standalone='yes'?>
<movies>
 <movie>
  <titles>PHP: Behind the Parser</title>
 </movie>
</movies>
XML
;
    $loader = XML::Loader();
    $this->
      assert_false($loader->load($xml))->
      assert_equal($loader->errors[0]->message, "Opening and ending tag mismatch: titles line 4 and title\n");
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>