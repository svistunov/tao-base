<?php
/// <module name="Test.Data.Pagination" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Data.Pagination');

/// <class name="Test.Data.Pagination" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Data_Pagination implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Data.Pagination.', 'PagerCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Data.Pagination.Case" extends="Dev.Unit.TestCase">
class Test_Data_Pagination_PagerCase extends Dev_Unit_TestCase {
  protected $pager;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->pager = Data_Pagination::Pager(53, 5, 7);
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_undestroyable($this->pager, range(1,8))->
      assert_missing($this->pager, 9)->
      assert_read_only($this->pager, array(
        1 => new Data_Pagination_Page($this->pager, 1),
        2 => new Data_Pagination_Page($this->pager, 2),
        3 => new Data_Pagination_Page($this->pager, 3),
        4 => new Data_Pagination_Page($this->pager, 4),
        5 => new Data_Pagination_Page($this->pager, 5),
        6 => new Data_Pagination_Page($this->pager, 6),
        7 => new Data_Pagination_Page($this->pager, 7),
        8 => new Data_Pagination_Page($this->pager, 8)
      ));
  }
///     </body>
///   </method>

///   <method name="test_pages">
///     <body>
  public function test_pages() {
    $this->
      assert_class('Data.Pagination.Page', $this->pager->first)->
      assert_equal($this->pager->first, new Data_Pagination_Page($this->pager, 1))->
      assert_class('Data.Pagination.Page', $this->pager->last)->
      assert_equal($this->pager->last, new Data_Pagination_Page($this->pager, 8))->
      assert_class('Data.Pagination.Page', $this->pager->current)->
      assert_equal($this->pager->current, new Data_Pagination_Page($this->pager, 5))
    ;
  }
///     </body>
///   </method>

///   <method name="test_array_cache">
///     <body>
  public function test_array_cache() {
    $this->
      assert_equal(0, $this->pager->length);
    $first = $this->pager[1];
    $this->
      assert_equal(1, $this->pager->length)->
      assert_equal(1, count($this->pager));
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->pager, array(
        'num_of_items' => 53,
        'num_of_pages' => 8,
        'items_per_page' => 7,
        'length' => 0,
        'last' => new Data_Pagination_Page($this->pager, 8),
        'first' => new Data_Pagination_Page($this->pager, 1),
        'current' => new Data_Pagination_Page($this->pager, 5)
      ))->
      assert_missing($this->pager)->
      assert_undestroyable($this->pager, array(
        'num_of_items', 'num_of_pages',
        'items_per_page', 'length',
        'last', 'first', 'current'
      ));
    ;
  }
///     </body>
///   </method>

///   <method name="test_page_properties">
///     <body>
  public function test_page_accessing() {
    $page = $this->pager->current;

    $this->asserts->accessing->
      assert_read_only($page, array(
        'pager' => $this->pager,
        'first_item' => 29,
        'last_item' => 35,
        'offset' => 28,
        'previous' => $this->pager[4],
        'next' => $this->pager[6],
        'is_first' => false,
        'is_last' => false,
        'number' => 5
      ))->
      assert_missing($page)->
      assert_undestroyable($page, array(
        'pager', 'first_item',
        'last_item', 'offset',
         'previous', 'next',
         'is_first', 'is_last', 'number'));
  }
///     </body>
///   </method>

///   <method name="test_window_properties">
///     <body>
  public function test_window_accessing() {
    $window = $this->pager->current->window(2);

    $this->assert_equal(count($window), 5);
    $this->asserts->accessing->
      assert_read_only($window, array(
        'pager' => $this->pager,
        'first' => $this->pager[3],
        'last' => $this->pager[7],
        'pages' => new ArrayObject(array(
          3 => $this->pager[3],
          4 => $this->pager[4],
          5 => $this->pager[5],
          6 => $this->pager[6],
          7 => $this->pager[7]))
      ))->
      assert_write($window, array('padding' => 1))->
      assert_read($window, array('padding' => 1))->
      assert_missing($window)->
      assert_undestroyable($window, array(
        'pager', 'first', 'last', 'pages', 'padding'
      ));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>