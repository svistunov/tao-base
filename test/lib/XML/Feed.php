<?php
/// <module name="Test.XML.Feed" version="0.1.1" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'XML.Feed');

/// <class name="Test.XML.Feed" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_XML_Feed implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.XML.Feed.', 'ParserCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.XML.Feed.Agent">
///   <implements interface="Net.HTTP.AgentInterface" />
class Test_XML_Feed_Agent implements Net_HTTP_AgentInterface {
  public $called_methods;

///   <protocol name="performing">

///   <method name="send" returns="Net.HTTP.Response">
///     <args>
///       <arg name="request" type="Net.HTTP.Request" brief="запрос" />
///     </args>
///     <body>
  public function send(Net_HTTP_Request $request) {
    $this->called_methods['send'][] = array('request' => $request);
    return Net_HTTP::Response(@file_get_contents('./test/data/XML/Feed/rss20.xml'));
  }
///     </body>
///   </method>

///   </protocol>

}
/// </class>

/// <class name="Test.XML.Feed.ParserCase" extends="Dev.Unit.TestCase">
class Test_XML_Feed_ParserCase extends Dev_Unit_TestCase {

  protected $parser;

///   <protocol name="testing">

///   <method name="test_rss20">
///     <body>
  public function test_rss20() {
    $feed = XML_Feed::load('./test/data/XML/Feed/rss20.xml');

    $this->
      assert_class('XML.Feed.Feed', $feed)->
      assert_class('DOMDocument',   $feed->document)->
      assert_class('XML.Feed.RSS',  $feed->protocol)->
      assert_equal('rss 2.0',       $feed->protocol->version);


    $this->asserts->accessing->
      assert_read_only($feed, array(
        'title' => 'Sample Feed',
        'description' => 'For documentation <em>only</em>',
        'link' => 'http://example.org/',
        'published_at' => Time::DateTime('2002-09-07 04:00:01'),
        'language' => 'en',
        'copyright' => 'Copyright 2004, Mark Pilgrim',
        'managing_editor' => 'editor@example.org',
        'web_master' => 'webmaster@example.org',
        'pub_date' => Time::DateTime('2002-09-07 04:00:01'),
        'category' => '1024',
      ))->
      assert_read_only($feed->image, array(
        'url' => 'http://example.org/banner.png',
        'title' => 'Example banner',
        'link' => 'http://example.org/',
        'width' => '80',
        'height' => '15'
      ))->
      assert_equal(count($feed), 1)->
      assert_equal($feed->get('category', 1), 'Top/Society/People/Personal_Homepages/P/')->
      assert_equal($feed->get('category/@domain'), 'Syndic8')->
      assert_read_only( $feed[0], array(
        'title' => 'First item title',
        'link' => 'http://example.org/item/1',
        'description' => 'Watch out for <span style="background: url(javascript:window.location=\'http://example.org/\')">nasty tricks</span>',
        'pub_date' => Time::DateTime('2002-09-05 04:00:01')
      ));

    $this->asserts->iterating->assert_read($feed, array($feed[0]));
  }
///     </body>
///   </method>

///   <method name="test_atom10">
///     <body>
  public function test_atom10() {

    $feed = XML_Feed::load('./test/data/XML/Feed/atom10.xml');

    $this->
      assert_class('XML.Feed.Feed', $feed)->
      assert_class('DOMDocument',   $feed->document)->
      assert_class('XML.Feed.Atom', $feed->protocol)->
      assert_equal($feed->protocol->version,  'atom 1.0');

    $this->asserts->accessing->
      assert_read_only($feed, array(
        'title' => 'Sample Feed',
        'link' => 'http://example.org/',
        'description' => 'For documentation <em>only</em>',
        'published_at' => Time::DateTime('2005-11-09 14:56:34'),
        'updated' => Time::DateTime('2005-11-09 14:56:34')))->
      assert_equal(count($feed), 1)->
      assert_read_only($feed[0], array(
        'title' => 'First entry title',
        'link' => 'http://example.org/entry/3',
        'description' => '<div>Watch out for <span style="background: url(javascript:window.location=\'http://example.org/\')"> nasty tricks</span></div>',
        'published' => Time::DateTime('2005-11-09 03:23:47')
      ));

     $this->asserts->iterating->assert_read($feed, array($feed[0]));
  }
///     </body>
///   </method>

///   <method name="test_rss10">
///     <body>
  public function test_rss10() {
    $feed = XML_Feed::load('./test/data/XML/Feed/rss10.xml');

    $this->
      assert_class('XML.Feed.Feed', $feed)->
      assert_class('DOMDocument', $feed->document)->
      assert_class('XML.Feed.RSS', $feed->protocol)->
      assert_equal($feed->protocol->version, 'rss 1.0');

    $this->asserts->accessing->
      assert_read_only($feed, array(
        'title' => 'Sample Feed',
        'link' => 'http://www.example.org/',
        'description' => 'For documentation only',
        'published_at' => Time::DateTime('2004-06-05 02:40:33'),
        'dc:date' => Time::DateTime('2004-06-05 02:40:33')))->
      assert_equal(count($feed), 1)->
      assert_read_only($feed[0], array(
        'title' => 'First of all',
        'link' => 'http://example.org/archives/2002/09/04.html#first_of_all',
        'description' => 'Americans are fat. Smokers are stupid. People who don\'t speak Perl are irrelevant.',
        'dc:date' => Time::DateTime('2004-05-31 00:23:54')));

     $this->asserts->iterating->assert_read($feed, array($feed[0]));
  }
///     </body>
///   </method>

///   <method name="test_atom03">
///     <body>
  public function test_atom03() {
    $feed = XML_Feed::load('./test/data/XML/Feed/atom03.xml');

    $this->
      assert_class('XML.Feed.Feed', $feed)->
      assert_class('DOMDocument', $feed->document)->
      assert_class('XML.Feed.Atom', $feed->protocol)->
      assert_equal($feed->protocol->version, 'atom 0.3');

    $this->asserts->accessing->
      assert_read($feed, array(
        'title' => 'Sample Feed',
        'link' => 'http://example.org/',
        'description' => '<div><p>This is an Atom syndication feed.</p></div>',
        'published_at' => Time::DateTime('2004-04-20 15:56:34'),
        'modified' => Time::DateTime('2004-04-20 15:56:34')))->
      assert_equal(count($feed), 1)->
      assert_read_only($feed[0], array(
        'title' => 'First entry title',
        'link' => 'http://example.org/entry/3',
        'description' => '<div>Watch out for <span style="background-image: url(javascript:window.location=\'http://example.org/\')"> nasty tricks</span></div>',
        'modified' => Time::DateTime('2004-04-20 15:56:34')));

     $this->asserts->iterating->assert_read($feed, array($feed[0]));
  }
///     </body>
///   </method>

///   <method name="test_url">
///     <body>
  public function test_url() {
    $agent = new Test_XML_Feed_Agent();
    $feed  = XML_Feed::fetch('http://example.org/index.rss', $agent);

    $this->
      assert_equal(
        $agent->called_methods,
        array('send' => array(array('request' => Net_HTTP::Request($url)))))->
      assert_equal($feed->title, 'Sample Feed');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>