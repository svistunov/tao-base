<?php

/// <module name="Test.Templates.HTML" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Templates', 'Templates.HTML');

/// <class name="Test.Templates.HTML" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Templates_HTML implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Templates.HTML.', 'TemplateCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.HelperForModule" >
///   <implements interface="Templates.HelperInterface" />
class Test_Templates_HTML_HelperForTemplatesModule implements Templates_HelperInterface {

///   <protocol name="performing" >

///   <method name="say_hello_for" returns="string">
///     <args>
///       <arg name="t" type="Templates.Template" />
///       <arg name="name" type="string" />
///     </args>
///     <body>
  public function say_hello_for(Templates_Template $t, $name) {
    return 'Hello, '.$name."\n";
  }
///     </body>
///   </method>

///   <method name="say_hello" returns="string">
///     <args>
///       <arg name="t" type="Templates.Template" />
///     </args>
///     <body>
  public function say_hello(Templates_Template $t) {
    return "Hello\n";
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.Templates.HelperForModule" >
///   <implements interface="Templates.HelperInterface" />
class Test_Templates_HTML_HelperForTemplatesHTMLModule implements Templates_HelperInterface {

///   <protocol name="performing" >

///   <method name="say_hello_for" returns="string">
///     <args>
///       <arg name="t" type="Templates.Template" />
///     </args>
///     <body>
  public function say_hello_for(Templates_Template $t, $name) {
    return '<strong>Hello</strong>, '.$name."\n";
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Tempates.HelperForIntstance" >
///   <implements interface="Templates.HelperInterface" />
class Test_Tempates_HTML_HelperForIntstance implements Templates_HelperInterface {
///   <protocol name="performing" >

///   <method name="say_hey_hey_hey" returns="string">
///     <args>
///       <arg name="t" type="Templates.Template" />
///     </args>
///     <body>
  public function say_hey_hey_hey(Templates_Template $t) {
    return "Hey Hey Hey!\n";
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.Tempates.AnotherHelperForIntstance" >
///   <implements interface="Templates.HelperInterface" />
class Test_Tempates_HTML_AnotherHelperForIntstance implements Templates_HelperInterface {
///   <protocol name="performing" >

///   <method name="say_bye_bye" returns="string">
///     <args>
///       <arg name="t" type="Templates.Template" />
///     </args>
///     <body>
  public function say_bye_bye(Templates_Template $t) {
    return "Bye-bye\n";
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.HTML.TemplateCase" extends="Dev.Unit.TestCase">
class Test_Templates_HTML_TemplateCase extends Dev_Unit_TestCase {
  protected $template;
  protected $outer_template;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    Templates::option('templates_root', 'test/data/Templates');
    $this->template = Templates::HTML('test')->
      inside($this->outer_template = Templates::HTML('outer'));
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_write($this->template, $w = array(
        'key1' => 'value1',
        'key2' => 'value2'
      ))->
      assert_read($this->template, $w);
  }
///     </body>
///   </method>

///   <method name="test_helpers">
///     <body>
 public function test_helpers() {
    Templates::use_helpers(new Test_Templates_HTML_HelperForTemplatesModule());
    Templates_HTML::use_helpers(new Test_Templates_HTML_HelperForTemplatesHTMLModule());
    $this->outer_template->use_helpers(new Test_Tempates_HTML_HelperForIntstance());
    $this->template->use_helpers(new Test_Tempates_HTML_AnotherHelperForIntstance());

    $name = 'Petya';
    $this->
      assert_equal(
        "Hello\n",
        $this->template->say_hello()
      )->
      assert_equal(
        '<strong>Hello</strong>, '.$name."\n",
        $this->template->say_hello_for('Petya')
      )->
      assert_equal(
        "Hey Hey Hey!\n",
        $this->template->say_hey_hey_hey()
      )->
      assert_equal(
        "Bye-bye\n",
        $this->template->say_bye_bye()
      );
  }
///     </body>
///   </method>

///   <method name="test_begin_end">
///     <body>
  public function test_begin_end() {
    $this->template->begin('name');
    print('content');
    $this->template->end('name');

    $this->template->begin('name', true);
    print(' appended');
    $this->template->end('name');

    $this->assert_equal($this->template['name'], 'content appended');
  }
///     </body>
///   </method>

///   <method name="test_content">
///     <body>
  public function test_content() {
    $this->template->
      content('name', 'content')->
      content('name', 'prepend ')->
      prepend_to('name', 'prepend_to ')->
      content('name', ' append', false)->
      append_to('name', ' append_to')->
      title('test title')->
      title('new title')->
      description('test description')->
      description('new description');

    $this->
      assert_equal(
        $this->template['name'],
        'prepend_to prepend content append append_to'
      )->
      assert_equal(
        $this->template['title'],
        'test title'
      )->
      assert_equal(
        $this->template['description'],
        'test description'
      );
  }
///     </body>
///   </method>

///   <method name="test_tags">
///     <body>
  public function test_tags() {
    $this->
      assert_equal($this->template->tag('div'), '<div />')->
      assert_equal(
        $this->template->tag(
          'input',
          array(
            'type' => 'radio',
            'checked' => true
          ),
          false),
        '<input type="radio" checked >')->
      assert_equal(
        $this->template->tag(
          'input',
          array(
            'type' => 'radio',
            'checked' => false
          )),
        '<input type="radio" />')->
      assert_equal(
        $this->template->content_tag(
          'div',
          'content',
          array(
            'class' => 'article',
          )),
        '<div class="article">content</div>')->
      assert_equal(
        $this->template->link_to(
          'http://www.techart.ru/',
          'link',
          array(
            'target' => '_blank'
          )
        ),
        '<a target="_blank" href="http://www.techart.ru/">link</a>')->
      assert_equal(
        $this->template->mail_to(
          'test@techart.ru',
          'mail'
        ),
        '<a href="mailto:test@techart.ru">mail</a>')->
      assert_equal(
        $this->template->button_to(
          'http://www.techart.ru/',
          'button link'
        ),
        '<form action="http://www.techart.ru/" method="get"><button type="submit">button link</button></form>')->
      assert_equal(
        $this->template->button_to(
          'http://www.techart.ru/',
          'button link',
          'delete',
          array('confirm' => 'Delete?')
        ),
        '<form action="http://www.techart.ru/" method="post"><input type="hidden" name="_method" value="delete" /><button type="submit" onclick="return confirm(\'Delete?\');">button link</button></form>')->
      assert_equal(
        $this->template->form_button_to(
          'http://www.techart.ru/',
          'button link'
        ),
        '<input value="button link" type="submit" onclick="this.form.action=\'http://www.techart.ru/\';this.form.method=\'get\';this.form.elements._method.value=\'get\';return true;" />')->
      assert_equal(
        $this->template->form_button_to(
          'http://www.techart.ru/',
          'button link',
          'delete',
          array('confirm' => 'Delete?')
        ),
        '<input value="button link" type="submit" onclick="this.form.action=\'http://www.techart.ru/\';this.form.method=\'post\';this.form.elements._method.value=\'delete\';return confirm(\'Delete?\');" />')->
      assert_equal(
        $this->template->image('/assets/img/logo.gif'),
        '<img src="/assets/img/logo.gif" />'
      )->
      assert_equal(
        $this->template->js_link('init.js'),
        "<script src=\"/assets/js/init.js\"></script>\n"
      )->
      assert_equal(
        $this->template->css_link('common.css'),
        "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/assets/css/common.css\" />\n"
      );
  }
///     </body>
///   </method>

///   <method name="test_js_css_blocks">
///     <body>
  public function test_js_css_blocks() {
    $this->template->
      js('init.js', 'common.js')->
      css('site.css', 'icons.css');
    $this->asserts->indexing->
      assert_read($this->template, array(
        'js' => "<script src=\"/assets/js/init.js\"></script>\n<script src=\"/assets/js/common.js\"></script>\n",
        'css' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/assets/css/site.css\" />\n".
                 "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/assets/css/icons.css\" />\n"
      ));
  }
///     </body>
///   </method>

///   <method name="test_partial">
///     <body>
  public function test_partial() {
    IO_FS::File(Templates::option('templates_root').'/partial.phtml', 'w+')->update(
'<h3><?= $header ?></h3>'.
'<ul>'.
'<?php foreach ($elements as $e) {?>'.
'<?= $this->content_tag("li", $e) ?>'.
'<?php } ?>'.
'</ul>'
    );

    $this->template->with('elements', array('element1', 'element2'));
    $this->
      assert_equal(
        $this->template->partial('partial', array('header' => 'Test')),
        $r = '<h3>Test</h3><ul><li>element1</li><li>element2</li></ul>'
      )->
      assert_equal(
        $this->template->compose(array('partial', array('header' => 'Test')), 'New line'),
        '<h3>Test</h3><ul><li>element1</li><li>element2</li></ul>New line'
      );

    $cache = Cache::connect('memcache://localhost:11211');
    $this->
      assert_equal(
        $this->template->cached_partial($cache, 'partial', array('header' => 'Test'), 'key'),
        $r
      )->
      assert_equal(
        $cache['key'],
        $r
      );
  }
///     </body>
///   </method>

///   <method name="test_render">
///     <body>
  public function test_render() {
    $data = <<<TXT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Test page for Tets.Templates</title>

  <link title="News" type="application/rss+xml" rel="alternate" href="/news/index.rss"/>
  <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/common.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/assets/css/site.css" />
<link rel="stylesheet" type="text/css" media="screen" href="/assets/css/site/news.css" />

  <script src="/assets/js/jquery.js"></script>

  <script>
    $(function() {alert("test");});
  </script>
</head>


<body>
  <div id="header">
  Something in site head from parameter \$head: Test page  </div>

  <div id="content">
    Main part. From nested template.




For exalmpe list of news
  <ul>
    <li>first</li>    <li>second</li>  </ul>
Parametres from parent template: \$head -  Test page   </div>

    <div id="footer">
      Insert footer from partial template with own parametres
      <span>Copyright: TAO</span>  </div>
</body>
</html>
TXT
;
    $template = Templates::HTML('news')->with(array('news' => array('first', 'second')))->
      inside(Templates::HTML('site')->with(array('head' => 'Test page')));
    $this->assert_same(
      $template->render(),
      $data
    );
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    Templates::option('templates_root', '.');
    if (IO_FS::exists($path = Templates::option('templates_root').'/partial.phtml')) IO_FS::rm($path);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>

?>