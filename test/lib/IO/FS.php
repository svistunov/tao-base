<?php
/// <module name="Test.IO.FS" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'IO','IO.FS');

/// <class name="Test.IO.FS" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_IO_FS implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.IO.FS.',
      'ModuleCase', 'StatCase', 'PathCase', 'ObjectCase',
       'FileCase', 'DirCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.ModuleCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_ModuleCase extends Dev_Unit_TestCase {
///   <protocol name="testing">

///   <method name="test_pwd_cd">
///     <body>
//TODO: перенести создание и удаление файлов в setup teardown
  public function test_pwd_cd() {
    $dir = IO_FS::pwd();
    $this->
      assert_class('IO.FS.Dir', $dir)->
      assert_equal($dir->real_path, getcwd());

    $cd_path = $dir->real_path.DIRECTORY_SEPARATOR.'test';
    $cd_dir = IO_FS::cd('test');
    $this->
      assert_class('IO.FS.Dir', $cd_dir)->
      assert_equal($cd_dir->real_path, $cd_path);
    IO_FS::cd('../');
  }
///     </body>
///   </method>

///   <method name="test_make_dir">
///     <body>
  public function test_make_dir() {
    $path = 'test/data/IO/FS/newdir/nesteddir';
    $parent_path = 'test/data/IO/FS/newdir';
    $dir = IO_FS::make_nested_dir($path);
    $this->
      assert_class('IO.FS.Dir', $dir)->
      assert_true(IO_FS::is_dir($dir))->
      assert_equal($dir->path, $path)->
      assert_true(IO_FS::rm($path))->
      assert_true(IO_FS::rm($parent_path))->
      assert_false(IO_FS::is_dir($path))->
      assert_false(IO_FS::is_dir($parent_path))->
      assert_false(IO_FS::exists($path))->
      assert_false(IO_FS::exists($parent_path));
  }
///     </body>
///   </method>

