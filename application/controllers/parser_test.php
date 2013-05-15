<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parser_test extends CI_Controller {

	var $data;

	// -------------------------------------------------- Constructor
	function Parser_test()
	{
		parent::__construct();
	}

	// --------------------------------------------------
	function index()
	{
        $this->user->authorize();
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
		echo ' <head><meta http-equiv="Content-type" content="text/html; charset=utf-8" /></head>';
		echo "<body style='font-family:courier;font-size:10px;'><pre>";

		$this->load->library('parser');
		$this->load->library('unit_test');
		
        $this->data = array
		(
            'boolfalse'=> false,
            'booltrue'=> true,
            'number' => 10,
            'foo' => 'bar',
            'test'=>'something',
            'array'=>array(
                'unique'=>'im unique',
                array('id'=>'23', 'sale_price'=>12, 'price'=>15),
                array('id'=>'21', 'sale_price'=>28, 'price'=>20)
                ),
            'myarray'=>array('submitter'=>'clemens', 'id'=>1),
			'title2'=> 'Single Title not in posts',
            'posts'=>array(
                array('title'=>'first post', 'paras'=>array('main'=>'foo', 'short'=>'bar')),
                array('title'=>'second post', 'paras'=>array('main'=>'ice', 'short'=>'cold'))
            ),
			'emptyarray' => array( ),
			'emptyvar'   => '',
			'withdelimiters' => 'delimiters in data {if number}{test}{/if} are converted to html-entities',
			'withwrongdelimiters' => 'this var has {if  something wrong} with it'
            );

		$this->testNestedConditionals();
//		$this->testIgnore();
//		$this->FixMe2(); // gives php errors
		$this->testWhole();
//		$this->testConditionalSyntax();
//		$this->testNested();
//		$this->testArray();
//		$this->testErrors();
//		$this->testBool();
//		$this->testArrayRepeatedAndNested();
}

// --------------------------------------------------------------------
	function testnestedConditionals()
{
$templates[] = '
Working with nested conditionals
{if {array unique}==im unique}
..array key unique
....{if {boolfalse}}
......im not shown
....{else}
......{if "{foo}"=="bar"}foo=bar{/if}
....ok im shown too
..{/if}
{/if}
';
$expected[] ='
Working with nested conditionals

..array key unique
....
......foo=bar
....ok im shown too
..

';

$templates[] ='
{if {myarray submitter}==clemens} doing this
 .{if b} doing b
  .{if c} doing c
   .{if d} doing d {/if}
    .after d is done
   .{/if} after c is done
  .{else}
   .b was not set
  .{/if}
  .after b test, but nested inside first
{/if}
and after a nested group, outside any conditionals
';
$expected[] ='
 doing this
 .
   .b was not set
  .
  .after b test, but nested inside first

and after a nested group, outside any conditionals
';

$templates[] = '{withdelimiters}
';
$expected[] = 'delimiters in data &#123;if number&#125&#123;test&#125&#123;/if&#125 are converted to html-entities
';

		foreach($templates as $key => $template)
		{
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testConditionalSyntax');
		}
    }

// --------------------------------------------------------------------
	function testIgnore(){
		$template = '
Testing ignore
{ignore_pre}and ignoring {test} :-)
ignore_pre is stored early. Tags not parsed nor stripped.{/ignore_pre}

nonexisting tag:{nonexistingtag}
nonexisting array:{nonexistingarray}foo {test} bar{/nonexistingarray}
empty vars or empty array() will be stripped by parse, no need for ignore
emptyarray:{emptyarray}foo {test} bar{/emptyarray}
emptyvar:{emptyvar}

{ignore}i\m ignored when stripping tags, but my vars are parsed.
 test:{test} and arrays too: multi-dim-array:{posts}title:{title},{paras}main:{main} short:{short}{/paras}|{/posts}
 nonexistingtag:{nonexistingtag} tag is preserved
 nonexistingarray:{nonexistingarray}blah{/nonexistingarray} tag-pair is preserved
 emptyarray:{emptyarray}foo {test} bar{/emptyarray} stripped by parse, not if in ignore_pre
 emptyvar:{emptyvar} stripped by parse
 access a one-dimensional array directly by key, dont use tag-pair:submitter:{myarray submitter} id:{myarray id}
 booltrue:{if {booltrue}}True{else}False{/if}
 booltrue:{if booltrue}set{else}not set{/if}
{/ignore}

{ignore}// preserve some javascript with curly-brackets
$().ready(function(){
  $("#selector").addClass("something");
});
{/ignore}

Multiple ignore and ignore_pre tag-pairs are allowed
';

		$expected= '
Testing ignore
and ignoring {test} :-)
ignore_pre is stored early. Tags not parsed nor stripped.

