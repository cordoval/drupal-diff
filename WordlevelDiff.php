<?php

namespace Drupal\Component\Diff;

class WordLevelDiff extends MappedDiff {
    function MAX_LINE_LENGTH() {
        return 10000;
    }

    function WordLevelDiff($orig_lines, $closing_lines) {
        list($orig_words, $orig_stripped) = $this->_split($orig_lines);
        list($closing_words, $closing_stripped) = $this->_split($closing_lines);

        $this->MappedDiff($orig_words, $closing_words, $orig_stripped, $closing_stripped);
    }

    function _split($lines) {
        $words = array();
        $stripped = array();
        $first = TRUE;
        foreach ($lines as $line) {
            // If the line is too long, just pretend the entire line is one big word
            // This prevents resource exhaustion problems
            if ( $first ) {
                $first = FALSE;
            }
            else {
                $words[] = "\n";
                $stripped[] = "\n";
            }
            if ( Unicode::strlen( $line ) > $this->MAX_LINE_LENGTH() ) {
                $words[] = $line;
                $stripped[] = $line;
            }
            else {
                if (preg_match_all('/ ( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . ) (?: (?!< \n) [^\S\n])? /xs', $line, $m)) {
                    $words = array_merge($words, $m[0]);
                    $stripped = array_merge($stripped, $m[1]);
                }
            }
        }
        return array($words, $stripped);
    }

    function orig() {
        $orig = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy') {
                $orig->addWords($edit->orig);
            }
            elseif ($edit->orig) {
                $orig->addWords($edit->orig, 'mark');
            }
        }
        $lines = $orig->getLines();
        return $lines;
    }

    function closing() {
        $closing = new _HWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy') {
                $closing->addWords($edit->closing);
            }
            elseif ($edit->closing) {
                $closing->addWords($edit->closing, 'mark');
            }
        }
        $lines = $closing->getLines();
        return $lines;
    }
}
