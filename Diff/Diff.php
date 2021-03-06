<?php

namespace Drupal\Component\Diff\Diff;

use Drupal\Component\Diff\Engine;

/**
 * Class representing a 'diff' between two sequences of strings.
 */
class Diff
{
    protected $edits;

    /**
     * Constructor.
     * Computes diff between sequences of strings.
     *
     * @param $from_lines array An array of strings.
     *      (Typically these are lines from a file.)
     * @param $to_lines array An array of strings.
     */
    public function __construct($from_lines, $to_lines)
    {
        $eng = new Engine;
        $this->edits = $eng->diff($from_lines, $to_lines);
        $this->check($from_lines, $to_lines);
    }

    /**
     * Compute reversed Diff.
     *
     * SYNOPSIS:
     *
     *  $diff = new Diff($lines1, $lines2);
     *  $rev = $diff->reverse();
     * @return object A Diff object representing the inverse of the
     *          original diff.
     */
    public function reverse()
    {
        $rev = $this;
        $rev->edits = array();
        foreach ($this->edits as $edit) {
            $rev->edits[] = $edit->reverse();
        }
        return $rev;
    }

    /**
     * Check for empty diff.
     *
     * @return bool True iff two sequences were identical.
     */
    public function isEmpty()
    {
        foreach ($this->edits as $edit) {
            if ($edit->type != 'copy') {
                return false;
            }
        }
        return true;
    }

    /**
     * Compute the length of the Longest Common Subsequence (LCS).
     *
     * This is mostly for diagnostic purposed.
     *
     * @return int The length of the LCS.
     */
    public function lcs()
    {
        $lcs = 0;
        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy') {
                $lcs += sizeof($edit->orig);
            }
        }

        return $lcs;
    }

    /**
     * Get the original set of lines.
     *
     * This reconstructs the $from_lines parameter passed to the
     * constructor.
     *
     * @return array The original sequence of strings.
     */
    public function orig()
    {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->orig) {
                array_splice($lines, sizeof($lines), 0, $edit->orig);
            }
        }

        return $lines;
    }

    /**
     * Get the closing set of lines.
     *
     * This reconstructs the $to_lines parameter passed to the
     * constructor.
     *
     * @return array The sequence of strings.
     */
    public function closing()
    {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->closing) {
                array_splice($lines, sizeof($lines), 0, $edit->closing);
            }
        }

        return $lines;
    }

    /**
     * Check a Diff for validity.
     *
     * This is here only for debugging purposes.
     */
    public function check($from_lines, $to_lines)
    {
        if (serialize($from_lines) != serialize($this->orig())) {
            trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
        }
        if (serialize($to_lines) != serialize($this->closing())) {
            trigger_error("Reconstructed closing doesn't match", E_USER_ERROR);
        }

        $rev = $this->reverse();
        if (serialize($to_lines) != serialize($rev->orig())) {
            trigger_error("Reversed original doesn't match", E_USER_ERROR);
        }
        if (serialize($from_lines) != serialize($rev->closing())) {
            trigger_error("Reversed closing doesn't match", E_USER_ERROR);
        }


        $prevtype = 'none';
        foreach ($this->edits as $edit) {
            if ( $prevtype == $edit->type ) {
                trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
            }
            $prevtype = $edit->type;
        }

        $lcs = $this->lcs();
        trigger_error('Diff okay: LCS = ' . $lcs, E_USER_NOTICE);
    }
}