nonexisting tag:
nonexisting array:
empty vars or empty array() will be stripped by parse, no need for ignore
emptyarray:
emptyvar:

i\m ignored when stripping tags, but my vars are parsed.
 test:something and arrays too: multi-dim-array:title:first post,main:foo short:bar|title:second post,main:ice short:cold|
 nonexistingtag:{nonexistingtag} tag is preserved
 nonexistingarray:{nonexistingarray}blah{/nonexistingarray} tag-pair is preserved
 emptyarray: stripped by parse, not if in ignore_pre
 emptyvar: stripped by parse
 access a one-dimensional array directly by key, dont use tag-pair:submitter:clemens id:1
 booltrue:True
 booltrue:set


// preserve some javascript with curly-brackets
$().ready(function(){
  $("#selector").addClass("something");
});


Multiple ignore and ignore_pre tag-pairs are allowed
';
	$expected_not_stripped= '
Testing ignore
and ignoring {test} :-)
ignore_pre is stored early. Tags not parsed nor stripped.

nonexisting tag:{nonexistingtag}
nonexisting array:{nonexistingarray}foo something bar{/nonexistingarray}
empty vars or empty array() will be stripped by parse, no need for ignore
emptyarray:
emptyvar:

i\m ignored when stripping tags, but my vars are parsed.
 test:something and arrays too: multi-dim-array:title:first post,main:foo short:bar|title:second post,main:ice short:cold|
 nonexistingtag:{nonexistingtag} tag is preserved
 nonexistingarray:{nonexistingarray}blah{/nonexistingarray} tag-pair is preserved
 emptyarray: stripped by parse, not if in ignore_pre
 emptyvar: stripped by parse
 access a one-dimensional array directly by key, dont use tag-pair:submitter:clemens id:1
 booltrue:True
 booltrue:set


// preserve some javascript with curly-brackets
$().ready(function(){
  $("#selector").addClass("something");
});


Multiple ignore and ignore_pre tag-pairs are allowed
';
		$returned = $this->parser->parse_string($template, $this->data, true, true);
		echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n$expected<hr />";
        echo $this->unit->run($returned,$expected,'TestIgnore with strip_tags true');

		$returned = $this->parser->parse_string($template, $this->data, true, false);
		echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n$expected_not_stripped<hr />";
        echo $this->unit->run($returned,$expected_not_stripped,'TestIgnore with strip_tags false');

	}

// --------------------------------------------------------------------
	function FixMe2(){
        $template = '
{nonexistingvar} is shown at error

// some javascript with curly-brackets
$().ready(function(){
  $("#selector").addClass("something");
});

and test:{test}
';

        $expected= '
{nonexistingvar} is shown at error

// some javascript with curly-brackets to ignore
$().ready(function(){
  $("#selector").addClass("something");
});
{/ignore}
and more text at the end. test:Something
';

		$returned = $this->parser->parse_string($template, $this->data, true, true);
		echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n$expected<hr />";
        echo $this->unit->run($returned,$expected,'FixMe2 borks on javascript brackets');
    }

// --------------------------------------------------------------------
	function testWhole(){
        $template = '
{if 10 > 8}10 is greater then 8{/if}
{if "bar"=={foo}}Foo is equal to bar{/if}
{if {test}!="asdfsd"}Test is not equal to asdfsd{/if}
{if "something"=={test}}Test is equal to "{test}"{/if}
{if test}Test is set{/if}
{if randomjunk}This should never show{else}This should always show{/if}
{array}ID: {id}, {if {sale_price} > 20}Sale Price: {sale_price}{else}Price: {price}{/if}
{/array}
{if "something something"=="something something"}Testing{else}test{/if}';

        $expected= '
10 is greater then 8
Foo is equal to bar
Test is not equal to asdfsd
Test is equal to "something"
Test is set
This should always show
ID: 23, Price: 15
ID: 21, Sale Price: 28

Testing';

		$returned = $this->parser->parse_string($template, $this->data, true, true);
		echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n$expected<hr />";
        echo $this->unit->run($returned,$expected,'testWhole');
    }

