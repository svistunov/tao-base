<?php
/// <module name="Test.Proc" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Proc', 'IO.FS');

/// <class name="Test.Proc" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Proc implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Proc.', 'PipeCase', 'ProcessCase');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Proc.PipeCase" extends="Dev.Unit.TestCase">
class Test_Proc_PipeCase extends Dev_Unit_TestCase {
  protected $pipe;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->pipe = Proc::Pipe('/bin/ls $TAO_HOME/test/data/Proc/test_folder');
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->assert_equal($this->pipe->load(), "1\n2\n");
    $this->pipe->close();
    $this->assert_equal($this->pipe->exit_status, 0);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Proc.ProcessCase" extends="Dev.Unit.TestCase">
class Test_Proc_ProcessCase extends Dev_Unit_TestCase {
  protected $process;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->process = Proc::Process('/bin/sh')->
      working_dir('test/data/Proc/working_dir')->
      environment(array('KEY_TEST' => 'test'))->
      input()->output()->error()->
      run();
  }
///     </body>
///   </method>

///   <method name="teardown">
///     <body>
  public function teardown() {
    $this->process->close();
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
   $this->assert_true($this->process->is_started());
    $this->process->input->write('echo $KEY_TEST;');
    $this->assert_equal($this->process->get_status()->running, true);
    $this->process->finish_input();
    $this->assert_equal($this->process->output->read_line(), "test\n");
    usleep(10000);
    $this->assert_equal($this->process->get_status()->running, false);
  }
///     </body>
///   </method>

///   <method name="test_accessing">
///     <body>
  public function test_accessing() {
    $this->asserts->accessing->
      assert_read_only($this->process, $ro  = array(
        'command' => '/bin/sh',
        'working_dir' => 'test/data/Proc/working_dir',
        'environment' => array('KEY_TEST' => 'test')
      ))->
      assert_exists_only($this->process, $eo = array(
        'id',  'input',  'output',  'error'
      ))->
      assert_undestroyable($this->process, array_merge(array_keys($ro) + $eo))->
      assert_class('IO.Stream.ResourceStream', $this->process->input)->
      assert_class('IO.Stream.ResourceStream', $this->process->output)->
      assert_class('IO.Stream.ResourceStream', $this->process->error);
  }
///     </body>
///   </method>

///   <method name="test_error">
///     <body>
  public function test_error() {
    $p = Proc::Process('/path/to/spooge')->
      error('test/data/Proc/working_dir/error.txt')->
      run();
    $this->assert_equal(
      IO_FS::File('test/data/Proc/working_dir/error.txt')->load(),
      "sh: /path/to/spooge: not found\n");
    $p->close();
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>