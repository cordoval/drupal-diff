<?php

namespace Drupal\Component\Diff\Diff;

use Drupal\Component\Utility\Unicode;

class WordLevelDiff extends MappedDiff
{
    public function MAX_LINE_LENGTH()
    {
        return 10000;
    }

    public function __construct($orig_lines, $closing_lines)
    {
        list($orig_words, $orig_stripped) = $this->split($orig_lines);
        list($closing_words, $closing_stripped) = $this->split($closing_lines);

        $this->MappedDiff($orig_words, $closing_words, $orig_stripped, $closing_stripped);
    }

    public function split($lines)
    {
        $words = array();
        $stripped = array();
        $first = true;
        foreach ($lines as $line) {
            // If the line is too long, just pretend the entire line is one big word
            // This prevents resource exhaustion problems
            if ( $first ) {
                $first = false;
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

    public function orig()
    {
        $orig = new Accumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy') {
                $orig->addWords($edit->orig);
            } elseif ($edit->orig) {
                $orig->addWords($edit->orig, 'mark');
            }
        }
        $lines = $orig->getLines();

        return $lines;
    }

    public function closing()
    {
        $closing = new Accumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy') {
                $closing->addWords($edit->closing);
            } elseif ($edit->closing) {
                $closing->addWords($edit->closing, 'mark');
            }
        }
        $lines = $closing->getLines();

        return $lines;
    }
}
