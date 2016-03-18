<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 * An open source application development framework
 *
 * @package     CodeIgniter * @author Rick Ellis
 * @copyright   Copyright (c) 2006, EllisLab, Inc.
 * @license     http://www.codeignitor.com/user_guide/license.html
 * @link        http://www.codeigniter.com * @since Version 1.0
 */

// ----------------------------------------------------------------------
/**
 * Library:  Extended Parser Class (2008-2010)
 * Features: Parse templates, loaded as a view, and/or parse strings;
 *           Nested simple conditionals {if *}x{else}y{/if}.
 *           Nested conditionals (introduced in version 0.5.0);
 *           Array-element access; Successive same array usage;
 *           {Ignore} tag-pair protects a section from strip_vars but tags inside
 *           it are parsed. {Ignore_pre} is saved early on, and not parsed at all.
 *           You can use multiple ignore and ignore_pre tag-pairs.
 * Tips:     - Use Parse() to load a standard view with the php being processed.
 *           Parse_string() allows you, for instance, to build an email-message
 *           with simple template logic, or a partial view.
 *           - The cleanup unused tags option (strip_vars) is optional and should
 *           IMO be avoided. Better is to set all variables, be it empty. The parse
 *           will then automatically replace them with empties. Currently strip_vars
 *           generates a php error on various curly-brackets like inside javascript.
 *           For the time being, either protect javascript with ignore, or don't
 *           strip tags. Another reason to not strip_vars is, it also removes tags
 *           that might be in your data, like an article discussing parser {tags}.
 * Version:  0.5.0
 * Changes:  0.5.0 gdmac. nested conditionals. option (default) to convert
 *                 delimiters inside data to hmtl-entities.
 *           0.4.1 gdmac. fixme1 fixed, process all tag-pairs before singles.
 *                 changed: remove ignore tags, even when strip_tags==false
 *           0.4 gdmac. Mashup of parser versions, ignore tag, extra code and debug
 *           0.3 rafsoaken. cleanup, test-suite, parse, array, booleans etc.
 *           0.2 isaiahdw. conditionals
 *           0.1 adamp1. cleanup unused tags and tag-pairs
 * Todo:     - To get this going, we need some nice documentation with it
 *           - set_options and better error-reporting and handling (malformed tags).
 *           - fix for stripping tags on curly brackets like in javascript
 *           - option to check for empty (next to isset)
 *           - conditional combinations {if {is_admin}==1 AND {username}==Tim}
 *           - general optimization and debugging
 *
 * Install:  Put in /application/libraries/ as parser.php instead as MY_parser.php
 *           Since most methods of the class changed, it made sense to not extend
 *           the default parser anymore.
 *
 * Discuss:  http://codeigniter.com/forums/viewthread/68878/P45/
 *
 * usage:    See the parser_test controller for some examples
 *
 *  */

class CI_Parser {

	var $CI;
	var $_ignore = array();
	var $_template;
	var $_conditionals;

	var $l_delim = '{';
	var $r_delim = '}';

	var $options = array
	(
		// convert delimiters in data to entities. { = &#123; } = &#125;
		'convert_delimiters' => array( true, '&#123;', '&#125;')
	);

	// --------------------------------------------------------------------
	// Loads a view (and parses the php). Then parses pseudo-variables contained
	// in the specified template and replace them with the data in the second param.
	// Param return: output or return as string. Param strip_vars, removes
	// unused pseudo-variables.
	//
	function parse($template, $data, $return = FALSE, $strip_vars = FALSE)
	{
		$this->CI =& get_instance();
		$template = $this->CI->load->view($template, $data, TRUE);
		return $this->parse_string($template, $data, $return, $strip_vars);
	}

