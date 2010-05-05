<?php
$data = array(
  'db_table' => new ArrayObject(array(
    0 => array(
      'body' => 'text text text',
      'title' => 'db test data',
      'date_time' => Time::parse('2008-01-16 22:30:35')
    ),
    1 => array(
      'body' => 'test text field row 2',
      'title' => 'Another db test data',
      'date_time' => Time::parse('2007-02-15 12:30:35')
    ))
));
return $data;
?>