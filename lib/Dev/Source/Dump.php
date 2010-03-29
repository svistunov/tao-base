<?php
/// <module name="Dev.Source.CreateXML" maintainer="svistunov@techart.ru" version="0.2.0">
Core::load('CLI.Application', 'IO.FS', 'Dev.Source');

/// <class name="Dev.Source.Dump" stereotype="module">
///   <implements interface="Core.ModuleInterface" />
class Dev_Source_Dump implements Core_ModuleInterface, CLI_RunInterface {
///   <constants>
  const VERSION = '0.2.0';
///   </constants>

///   <protocol name="performing">

///   <method name="main" scope="class">
///     <args>
///       <arg name="argv" type="array" />
///     </args>
///     <body>
  static public function main(array $argv) { Core::with(new Dev_Source_Dump_Application())->main($argv); }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Dev.Source.Dump.Application" extends="CLI.Application.AbstractApplication">
class Dev_Source_Dump_Application extends CLI_Application_AbstractApplication {

///   <protocol name="performing">

///   <method name="run" returns="int">
///     <args>
///       <arg name="argv" type="array" />
///     </args>
///     <body>
  public function run(array $argv) {
    Core::with($this->options['output'] ?
      IO_FS::File($this->options['output'])->open('w+') :
      IO::stdout())->write(Dev_Source::Library($argv)->xml->SaveXML());
    return 0;
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    return parent::setup()->
      usage_text(Core_Strings::format("Dev.Source.Dump %s: TAO module visualization utility\n", Dev_Source_Dump::VERSION))->
      options(
        array(
          array('output',      '-o', '--output',      'string',  null,  'Output file')),
          array('output'=> null));
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>