	// --------------------------------------------------------------------
	// Parse a string as a template
	//
	function parse_string($template, $data, $return = TRUE, $strip_vars = FALSE)
	{
		if ($template == '') {
			return FALSE;
		}
		// have a local references to $template and $data in the class
		$this->CI        =& get_instance();
		$this->_template =& $template;
		$this->_data     =& $data;
		$this->_ignore   = array(); // start empty on

		// store ignore_pre tag data early
		$this->_store_ignored('ignore_pre');

		// first round process tag data, pairs first
		foreach ($data as $key => $val)
		{
			if (is_array($val))
                            $template = $this->_parse_pair($key, $val, $template);
		}
		foreach ($data as $key => $val)
		{
			if (is_array($val)==false) $template = $this->_parse_single($key, $val, $template);
		}

		// parse array elements
		foreach ($data as $key => $val)
		{
			if (is_array($val)) $template = $this->_parse_array_elems($key, $val, $template);
		}

		// Check for conditional statments
		$this->_conditionals = $this->_find_nested_conditionals($template);

		if($this->_conditionals)
		{
			$template = $this->_parse_conditionals($template);
		}

		// Store ignore tags
		$this->_store_ignored('ignore');

		// Strip empty pseudo-variables
		if ($strip_vars)
		{
			// Todo: Javascript with curly brackets most times generates an error
			if (preg_match_all("(".$this->l_delim."([^".$this->r_delim."/]*)".$this->r_delim.")", $template, $m))
			{
				foreach($m[1] as $value)
				{
					$template = preg_replace('#'.$this->l_delim.$value.$this->r_delim.'(.+)'.$this->l_delim.'/'.$value.$this->r_delim.'#sU', "", $template);
					// preg_replace('#'.$this->l_delim.$value.$this->r_delim.'(.+)'.$this->l_delim.'/'.$value.$this->r_delim.'#sU', "", $template);
					$template = str_replace ("{".$value."}", "", $template);
				}
			}
		}
		// retrieve al ignored data
		if(!empty($this->_ignore))
		{
			$this->_restore_ignored();
		}

		if ($return == FALSE)
		{
			$this->CI->output->append_output($template);
		}

		return $template;
	}

	// --------------------------------------------------------------------
	//
	function _restore_ignored()
	{
		foreach($this->_ignore as $key => $item)
		{
			$this->_template = str_replace($item['id'], $item['txt'], $this->_template);
		}
		// data stored in $this->_template
		return true;
		}

		// --------------------------------------------------------------------
	//
	function _store_ignored($name)
	{
		if (FALSE === ($matches = $this->_match_pair($this->_template, $name)))
		{
			return false;
		}

		foreach( $matches as $key => $tagpair)
		{
			// store $tagpair[1] and replace $tagpair[0] in template with unique identifier
			$this->_ignore[$name.$key] = array(
				'txt' => $tagpair[1],
				'id'  => '__'.$name.$key.'__'
			);
			// strip it and place a temporary string
			$this->_template = str_replace($tagpair[0], $this->_ignore[$name.$key]['id'], $this->_template);
		}

		return true;
	}

	// --------------------------------------------------------------------
	//
	function _parse_array_elems($name, $arr, $template)
	{
		foreach($arr as $arrkey=>$arrval) {
			if(!is_array($arrval)) {
				$template = $this->_parse_single("$name $arrkey", $arrval, $template);
			}
		}
		return $template;
	}

