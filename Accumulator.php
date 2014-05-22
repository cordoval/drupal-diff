<?php

namespace Drupal\Component\Diff;

/**
 *  Additions by Axel Boldt follow, partly taken from diff.php, phpwiki-1.3.3
 *
 */

define('NBSP', '&#160;');      // iso-8859-x non-breaking space.

class Accumulator
{
    function __construct() {
        $this->_lines = array();
        $this->_line = '';
        $this->_group = '';
        $this->_tag = '';
    }

    function flushGroup($new_tag) {
        if ($this->_group !== '') {
            if ($this->_tag == 'mark') {
                $this->_line .= '<span class="diffchange">' . String::checkPlain($this->_group) . '</span>';
            }
            else {
                $this->_line .= String::checkPlain($this->_group);
            }
        }
        $this->_group = '';
        $this->_tag = $new_tag;
    }

    function flushLine($new_tag) {
        $this->_flushGroup($new_tag);
        if ($this->_line != '') {
            array_push($this->_lines, $this->_line);
        }
        else {
            // make empty lines visible by inserting an NBSP
            array_push($this->_lines, NBSP);
        }
        $this->_line = '';
    }

    function addWords($words, $tag = '') {
        if ($tag != $this->_tag) {
            $this->_flushGroup($tag);
        }
        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word == '') {
                continue;
            }
            if ($word[0] == "\n") {
                $this->_flushLine($tag);
                $word = Unicode::substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->_group .= $word;
        }
    }

    function getLines() {
        $this->_flushLine('~done');
        return $this->_lines;
    }
}