// --------------------------------------------------------------------
    function testConditionalSyntax(){

        //number comparisons
        $templates[] = '{if 10 > 8}10 is greater then 8{/if}';
        $expected[] = '10 is greater then 8';

        $templates[] = '{if "10" > "8"}10 is greater then 8{/if}';
        $expected[] = '10 is greater then 8';

        //!this should be wrong as we are comparing strings!
        $templates[] = '{if "  20" > "18     "}10 is greater then 8{/if}';
        $expected[] = '';

        $templates[] = '{if 10 < 8}10 is greater then 8{/if}';
        $expected[] = '';

        $templates[] = '{if 10}number 10{/if}';
        $expected[] = 'number 10';

        $templates[] = '{if "10 "}it\s a string{/if}';
        $expected[] = '';

		$templates[] = '{if {number} >= 8}10 is greater then 8{/if}';
        $expected[] = '10 is greater then 8';

        $templates[] = '{if 8 != {number} }10 is greater then 8{/if}';
        $expected[] = '10 is greater then 8';

        //string comparisons
        $templates[] = '{if {foo}!=bar}Foo is not bar{/if}';
        $expected[] = '';

        $templates[] = '{if {foo}==bar}Foo is bar{/if}';
        $expected[] = 'Foo is bar';

        //also wrong!
        $templates[] = '{if {foo}=="bar "}Foo is bar{/if}';
        $expected[] = '';

        $templates[] = '{if "bar"=={foo}}Foo is bar{/if}';
        $expected[] = 'Foo is bar';

        $templates[] = '{if foo}Foo exists{/if}';  //attention we can't
        $expected[] = 'Foo exists';

        $templates[] = '{if schmafu}schmafu exists{/if}';
        $expected[] = '';

        //now with else constructs
        $templates[] = '{if schmafu}schmafu exists{else}schmafu doesn\'t exist{/if}';
        $expected[] = 'schmafu doesn\'t exist';

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testConditionalSyntax');
        }
    }

	//-------------------------------------------------
    function testNested(){
        $templates[] = '{array}{id}{foo}{/array}';
        $expected[] = '23bar21bar';

        $templates[] = '{array}{if {id}> 22}{foo}{/if}{/array}';
        $expected[] = 'bar';

        $templates[] = '{array}{if {id}=={foo}}can\'t be true{else} {test} strange is happening.. <br />{/if}
{/array}';
        $expected[] = ' something strange is happening.. <br />
 something strange is happening.. <br />
';

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testNested');
        }
    }

    function testArray(){
        $templates[]="{array unique}";
        $expected[]="im unique";

        $templates[]="{array}{unique}{/array}";
        $expected[]="im uniqueim unique";

        $templates[]="{array}{unique} {id} {/array}";
        $expected[]="im unique 23 im unique 21 ";

        $templates[]="name:{myarray submitter} id:{myarray id}";
        $expected[]="name:clemens id:1";

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testArray');
        }
    }

    function testErrors(){
        $templates[]="{notexisting}";
        $expected[]="";
        $err_expected[]="{notexisting}";

        $templates[]="{array}test {notexisting}{/array}";
        $expected[]="test test ";
        $err_expected[]="test {notexisting}test {notexisting}";

        $templates[]="{array}{if notexisting}hello{else}test {/if}{/array}";
        $expected[]="test test ";
        $err_expected[]="test test ";

        $templates[]="{array}{if {id}==23}hello23{else}hello{id}{/if} {/array}";
        $expected[]="hello23 hello21 ";
        $err_expected[]="hello23 hello21 ";

        $templates[]="{array}{if {id}==23}hello23{else}{idontexist}{array me_neither}hello{id}{/if} {/array}";
        $expected[]="hello23 hello21 ";
        $err_expected[]="hello23 {idontexist}{array me_neither}hello21 ";

        $templates[]="{if ihaveafriend}logged_in{else}goaway{/if} number:{myarray id}!";
        $expected[]="goaway number:1!";
        $err_expected[]="goaway number:1!";

		// strip_tags off should keep unset variables
        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, false);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$err_expected[$key]}<hr />";
			echo $this->unit->run($returned,$err_expected[$key],'testErrors strip_tags = true');
        }

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testErrors strip_tags = false');
        }
    }

    function testBool(){
        $templates[]="{if {booltrue}}its true!{else}its false :({/if}";
        $expected[]="its true!";

        $templates[]="{if {boolfalse}}its true!{else}its false :({/if}";
        $expected[]="its false :(";

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testBool');
        }
    }

    function testArrayRepeatedAndNested(){
        $templates[]="{array}test{/array} and another time: {array}me{/array}";
        $expected[]="testtest and another time: meme";

        $templates[]="{posts}Post:{title}
        {paras}{main}
        {short}
        {/paras}{/posts}";
        $expected[]="Post:first post
        foo
        bar
        Post:second post
        ice
        cold
        ";

        foreach($templates as $key => $template){
			$returned = $this->parser->parse_string($template, $this->data, true, true);
			echo "<pre>INPUT:\n$template<hr />RETURNED:\n$returned<hr />EXPECTED:\n{$expected[$key]}<hr />";
			echo $this->unit->run($returned,$expected[$key],'testArrayRepeatedAndNested');
        }
    }

}
?>