	// --------------------------------------------------------------------
	// TODO: restore usage of custom left and right delimiter
	//
	function _find_nested_conditionals($template)
	{
		// any conditionals found?
		$f = strpos($template, '{if');
		if ($f === false)
		{
			return false;
		}

		$found_ifs = array();
		$found_open = strpos($template, '{if');
		while ( $found_open !== false)
		{
			$found_ifs[] = $found_open;
			$found_open = strpos($template, '{if', $found_open+3);
		}
		// print_r($conditionals);

		// -----------------------------------------------------------------------------
		// find all nested ifs. Yeah!
		for($key = 0; $key < sizeof($found_ifs); ++$key)
		{
			$open_tag = $found_ifs[$key];
			$found_close = strpos($template, '{/if}', $open_tag);
/*msg*/		if($found_close === false){ echo("\n Error. No matching /if found for opening tag at: $open_tag"); exit(); }
			$new_open  = $open_tag;
			$new_close = $found_close;
			// -------------------------------------------------------------------------
			// find new {if  inside a chunk, if found find next close tag
			$i=0; // fail safe, for now test 100 nested ifs maximum :-)
			$found_blocks=array();
			do
			{
				// does it have an open_tag inside?
				$chunk = substr($template, $new_open+3, $new_close - $new_open - 3);
				$found_open = strpos($chunk, '{if');

				if($found_open !== false)
				{
					$new_close = $new_close+5;
					$new_close = strpos($template, '{/if}', $new_close);
/* msg */			if($new_close===false) { echo("\n Error. No matching /if found for opening tag at: $found_open"); exit(); }
					$new_open = $new_open + $found_open + 3;
					$found_blocks[] = $new_open;
				}
				$i++;
			}
			while( $found_open !== FALSE && ($i < 100) );

			// store it
			$length = $new_close - $open_tag + 5; // + 5 to catch closing tag
			$chunk = substr($template, $open_tag, $length);
			$conditionals[$open_tag]=array
			(
				'start'    => $open_tag,
				'stop'     => $open_tag + $length,
				'raw_code' => $chunk,
				'found_blocks' => $found_blocks
			);
		}// end for all found ifs

		// walk thru conditionals[] and extract condition_string and replace nested
		$regexp = '#{if (.*)}(.*){/if}#sU';
		foreach($conditionals as $key => $conditional)
		{
			$found_blocks = $conditional['found_blocks'];
			$conditional['parse'] = $conditional['raw_code'];
			if(!empty($found_blocks))
			{
				foreach($found_blocks as $num)
				{
					// it contains another conditional, replace with unique identifier for later
					$unique = "__pparse{$num}__";
					$conditional['parse'] = str_replace($conditionals[$num]['raw_code'], $unique, $conditional['parse']);
				}
			}
			$conditionals[$key]['parse'] = $conditional['parse'];

			if(preg_match($regexp, $conditional['parse'], $preg_parts, PREG_OFFSET_CAPTURE))
			{
				// echo "\n"; print_r($preg_parts);
				$raw_code = $preg_parts[0][0];
				$cond_str = $preg_parts[1][0] !=='' ? $preg_parts[1][0] : '';
				$insert   = $preg_parts[2][0] !=='' ? $preg_parts[2][0] : '';

/* msg */		if($raw_code !== $conditional['parse']){ echo "\n Error. raw_code differs from first run!\n$raw_code\n{$conditional['raw_code']}";exit; }

				if(preg_match('/({|})/', $cond_str, $problematic_conditional))
				{
					// Problematic conditional, delimiters found or something
					// if strip_vars, remove whole raw_code, for now bye-bye
/* msg */			echo "\n Error. Found delimiters in condition to test\n: $cond_str";
					exit;
				}
				// store condition string and insert
				$conditionals[$key]['cond_str'] = $cond_str;
				$conditionals[$key]['insert']   = $insert;
			}
			else
			{
/* msg */		echo "\n Error in conditionals (preg parse) No conditional found or some was not closed properly";
				exit();
				// todo
				$conditionals[$key]['cond_str'] = '';
				$conditionals[$key]['insert']   = '';
			}
		}
		return $conditionals;
	}

