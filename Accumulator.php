<?php

namespace Drupal\Component\Diff;

use Drupal\Component\Utility\String;
use Drupal\Component\Utility\Unicode;

define('NBSP', '&#160;');      // iso-8859-x non-breaking space.

/**
 *  Additions by Axel Boldt follow, partly taken from diff.php, phpwiki-1.3.3
 */
class Accumulator
{
    protected $lines;
    protected $line;
    protected $group;
    protected $tag;

    public function __construct()
    {
        $this->lines = array();
        $this->line = '';
        $this->group = '';
        $this->tag = '';
    }

    public function flushGroup($new_tag)
    {
        if ($this->group !== '') {
            if ($this->tag == 'mark') {
                $this->line .= '<span class="diffchange">' . String::checkPlain($this->group) . '</span>';
            }
            else {
                $this->line .= String::checkPlain($this->group);
            }
        }
        $this->group = '';
        $this->tag = $new_tag;
    }

    public function flushLine($new_tag)
    {
        $this->flushGroup($new_tag);
        if ($this->line != '') {
            array_push($this->lines, $this->line);
        }
        else {
            // make empty lines visible by inserting an NBSP
            array_push($this->lines, NBSP);
        }
        $this->line = '';
    }

    public function addWords($words, $tag = '')
    {
        if ($tag != $this->tag) {
            $this->flushGroup($tag);
        }
        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word == '') {
                continue;
            }
            if ($word[0] == "\n") {
                $this->flushLine($tag);
                $word = Unicode::substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->group .= $word;
        }
    }

    public function getLines()
    {
        $this->flushLine('~done');

        return $this->lines;
    }
}
