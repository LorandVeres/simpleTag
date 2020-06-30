<?php

/**
 *
 */
class simpleTag {

    public $Doc = array();
    public $inline_tag = array('a', 'b', 'big', 'i', 'small', 'tt', 'abbr', 'acronym', 'cite', 'code', 'dfn', 'em', 'kbd', 'strong', 'samp', 'time', 'var', 'bdo', 'br', 'img', 'map', 'object', 'q', 'script', 'span', 'sub', 'sup', 'button', 'input', 'label', 'select', 'textarea', 'meta', 'link');
    public $single_tag = array('!DOCTYPE html', 'meta', 'link', 'br', 'img', 'hr', 'input', 'embed', 'bgsound', 'base', 'col', 'source');
    private $countNum = 0;

    function __construct($config, $DBconect) {
    }

    public function append_tag(&$into, $tag) {
        if (!array_key_exists(1, $into)) {
            if (!array_push($into, $tag))
                ;
        } else {
            if (!array_push($into[1], $tag))
                ;
        }
    }

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
            $tag = ' ';
            $tag .= ($o . $tagname . $this -> setAttr($attr) . $c);
        }
        return $tag;
    }

    private function check_single_tag($tag) {
        in_array($tag, $this -> single_tag) ? $b = true : $b = false;
        return $b;
    }

    public function addAttr($tag, $attr) {

    }

    public function changeAttr($tag, $old, $new) {

    }

    public function addText($tag, $text) {

    }

    public function docOutput($argument) {
        foreach ($argument as $key => $value) {
            if (is_array($value) && !empty($value)) {
                if (is_string($value[0])) {
                    if (!$this -> chek_inline_tag($value, $this -> inline_tag)) {
                        $this -> indent($value);
                    } else {
                        if (count($value) === 3) {
                            printf("%s%s%s", $value[0], $value[1][0], $value[2]);
                        } elseif (count($value) <= 1) {
                            printf("%s", $value[0]);
                        }
                    }
                } else {
                    $this -> docOutput($value);
                }
            } elseif (is_string($value)) {
                printf("%s", $value);
            }
        }
    }

    private function indent($value) {
        $tf = '';
        $te = '';
        $tf = str_pad($tf, $this -> countNum, "\t");
        $te = str_pad($te, $this -> countNum - 1, "\t");
        $this -> countNum++;
        printf("\n%s", $tf);
        $this -> docOutput($value);
        printf("\n");
        $this -> countNum--;
    }

    private function chek_inline_tag($tag, $array) {
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
        $tag_array[0] = str_replace('>', '', $tag_array[0]);
        in_array($tag_array[0], $array) ? $n = true : $n = false;
        return $n;
    }

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

    public function fileBuffer($file) {
        ob_start();
        if (is_array($file)) {
            foreach ($file as $value) {
                include $value;
            }
        } elseif (is_string($file)) {
            include $file;
        }
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    private function printHeaders() {
        if ($this -> isPage === 1) {
            header('HTTP/1.0 404 Not Found');
        }
    }

}// end of class
?>