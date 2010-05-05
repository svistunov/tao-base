<?php
/// <module name="Test.DB.ORM.Blog">
Core::load('Test.DB.ORM');

/// <class name="Test.DB.ORM.News" stereotype="module">
///   <implements interface="ModuleInterface" />
class Test_DB_ORM_Blog implements Test_DB_ORM_ModuleInterface {

///   <constants>
  const VERSION = '0.1.1';
///   </constants>

///   <protocol name="building">

///   <method name="mappers" returns="P2.DB.News.MapperSet" scope="class">
///     <args>
///       <arg name="session" type="P2.DB.Session" />
///     </args>
///     <body>
  static public function mappers(Test_DB_ORM_Session $session) { return new Test_DB_ORM_Blog_MapperSet($session); }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="building">

///   <method name="User">
///     <args>
///       <arg name="attrs" type="array" />
///     </args>
///     <body>
  static public function User($attrs) {
    return new Test_DB_ORM_Blog_User($attrs);
  }
///     </body>
///   </method>

///   <method name="Posting">
///     <args>
///       <arg name="attrs" type="array" />
///     </args>
///     <body>
  static public function Posting($attrs) {
    return new Test_DB_ORM_Blog_Posting($attrs);
  }
///     </body>
///   </method>

///   <method name="Tag">
///     <args>
///       <arg name="attrs" type="array" />
///     </args>
///     <body>
  static public function Tag($attrs) {
    return new Test_DB_ORM_Blog_Tag($attrs);
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.MapperSet" extends="DB.ORM.MapperSet">
class Test_DB_ORM_Blog_MapperSet extends DB_ORM_MapperSet {

///   <protocol name="mapping">

///   <method name="map_users" returns="Test.DB.ORM.Blog.UsersMapper" access="protected">
///     <body>
  protected function map_users()    { return new Test_DB_ORM_Blog_UsersMapper($this->session); }
///     </body>
///   </method>

///   <method name="map_postings" returns="Test.DB.ORM.Blog.PostingsMapper" access="protected">
///     <body>
  protected function map_postings() { return new Test_DB_ORM_Blog_PostingsMapper($this->session);}
///     </body>
///   </method>

///   <method name="map_tags" returns="Test.DB.ORM.Blog.TagsMapper" access="protected">
///     <body>
  protected function map_tags()     { return new Test_DB_ORM_Blog_TagsMapper($this->session);}
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.Posting" extends="Test.DB.ORM.Entity">
class Test_DB_ORM_Blog_User extends Test_DB_ORM_Entity {
/// <protocol name="supporting">

///   <method name="get_postings" access="protected" returns="Test.DB.ORM.Blog.UserssMapper">
///     <body>
  protected function get_postings() { return self::db()->blog->postings->for_user($this);}
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.Posting" extends="Test.DB.ORM.Entity">
class Test_DB_ORM_Blog_Posting extends Test_DB_ORM_Entity {

///   <protocol name="supporting">

///   <method name="get_tags" access="protected" returns="Test.DB.ORM.Blog.PostingsMapper">
///     <body>
  protected function get_tags() { return self::db()->blog->tags->for_posting($this);}
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.Tag" extends="Test.DB.ORM.Entity">
class Test_DB_ORM_Blog_Tag extends Test_DB_ORM_Entity {
/// <protocol name="supporting">
///   <method name="get_postings" access="protected" returns="Test.DB.ORM.Blog.TagsMapper">
///     <body>
  protected function get_postings() { return self::db()->blog->postings->for_tag($this);}
///     </body>
///   </method>

///  </protocol>
}
/// </class>


/// <class name="Test.DB.ORM.Blog.UsersDB_ORM_SQLMapper" extends="DB.ORM.Mapper">
class Test_DB_ORM_Blog_UsersMapper extends DB_ORM_SQLMapper {

///   <protocol name="creating">

///   <method name="setup" returns="Test.DB.ORM.Blog.UsersMapper" access="protected">
///     <body>
  protected function setup() {
    return $this->
      classname('Test.DB.ORM.Blog.User')->
      table('users')->
      columns('id', 'name', 'email', 'info')->
      order_by('users.id')->
      key('id');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="map_count_postings" returns="Test.DB.ORM.Blog.UsersMapper" access="protected">
///     <body>
  protected function map_count_postings() {
    return $this->
      calculate(array('count_post' => 'COUNT(postings.id)'))->
      join('inner','postings', 'users.id = postings.user_id')->
      group_by('users.id');
  }
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.PostingsMapper" extends="DB.ORM.Mapper">
class Test_DB_ORM_Blog_PostingsMapper extends DB_ORM_SQLMapper {

///   <protocol name="creating">

///   <method name="setup" returns="Test.DB.ORM.Blog.PostingsMapper" access="protected">
///     <body>
  protected function setup() {
    return $this->
      classname('Test.DB.ORM.Blog.Posting')->
      table('postings')->
      columns('id', 'user_id', 'title', 'published_at', 'body')->
      order_by('id')->
      key('id');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="map_for_user" returns="Test.DB.ORM.Blog.PostingsMapper" access="protected">
///     <body>
  protected function map_for_user(Test_DB_ORM_Blog_User $user) {
    return $this->where('user_id = :id', $user);
  }
///     </body>
///   </method>

///   <method name="map_no_body" returns="Test.DB.ORM.Blog.PostingsMapper" access="protected">
///     <body>
  protected function map_no_body(){
    return $this->exclude('body');
  }
///     </body>
///   </method>

///   <method name="map_for_tag" returns="Test.DB.ORM.Blog.PostingsMapper" access="protected">
///     <body>
  protected function map_for_tag(Test_DB_ORM_Blog_Tag $tag) {
    return $this->
      join('inner', 'tags_postings', 'postings.id = tags_postings.posting_id')->
      where('tags_postings.tag_id = :id', $tag);
  }
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// <class name="Test.DB.ORM.Blog.TagsMapper" extends="DB.ORM.Mapper">
class Test_DB_ORM_Blog_TagsMapper extends DB_ORM_SQLMapper {

///   <protocol name="creating">

///   <method name="setup" returns="Test.DB.ORM.Blog.TagsMapper" access="protected">
///     <body>
  protected function setup() {
    return $this->
      classname('test.DB.ORM.Blog.Tag')->
      table('tags')->
      columns('id', 'name')->
      order_by('id')->
      lookup_by('name')->
      key('id');
  }
///     </body>
///   </method>

///   </protocol>

///   <protocol name="supporting">

///   <method name="map_for_user" returns="Test.DB.ORM.Blog.TagsMapper" access="protected">
///     <body>
  protected function map_for_posting(Test_DB_ORM_Blog_Posting $posting) {
    return $this->
      join('inner', 'tags_postings', 'tags.id = tags_postings.tag_id')->
      where('tags_postings.posting_id = :id', $posting);
  }
///     </body>
///   </method>

/// </protocol>
}
/// </class>

/// </module>
?>