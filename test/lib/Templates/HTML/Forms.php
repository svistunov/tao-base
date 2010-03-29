<?php
/// <module name="Test.Templates.HTML.Froms" version="0.1.0" maintainer="svistunov@techart.ru">
Core::load('Dev.Unit', 'Templates', 'Templates.HTML.Forms');

/// <class name="Test.Templates.HTML.Froms" stereotype="module">
///   <implements interface="Dev.Unit.TestModuleInterface" />
class Test_Templates_HTML_Forms implements Dev_Unit_TestModuleInterface {

///   <constants>
  const VERSION = '0.1.0';
///   </constants>

///   <protocol name="testing">

///   <method name="suite" returns="Dev.Unit.TestSuite" scope="class">
///     <body>
  static public function suite() {
    return Dev_Unit::load_with_prefix('Test.Templates.HTML.Forms.', 'Case');
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// <class name="Test.Templates.HTML.Froms.Case" extends="Dev.Unit.TestCase">
class Test_Templates_HTML_Forms_Case extends Dev_Unit_TestCase {
  protected $template;
  protected $form;
  protected $forms_helper;
  protected $validator;
  protected $request;
///   <protocol name="testing">

///   <method name="setup" access="protected">
///     <body>
  protected function setup() {
    $this->forms_helper = Templates_HTML_Forms::instance();
    $this->template = Templates::HTML('name');
    $this->form = Forms::Form('create')->
      multipart()->
      method(Net_HTTP::PUT)->
      action('do.php')->
      begin_fields->
        input('login')->
        password('password')->
        checkbox('drink')->
        datetime('birsday', Time::parse('1900-12-31 00:00:00'))->
        object_select('category', $items = array(
          (object) array('id' => 1, 'title' => 'cetagory1'),
          (object) array('id' => 2, 'title' => 'cetagory2'),
          (object) array('id' => 3, 'title' => 'cetagory3')
        ))->
        object_multi_select('categories', $items)->
        select('color', array('red', 'green', 'blue'))->
        upload('attach')->
        textarea('body')->
      end->
      validate_with(
        $this->validator = Validation::Validator()->
          validate_presence_of('login', 'Empty field name')->
          validate_presence_of('password', 'Empty field password')
      );
      $this->request = Net_HTTP::Request('http://localhost/entity/create.html');
      $this->request['create'] = array(
        'login' => 'test login',
        'password' => '',
        'birsday' => array('day' => '31', 'month' => '12', 'year' => '1980', 'hour' => '23', 'minute' => '59')
      );
  }
///     </body>
///   </method>

///   <method name="test_all">
///     <body>
 public function test_all() {
    $this->
      assert_false($this->form->process($this->request))->
      assert_equal(
        $this->forms_helper->begin_form($this->template, $this->form),
          '<form action="do.php" method="post" id="create_form" enctype="multipart/form-data">'.
          '<fieldset>'.
          '<input type="hidden" name="_method" value="put" />'
      )->
      assert_equal(
        $this->forms_helper->begin_fieldset($this->template, array('legend' => 'create form')),
        '<fieldset><legend><span>create form</span></legend>'
      )->
      assert_equal(
        $this->forms_helper->end_fieldset($this->template),
        '</fieldset>'
      )->
      assert_equal(
        $this->forms_helper->begin_field($this->template, 'login'),
        '<div class=" field" id="field_create_login">'
      )->
      assert_equal(
        $this->forms_helper->end_field($this->template, 'login'),
        '</div>'
      )->
      assert_equal(
        $this->forms_helper->begin_field($this->template, 'password'),
        '<div class=" field error" id="field_create_password">'
      )->
      assert_equal(
        $this->forms_helper->end_field($this->template, 'pssword'),
        '<p class="error">Empty field password</p></div>'
      )->
      assert_equal(
        $this->forms_helper->field($this->template, 'login', 'input', array('label' => 'Login Name', 'help' => 'Enter you login name')),
        '<div class=" field" id="field_create_login">'.
        '<label for="create_login">Login Name</label>'.
        '<input type="text" name="create[login]" value="test login" id="create_login" />'.
        '<p class="help">Enter you login name</p></div>'
      )->
      assert_equal(
        $this->forms_helper->field(
          $this->template,
          'password',
          array('password', array('class' => 'wide')),
          array('label' => 'Password', 'help' => 'Enter you password')),
        '<div class=" field error" id="field_create_password">'.
        '<label for="create_password">Password</label>'.
        '<input type="password" name="create[password]" id="create_password" class="wide" />'.
        '<p class="help">Enter you password</p>'.
        '<p class="error">Empty field password</p></div>'
      )->
      assert_equal(
        $this->forms_helper->fields($this->template,
          array('login', 'input', array('label' => 'Login Name', 'help' => 'Enter you login name')),
          array('password', 'password', array('label' => 'Password'))
        ),
        '<div class=" field" id="field_create_login">'.
          '<label for="create_login">Login Name</label>'.
          '<input type="text" name="create[login]" value="test login" id="create_login" />'.
          '<p class="help">Enter you login name</p>'.
        '</div>'.
        '<div class=" field error" id="field_create_password">'.
          '<label for="create_password">Password</label>'.
          '<input type="password" name="create[password]" id="create_password" />'.
          '<p class="error">Empty field password</p>'.
        '</div>'
      )->
      assert_equal(
        $this->forms_helper->help($this->template, 'Help text', array('style' => 'color: green;')),
        '<p style="color: green;" class="help">Help text</p>'
      )->
      assert_equal(
        $this->forms_helper->checkbox($this->template, 'drink', array('label' => 'Drink are you?')),
        '<label class="inline">'.
          '<input type="checkbox" value="1" name="create[drink]" id="create_drink" />'.
          '&nbsp;Drink are you?'.
        '</label>'
      )->
      assert_equal(
        $this->forms_helper->radio($this->template, 'drink', array('label' => 'Beer')),
        '<label class="inline"><input type="radio" value="1" name="create[drink]" id="create_drink" />&nbsp;Beer</label>'
      )->
      assert_equal(
        $this->forms_helper->label($this->template, 'login', 'Login'),
        '<label for="create_login">Login</label>'
      )->
      assert_equal(
        $this->forms_helper->input($this->template, 'login', array('calss' => 'wide')),
        '<input type="text" name="create[login]" value="test login" id="create_login" calss="wide" />'
      )->
      assert_equal(
        $this->forms_helper->input_tag($this->template, 'create[login]', 'test value', array('id' => 'create_login', 'calss' => 'wide')),
        '<input type="text" name="create[login]" value="test value" id="create_login" calss="wide" />'
      )->
      assert_equal(
        $this->forms_helper->textarea($this->template, 'body', array('class' => 'description')),
        '<textarea name="create[body]" id="create_body" class="description"></textarea>'
      )->
      assert_equal(
        $this->forms_helper->submit($this->template, 'Send'),
        '<div class="submit"><button class="submit" type="submit">Send</button></div>'
      )->
      assert_equal(
        $this->forms_helper->password($this->template, 'password', array('class' => 'important')),
        '<input type="password" name="create[password]" id="create_password" class="important" />'
      )->
      assert_equal(
        $this->forms_helper->upload($this->template, 'attach', array('class' => 'file')),
        '<input type="file" name="create[attach]" id="create_attach" class="file" />'
      )->
      assert_equal(
        $this->forms_helper->hidden($this->template, 'login'),
        '<input type="hidden" name="create[login]" value="test login" id="create_login" />'
      )->
      assert_equal(
        $this->forms_helper->datetime_select($this->template, 'birsday', array('show_time' => true)),
        '<select name="create[birsday][day]" id="create_birsday_day">'.
          '<option value="1">01</option>'.
          '<option value="2">02</option>'.
          '<option value="3">03</option>'.
          '<option value="4">04</option>'.
          '<option value="5">05</option>'.
          '<option value="6">06</option>'.
          '<option value="7">07</option>'.
          '<option value="8">08</option>'.
          '<option value="9">09</option>'.
          '<option value="10">10</option>'.
          '<option value="11">11</option>'.
          '<option value="12">12</option>'.
          '<option value="13">13</option>'.
          '<option value="14">14</option>'.
          '<option value="15">15</option>'.
          '<option value="16">16</option>'.
          '<option value="17">17</option>'.
          '<option value="18">18</option>'.
          '<option value="19">19</option>'.
          '<option value="20">20</option>'.
          '<option value="21">21</option>'.
          '<option value="22">22</option>'.
          '<option value="23">23</option>'.
          '<option value="24">24</option>'.
          '<option value="25">25</option>'.
          '<option value="26">26</option>'.
          '<option value="27">27</option>'.
          '<option value="28">28</option>'.
          '<option value="29">29</option>'.
          '<option value="30">30</option>'.
          '<option value="31" selected >31</option>'.
        '</select>'.
        '<select name="create[birsday][month]" id="create_birsday_month">'.
          '<option value="1">01</option>'.
          '<option value="2">02</option>'.
          '<option value="3">03</option>'.
          '<option value="4">04</option>'.
          '<option value="5">05</option>'.
          '<option value="6">06</option>'.
          '<option value="7">07</option>'.
          '<option value="8">08</option>'.
          '<option value="9">09</option>'.
          '<option value="10">10</option>'.
          '<option value="11">11</option>'.
          '<option value="12" selected >12</option>'.
        '</select>'.
        '<input name="create[birsday][year]" id="create_birsday_year" size="4" type="text" value="1980"></input>'.
        '<select name="create[birsday][hour]" id="create_birsday_hour">'.
          '<option value="0">00</option>'.
          '<option value="1">01</option>'.
          '<option value="2">02</option>'.
          '<option value="3">03</option>'.
          '<option value="4">04</option>'.
          '<option value="5">05</option>'.
          '<option value="6">06</option>'.
          '<option value="7">07</option>'.
          '<option value="8">08</option>'.
          '<option value="9">09</option>'.
          '<option value="10">10</option>'.
          '<option value="11">11</option>'.
          '<option value="12">12</option>'.
          '<option value="13">13</option>'.
          '<option value="14">14</option>'.
          '<option value="15">15</option>'.
          '<option value="16">16</option>'.
          '<option value="17">17</option>'.
          '<option value="18">18</option>'.
          '<option value="19">19</option>'.
          '<option value="20">20</option>'.
          '<option value="21">21</option>'.
          '<option value="22">22</option>'.
          '<option value="23" selected >23</option>'.
        '</select>'.
        //FIXME: select не должен закрываться
        '<select name="create[birsday][minute]" id="create_birsday_minute">'.
          '<option value="0">00</option>'.
          '<option value="1">01</option>'.
          '<option value="2">02</option>'.
          '<option value="3">03</option>'.
          '<option value="4">04</option>'.
          '<option value="5">05</option>'.
          '<option value="6">06</option>'.
          '<option value="7">07</option>'.
          '<option value="8">08</option>'.
          '<option value="9">09</option>'.
          '<option value="10">10</option>'.
          '<option value="11">11</option>'.
          '<option value="12">12</option>'.
          '<option value="13">13</option>'.
          '<option value="14">14</option>'.
          '<option value="15">15</option>'.
          '<option value="16">16</option>'.
          '<option value="17">17</option>'.
          '<option value="18">18</option>'.
          '<option value="19">19</option>'.
          '<option value="20">20</option>'.
          '<option value="21">21</option>'.
          '<option value="22">22</option>'.
          '<option value="23">23</option>'.
          '<option value="24">24</option>'.
          '<option value="25">25</option>'.
          '<option value="26">26</option>'.
          '<option value="27">27</option>'.
          '<option value="28">28</option>'.
          '<option value="29">29</option>'.
          '<option value="30">30</option>'.
          '<option value="31">31</option>'.
          '<option value="32">32</option>'.
          '<option value="33">33</option>'.
          '<option value="34">34</option>'.
          '<option value="35">35</option>'.
          '<option value="36">36</option>'.
          '<option value="37">37</option>'.
          '<option value="38">38</option>'.
          '<option value="39">39</option>'.
          '<option value="40">40</option>'.
          '<option value="41">41</option>'.
          '<option value="42">42</option>'.
          '<option value="43">43</option>'.
          '<option value="44">44</option>'.
          '<option value="45">45</option>'.
          '<option value="46">46</option>'.
          '<option value="47">47</option>'.
          '<option value="48">48</option>'.
          '<option value="49">49</option>'.
          '<option value="50">50</option>'.
          '<option value="51">51</option>'.
          '<option value="52">52</option>'.
          '<option value="53">53</option>'.
          '<option value="54">54</option>'.
          '<option value="55">55</option>'.
          '<option value="56">56</option>'.
          '<option value="57">57</option>'.
          '<option value="58">58</option>'.
          '<option value="59" selected >59</option>'.
        '</select>'
      )->
      assert_equal(
        $this->forms_helper->select_tag($this->template, 'create[select]', array('red', 'green', 'blue'), 1, array('class' => 'wide')),
        '<select name="create[select]" class="wide">'.
          '<option value="0">red</option> '.
          '<option value="1" selected >green</option> '.
          '<option value="2">blue</option>'.
        '</select>'
      )->
      assert_equal(
        $this->forms_helper->select($this->template, 'color', array('class' => 'wide')),
        '<select name="create[color]" class="wide">'.
          '<option value="0">red</option> '.
          '<option value="1">green</option> '.
          '<option value="2">blue</option>'.
        '</select>'
      )->
      assert_equal(
        $this->forms_helper->object_select($this->template, 'category', array('calss' => 'wide')),
        '<select name="create[category]" id="create_category" calss="wide">'.
          '<option value="1">cetagory1</option>'.
          '<option value="2">cetagory2</option>'.
          '<option value="3">cetagory3</option>'.
        '</select>'
      )->
      assert_equal(
        $this->forms_helper->object_multicheckbox($this->template, 'categories', array('calss' => 'wide')),
        '<label class="inline">'.
          '<input type="checkbox" value="1" name="create[categories][0]" id="create_categories_0" calss="wide" />'.
          '&nbsp;cetagory1'.
        '</label>'.
        '<label class="inline">'.
          '<input type="checkbox" value="2" name="create[categories][1]" id="create_categories_1" calss="wide" />'.
            '&nbsp;cetagory2'.
        '</label>'.
        '<label class="inline">'.
          '<input type="checkbox" value="3" name="create[categories][2]" id="create_categories_2" calss="wide" />'.
            '&nbsp;cetagory3'.
        '</label>'
      )->
      assert_equal(
        $this->forms_helper->end_form($this->template),
        '</fieldset></form>'
      )->
      assert_equal(
        $this->forms_helper->begin_form_tag($this->template, 'do.php', 'put', array('calss' => 'additional')),
        '<form action="do.php" method="post" calss="additional">'."\n".
        '<input type="hidden" name="_method" value="put" />'."\n"
      )->
      assert_equal(
        $this->forms_helper->end_form_tag($this->template),
        "</form>\n"
      );
  }
///     </body>
///   </method>

///   </protocol>
}
/// </class>

/// </module>
?>