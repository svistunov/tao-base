<?php
/// <module name="Test.Templates.HTML.Pagination" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Templates.HTML.Pagination');

/// <class name="Test.Templates.HTML.Pagination" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Templates_HTML_Pagination implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Templates.HTML.Pagination.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.HTML.Pagination.Case" extends="Dev.Unit.TestCase">
class Test_Templates_HTML_Pagination_Case extends Dev_Unit_TestCase {
  protected $pager;
  protected $template;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->template = Templates::HTML('test_name');
    $this->pager = Data_Pagination::Pager(500,5,10);
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->assert_equal(
      Templates_HTML_Pagination::instance()->pager(
        $this->template, $this->pager,
        array($this, 'url', array('name' => 'companies')),
        array('info' => true, 'class' => 'companies', 'padding' => 2)),
      '<div class="companies pager">'.
        '<div class="info">41 &ndash; 50 / 500</div>'.
        '<a class="" href="http://localhost/companies/pages/3">3</a>'.
        '<a class="" href="http://localhost/companies/pages/4">4</a>'.
        '<a class="active" href="http://localhost/companies/pages/5">5</a>'.
        '<a class="" href="http://localhost/companies/pages/6">6</a>'.
        '<a class="" href="http://localhost/companies/pages/7">7</a>'.
      '</div>'
    );
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="url">
///     <body>
  public function url($name, $page) {
    return 'http://localhost/'.$name.'/'.'pages'.'/'.$page;
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>