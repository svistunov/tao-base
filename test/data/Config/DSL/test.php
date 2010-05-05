<?php
$this->begin('db')->
  dsn('mysql://user:pw@localhost/db')->
end->
begin('cache')->
  dsn('fs://../var/cache/app')->
  timeout(5*60)->
end->
begin('templates')->
  templates_root('../app/views/')->
end->
begin('curl')->
  proxy('http://192.168.5.21:3128')->
end;
?>