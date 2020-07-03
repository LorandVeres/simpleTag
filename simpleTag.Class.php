<?php

/**
 *===============================================================================
 *
 * MIT License
 *
 * Copyright (c) 2020 Lorand Veres (lorand.mast@gmail.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * ===============================================================================
 *
 *
 *
 * ABOUT simpleTag
 * ---------------
 * Easy to use and create simple HTML blocks, what are more often in use to format
 * data from a given array using PHP.
 *
 * The intended purpose it is just to print the data in a very handy fasion wihout
 * having to mess up the php code. simpleTag is not intended to manipulate any XML
 * documents or fragments.
 *
 * The following Objects
 *
 * simpleTag -> inline_tag
 * simpleTag -> single_tag
 *
 * Will be printed one per row without increasing the indenting deepness after
 * subsequent inline tags. The tags listed in the arrays are not the ones specified
 * until the HTML4 standard. These arrays were created to print out the tags in the
 * desired way. At this time are still missing elements yet.
 *
 *
 */

class simpleTag {

	public $Doc = array();
	private $inline_tag = array('a', 'b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp', 'time', 'var', 'bdo', 'br', 'img', 'map', 'object', 'q', 'script', 'span', 'sub', 'sup', 'button', 'input', 'label', 'select', 'textarea', 'meta', 'link');
	private $block_tag = array('address', 'article', 'aside', 'blockquote', 'canvas', 'dd', 'div', 'dl', 'dt', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'li', 'main', 'nav', 'noscript', 'ol', 'p', 'pre', 'section', 'table', 'tfoot', 'ul', 'video');
	private $single_tag = array('!DOCTYPE html', 'meta', 'link', 'br', 'img', 'hr', 'input', 'embed', 'bgsound', 'base', 'col', 'source');
	public $indentNum = 0;
	private $set_count = TRUE;

	function __construct() {

	}

	/*
	 * Append one tag into another
	 * Use:
	 *
	 * simpleTag -> append_tag($into, $tag);
	 * 
	 * @return void
	 * @author  Lorand Veres
	 *
	 */
	public function append_tag(&$into, $tag) {
		if (!array_key_exists(1, $into)) {
			if (!array_push($into, $tag))
				;
		} else {
			if (!array_push($into[1], $tag))
				;
		}
	}

	/*
	 * Create a tag. When single tags are printed like <input> the $txt variable
	 * value provided should be an empty string "". No need to add each attribute
	 * at once, it can take a string with multiple attributes concatenated like in
	 * the example.
	 * Use:
	 * $tag = simpleTag -> tag("input", 'type="text" name="fname" value="first name" style="width:100px"', '');
	 * 
	 * @return string
	 * @author  Lorand Veres
	 */
	public function tag($tagname, $attr, $txt) {
		$o = "<";
		$c = ">";
		//  output   /
		$e = chr(47);
		//  output   space
		$s = chr(32);
		//  output   "
		$q = chr(34);
		//   output   tab
		$t = chr(9);
		// output   =
		$eq = chr(61);
		//  output   new line
		$n = chr(10);

		$single = $this -> check_single_tag($tagname);
		if (!$single) {
			$tag = array();
			$tag[] = $o . $tagname . $this -> setAttr($attr) . $c;
			$tag[] = !empty($txt) ? array($txt) : array();
			$tag[] = $o . $e . $tagname . $c;

		} elseif ($single) {
			$tag = ($o . $tagname . $this -> setAttr($attr) . $c . $n);
		}
		return $tag;
	}

	/*
	 *  @parameter passed is a string. Returning TRUE if in a single_tag array
	 * Helper function inside docOutput(...)
	 *
	 * @return bool
	 * @author  Lorand Veres
	 */
	private function check_single_tag($tag) {
		in_array($tag, $this -> single_tag) ? $b = true : $b = false;
		return $b;
	}

	/*
	 * @parameter passed is a string. Returning TRUE if if is an opening tag
	 * Helper function inside docOutput(...).
	 *
	 * @return bool
	 * @author  Lorand Veres
	 */
	private function check_start_tag($value) {
		substr($value, 0, 2) !== "</" ? $start_tag = true : $start_tag = false;
		return $start_tag;
	}