	// -------------------------------------------------------------------------
	//
	function _parse_conditionals($template)
	{
		if(empty ($this->_conditionals))
		{
			return $template;
		}

		$conditionals =& $this->_conditionals;

		foreach($conditionals as $key => $conditional)
		{
			$raw_code = $conditional['raw_code'];
			$cond_str = $conditional['cond_str'];
			$insert   = $conditional['insert'];

			if($cond_str!=='' AND !empty($insert))
			{
				// Get the two values
				$cond = preg_split("/(\!=|==|<=|>=|<>|<|>|AND|XOR|OR|&&)/", $cond_str);

				// Do we have a valid if statement?
				if(count($cond) == 2)
				{
					// Get condition and compare
					preg_match("/(\!=|==|<=|>=|<>|<|>|AND|XOR|OR|&&)/", $cond_str, $cond_m);
					array_push($cond, $cond_m[0]);

					// Remove quotes - they cause to many problems!
					// trim first, removes whitespace if there are no quotes
					$cond[0] = preg_replace("/[^a-zA-Z0-9_\s\.,-]/", '', trim($cond[0]));
					$cond[1] = preg_replace("/[^a-zA-Z0-9_\s\.,-]/", '', trim($cond[1]));

					if(is_int($cond[0]) && is_int($cond[1]))
					{
						$delim = "";
					}
					else
					{
						$delim ="'";
					}

					// Test condition
					$to_eval = "\$result = ($delim$cond[0]$delim $cond[2] $delim$cond[1]$delim);";
					eval($to_eval);
				}
				else // single value
				{
					// name isset() or number. Boolean data is 0 or 1
					$result = (isset($this->_data[trim($cond_str)]) OR (intval($cond_str) AND (bool)$cond_str));
				}
			}
			else
			{
				$result = false;
			}

			// split insert text if needed. Can be '' or 'foo', or 'foo{else}bar'
			$insert = explode('{else}', $insert, 2);

			if($result == TRUE)
			{
				$conditionals[$key]['insert'] = $insert[0];
			}
			else // result = false
			{
				$conditionals[$key]['insert'] = (isset($insert['1'])?$insert['1']:'');
			}

			// restore raw_code from nested conditionals in this one
			foreach($conditional['found_blocks'] as $num)
			{
				$unique = "__pparse{$num}__";
				if(strpos($conditional['insert'], $unique))
				{
					$conditionals[$key]['insert'] = str_replace($unique, $conditionals[$num]['raw_code'], $conditionals[$key]['insert']);
				}
			}
		}
		// end foreach conditionals.

		// replace all rawcodes with inserts in the template
		foreach($conditionals as $conditional) $template = str_replace($conditional['raw_code'], $conditional['insert'], $template);

		return $template; // thank you, have a nice day!
	}


	// --------------------------------------------------------------------
	// Parse a single key/value
	function _parse_single($key, $val, $string)
	{
		if(is_bool($val)) $val = intval($val); // boolean numbers
		$convert =& $this->options['convert_delimiters'];
		// convert delimiters in data
		if($convert[0]) $val = str_replace(array($this->l_delim,$this->r_delim), array($convert[1],$convert[2]), $val);
		return str_replace($this->l_delim.$key.$this->r_delim, $val, $string);
	}

	// --------------------------------------------------------------------
	// Edited Parse a tag pair, now also 1-dim arrays
	// Parses tag pairs:  {some_tag} string... {/some_tag}
	function _parse_pair($variable, $data, $string)
	{
	
		if (FALSE === ($matches = $this->_match_pair($string, $variable)))
		{
			return $string;
		}

		$singles=array();

		foreach ($matches as $m)
		{
			$str = '';
			//if(FALSE === ($matches = $this->_match_pair($string, $variable)))
			foreach ($data as $rowkey=>$row)
			{
				$temp = $m['1'];
				if(is_array($row))
				{
					foreach ($row as $key => $val)
					{
						if ( ! is_array($val))
						{
							$temp = $this->_parse_single($key, $val, $temp);
						}
						else
						{
							$temp = $this->_parse_pair($key, $val, $temp);
                            // parse array elements
                            $temp=  $this->_parse_array_elems($key, $val, $temp);

						}
					}
					$str .= $temp;
				}
				else
				{
					$singles[$rowkey]=$row;
				}
			}
			if($singles) {
				foreach($singles as $key => $value) {
					$str = $this->_parse_single($key, $value, $str);
				}
			}
			$string = str_replace($m['0'], $str, $string);
		}
		return $string;
	}

	// --------------------------------------------------------------------
	// Edited Matches a variable pair (now match_all)
	//
	function _match_pair($string, $variable)
	{
		// var_dump($string,$variable,"|".$this->l_delim . $variable . $this->r_delim."(.+)".$this->l_delim . '/' . $variable . $this->r_delim."|sU");
		if ( @! preg_match_all("|".$this->l_delim . $variable . $this->r_delim."(.+)".$this->l_delim . '/' . $variable . $this->r_delim."|sU", $string, $match, PREG_SET_ORDER))
		{
			return FALSE;
		}

		return $match;
	}

	// --------------------------------------------------------------------
	// Original set_delimiters
	//
	function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}


}
// end class