///   <method name="test_cp_mv">
///     <body>
  public function test_cp_mv() {
    $path = 'test/data/IO/FS/file.original';
    $cp_path = 'test/data/IO/FS/file.cp';
    $mv_path = 'test/data/IO/FS/file.mv';

    IO_FS::FileStream($path, 'w+');

    $this->
      assert_true(IO_FS::cp($path, $cp_path))->
      assert_true(IO_FS::exists($path))->
      assert_true(IO_FS::exists($cp_path))->
      assert_true(IO_FS::mv($path, $mv_path))->
      assert_false(IO_FS::exists($path))->
      assert_true(IO_FS::exists($mv_path))->
      assert_true(IO_FS::rm($cp_path))->
      assert_true(IO_FS::rm($mv_path))->
      assert_false(IO_FS::exists($mv_path))->
      assert_false(IO_FS::exists($cp_path));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.StatusCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_StatCase extends Dev_Unit_TestCase {
  protected $path;
  protected $stat;
///   <protocol name="testing">

///   <method name="setup">
///     <body>
  public function setup() {
    $this->path = 'test/data/IO/FS/stat.file';
    $f = IO_FS::File($this->path);
    $f->open('w+');
    $f->close();
    $this->stat = $f->stat;
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->
      assert_class('IO.FS.Stat', $this->stat)->
      assert_class('Time.DateTime', $this->stat->atime)->
      assert_class('Time.DateTime', $this->stat->mtime)->
      assert_class('Time.DateTime', $this->stat->ctime)->
      asserts->accessing->
        assert_exists_only($this->stat, $o = array(
          'dev', 'ino', 'mode', 'nlink', 'uid', 'gid', 'rdev',
          'size', 'atime', 'mtime', 'ctime', 'blksize', 'blocks'
        ))->
        assert_undestroyable($this->stat, $o)->
        assert_missing($this->stat);
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    if (IO_FS::exists($this->path)) IO_FS::rm($this->path);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.PathCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_PathCase extends Dev_Unit_TestCase {

///   <protocol name="testing">

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $path = IO_FS::Path('test/data/IO/FS/path.file');

    $this->
      assert_class('IO.FS.Path', $path)->
      asserts->accessing->
        assert_read_only($path, $o = array(
          'dirname' => 'test/data/IO/FS',
          'basename' => 'path.file',
          'extension' => 'file',
          'filename' => 'path'
        ))->
        assert_undestroyable($path, array_keys($o))->
        assert_missing($path);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.ObjectCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_ObjectCase extends Dev_Unit_TestCase {
  protected $path;
///   <protocol name="testing" >

///   <method name="setup">
///     <body>
  public function setup() {
    $this->path = 'test/data/IO/FS/obj.file';
    IO_FS::FileStream($this->path, 'w+');
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $obj = new IO_FS_FSObject($this->path);
    $this->
      assert_class('IO.FS.FSObject', $obj)->
      assert_class('IO.FS.Stat', $obj->stat)->
      asserts->accessing->
        assert_read_only($obj, $r = array(
          'path' => 'test/data/IO/FS/obj.file',
          'dir_name' => 'test/data/IO/FS',
          'name' => 'obj.file'
        ))->
        assert_exists_only($obj, $e = array('real_path'))->
        assert_missing($obj)->
        assert_undestroyable($obj, array_keys($r) + array_keys($e));
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    if (IO_FS::exists($this->path)) IO_FS::rm($this->path);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.FileCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_FileCase extends Dev_Unit_TestCase {
  protected $file;
  protected $path;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->path = 'test/data/IO/FS/file_case.txt';
    $this->file = IO_FS::File($this->path);
    $this->file->open('w+b');
  }
///     </body>
///   </method>

///   <method name="test_stream">
///     <body>
  public function test_stream() {
    $stream = $this->file->stream;
    $stream->write("1 Line\n");
    $stream->format("%d %s\n", 2, 'Line');
    $stream->write("3 Line\n");

    $stream->seek(0, SEEK_SET);
    $stream->text();
    $this->assert_equal($stream->tell(), 0);

    foreach ($stream as $k=>$v)
      $this->assert_equal($v, "$k Line\n");

    $this->assert_equal($stream->tell(), $this->file->size);

    $stream->truncate(strlen("1 Line"));
    $this->assert_equal($this->file->load(), '1 Line');
  }
///     </body>
///   </method>

///   <method name="test_performing">
///     <body>
  public function test_performing() {
    $this->file->update('Write to file');
    $this->assert_equal($this->file->load(), 'Write to file');

    $this->file->append("\nAppend text");
    $this->assert_equal($this->file->load(), "Write to file\nAppend text");
  }
///     </body>
///   </method>

///   <method name="test_properties">
///     <body>
  public function test_accessing() {
    $this->
      assert_class('IO.FS.FileStream', $this->file->stream)->
      asserts->accessing->
        assert_read_only($this->file, array(
          'path' => $this->path,
          'size' => 0,
          'mime_type' => MIME::type('text/plain'),
          'content_type' => MIME::type('text/plain')->type
        ))->
        assert_missing($this->file);
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  protected function teardown() {
    $this->file->close();
    IO_FS::rm($this->path);
  }
///     </body>
///   </method>
///   </protocol>
}
/// </class>

/// <class name="Test.IO.FS.DirCase" extends="Dev.Unit.TestCase">
class Test_IO_FS_DirCase extends Dev_Unit_TestCase {

  protected $dir;
  protected $dir_path;
  protected $sub_dir_path;

///   <protocol name="testing">

///   <method name="setup">
///     <body>
  protected function setup() {
    $this->dir_path = 'test/data/IO/FS/test_dir/';
    $this->sub_dir_path = 'test/data/IO/FS/test_dir/sub_dir/';
    $this->dir = IO_FS::Dir($this->dir_path);
    IO_FS::make_nested_dir($this->sub_dir_path, 0777);
    IO_FS::FileStream($this->dir->path.'/test1.dat', 'w+b');
    IO_FS::FileStream($this->dir->path.'/test2.dat', 'w+b');
    IO_FS::FileStream($this->dir->path.'/sub_dir/test3.html', 'w+b');
  }
///     </body>
///   </method>

///   <method name="test_indexing">
///     <body>
  public function test_indexing() {
    $this->asserts->indexing->
      assert_read_only($this->dir, $o = array(
        'sub_dir' => IO_FS::Dir($this->sub_dir_path),
        'test1.dat' => IO_FS::File($this->dir_path.'/test1.dat'),
        'test2.dat' => IO_FS::File($this->dir_path.'/test2.dat')
      ))->
      assert_missing($this->dir)->
      assert_undestroyable($this->dir, array_keys($o));
  }
///     </body>
///   </method>

///   <method name="test_iteration">
///     <body>
  public function test_iteration() {
    foreach ($this->dir as $k => $v)
      switch ($k) {
        case $p = 'test/data/IO/FS/test_dir/test2.dat':
        case $p = 'test/data/IO/FS/test_dir/sub_dir':
        case $p = 'test/data/IO/FS/test_dir/test1.dat':
          $this->
            assert_class('IO.FS.FSObject', $v)->
            assert_equal($v, IO_FS::file_object_for($p));
          break;
        default:
          $this->assert_true(false);
      }
  }
///     </body>
///   </method>

///   <method name="test_quering">
///     <body>
  public function test_quering() {
    $query = IO_FS::Query();
    $query->recursive = true;
    $query->glob('*.dat');

    $this->
      assert_equal($query->regexp, '{.*\.dat}')->
      assert_true($query->is_recursive());

    foreach ($query->apply_to($this->dir) as $k => $v)
      switch ($k) {
        case $p ='test/data/IO/FS/test_dir/test2.dat':
        case $p ='test/data/IO/FS/test_dir/test1.dat':
          $this->
            assert_class('IO.FS.File', $v)->
            assert_true($query->allows(IO_FS::Path($p)->basename))->
            assert_equal($v, IO_FS::File($p));
          break;
        default:
          $this->assert_true(false) ;
      }

    foreach ($query->regexp('/.*\.html/')->apply_to($this->dir) as $k => $v)
      switch ($k) {
        case $p = 'test/data/IO/FS/test_dir/sub_dir/test3.html':
          $this->
            assert_class('IO.FS.File', $v)->
            assert_true($query->allows(IO_FS::Path($p)->basename))->
            assert_equal($v, IO_FS::File($p));
          break;
        default:
          $this->assert_true(false) ;
      }


  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  protected function teardown() {
    IO_FS::rm($this->dir->path.'/test1.dat');
    IO_FS::rm($this->dir->path.'/test2.dat');
    IO_FS::rm($this->dir->path.'/sub_dir/test3.html');
    IO_FS::rm($this->dir->path.'/sub_dir');
    IO_FS::rm($this->dir->path);

  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>