	/*
	 * @parameter passed is a string. Returning TRUE if if is a plain text, it may contain
	 * inline elements embeded in to the text.
	 * Helper function inside docOutput(...).
	 *
	 * @return bool
	 * @author  Lorand Veres
	 */
	private function check_plain_txt($value) {
		substr($value, 0, 1) !== "<" & substr($value, -1, 1) !== ">" ? $start_tag = true : $start_tag = false;
		return $start_tag;
	}

	/*
	 * Cheking the type of the tag.
	 * Helper function inside docOutput.
	 *
	 * @return bool
	 * @author  Lorand Veres
	 */
	private function check_tag_type($tag, $array) {
		$n = false;
		$txt_tag;
		if (is_array($tag)) {
			if (is_string($tag[0]))
				$txt_tag = $tag[0];
		} elseif (is_string($tag)) {
			$txt_tag = $tag;
		}
		$tag_array = explode(' ', $txt_tag);
		$tag_array[0] = str_replace('<', '', $tag_array[0]);
		$tag_array[0] = str_replace('/', '', $tag_array[0]);
		$tag_array[0] = str_replace('>', '', $tag_array[0]);
		in_array($tag_array[0], $array) ? $n = true : $n = false;
		return $n;
	}

	/**
	 *
	 *
	 * Helper function inside docOutput.
	 *
	 * @return void
	 * @author  Lorand Veres
	 */
	private function print_inline_tag($value) {
		$tf = str_pad("", $this -> indentNum + 1, "\t");
		printf("%s", $tf . $value[0] . $value[1][0] . $value[2] . "\n");
	}

	/**
	 * Helper function inside docOutput.
	 *
	 * @return void
	 * @author  Lorand Veres
	 */
	private function print_text($value) {
		$tf = str_pad("", $this -> indentNum + 1, "\t");
		if ($this -> check_plain_txt($value[0]))
			printf("%s", $tf . $value[0] . "\n");
	}

	/**
	 * Helper function inside docOutput.
	 *
	 * @return void
	 * @author  Lorand Veres
	 */
	private function print_single_tag($value) {
		$tf = str_pad("", $this -> indentNum + 1, "\t");
		printf("%s", $tf . $value . "");
	}

	/**
	 * Helper function inside docOutput.
	 *
	 * @return void
	 * @author  Lorand Veres
	 */
	private function print_block_tag($value) {
		$tf = str_pad("", $this -> indentNum, "\t");
		// checking for very first block tag if start indenting > 0
		if ($this -> set_count & $this -> indentNum > 0) {
			$value = $tf . $value . "\n";
			$this -> set_count = FALSE;
			printf("%s", $value);
		}
		// printing the block tags
		$this -> check_tag_type($value, $this -> block_tag) ? printf("%s", $tf . $value . "\n") : '';
		!$this -> check_start_tag($value) | $this -> check_tag_type($value, $this -> inline_tag) ? $this -> indentNum-- : '';
	}

	/*
	 * Helper function, appending the attributes, used inside simpleTag ->tag(..)
	 * 
	 * @return void
	 * @author  Lorand Veres
	 */
	private function setAttr($attr) {
		$attributes = '';
		if (is_array($attr) && !empty($attr)) {
			foreach ($attr as $key => $value) {
				$attributes .= ' ' . $key . '="' . $value . '"';
			}
		} elseif (is_string($attr) && !empty($attr)) {
			$attributes .= ' ' . $attr;
		}
		return $attributes;
	}

	/*
	 * A recursive function doing the heavy printing.
	 * @argument passsed is the $tag created under the form of Multidimensional Array.
	 *
	 * @return void
	 * @author  Lorand Veres
	 */
	public function docOutput($argument) {
		foreach ($argument as $key => $value) {
			if (is_array($value) && !empty($value)) {
				if (is_string($value[0])) {
					if ($this -> check_tag_type($value, $this -> block_tag)) {
						// increment the indenting if block tag
						$this -> check_start_tag($value[0]) ? $this -> indentNum++ : '';
						$this -> docOutput($value);
					} elseif ($this -> check_tag_type($value, $this -> inline_tag) & count($value) === 3) {
						$this -> print_inline_tag($value);
					} elseif (count($value) === 1) {
						$this -> print_text($value);
					}
				} else {
					$this -> docOutput($value);
				}
			} elseif (is_string($value)) {
				if ($this -> check_tag_type($value, $this -> single_tag)) {
					$this -> print_single_tag($value);
				} else {
					$this -> print_block_tag($value);
				}
			}
		}
	}

}// end of class
?>
