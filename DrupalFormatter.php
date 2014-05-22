<?php

/**
 * Diff formatter which uses Drupal theme functions.
 * @private
 * @subpackage DifferenceEngine
 */
class DrupalDiffFormatter extends DiffFormatter {

    var $rows;
    var $line_stats = array(
        'counter' => array('x' => 0, 'y' => 0),
        'offset' => array('x' => 0, 'y' => 0),
    );

    function DrupalDiffFormatter() {
        $this->leading_context_lines = Settings::get('diff_context_lines_leading', 2);
        $this->trailing_context_lines = Settings::get('diff_context_lines_trailing', 2);
    }

    function _start_diff() {
        $this->rows = array();
    }

    function _end_diff() {
        return $this->rows;
    }

    function _block_header($xbeg, $xlen, $ybeg, $ylen) {
        return array(
            array(
                'data' => $xbeg + $this->line_stats['offset']['x'],
                'colspan' => 2,
            ),
            array(
                'data' => $ybeg + $this->line_stats['offset']['y'],
                'colspan' => 2,
            )
        );
    }

    function _start_block($header) {
        if ($this->show_header) {
            $this->rows[] = $header;
        }
    }

    function _end_block() {
    }

    function _lines($lines, $prefix=' ', $color='white') {
    }

    /**
     * Note: you should HTML-escape parameter before calling this.
     */
    function addedLine($line) {
        return array(
            array(
                'data' => '+',
                'class' => 'diff-marker',
            ),
            array(
                'data' => $line,
                'class' => 'diff-context diff-addedline',
            )
        );
    }

    /**
     * Note: you should HTML-escape parameter before calling this.
     */
    function deletedLine($line) {
        return array(
            array(
                'data' => '-',
                'class' => 'diff-marker',
            ),
            array(
                'data' => $line,
                'class' => 'diff-context diff-deletedline',
            )
        );
    }

    /**
     * Note: you should HTML-escape parameter before calling this.
     */
    function contextLine($line) {
        return array(
            '&nbsp;',
            array(
                'data' => $line,
                'class' => 'diff-context',
            )
        );
    }

    function emptyLine() {
        return array(
            '&nbsp;',
            '&nbsp;',
        );
    }

    function _added($lines) {
        foreach ($lines as $line) {
            $this->rows[] = array_merge($this->emptyLine(), $this->addedLine(String::checkPlain($line)));
        }
    }

    function _deleted($lines) {
        foreach ($lines as $line) {
            $this->rows[] = array_merge($this->deletedLine(String::checkPlain($line)), $this->emptyLine());
        }
    }

    function _context($lines) {
        foreach ($lines as $line) {
            $this->rows[] = array_merge($this->contextLine(String::checkPlain($line)), $this->contextLine(String::checkPlain($line)));
        }
    }

    function _changed($orig, $closing) {
        $diff = new WordLevelDiff($orig, $closing);
        $del = $diff->orig();
        $add = $diff->closing();

        // Notice that WordLevelDiff returns HTML-escaped output.
        // Hence, we will be calling addedLine/deletedLine without HTML-escaping.

        while ($line = array_shift($del)) {
            $aline = array_shift( $add );
            $this->rows[] = array_merge($this->deletedLine($line), isset($aline) ? $this->addedLine($aline) : $this->emptyLine());
        }
        foreach ($add as $line) {  // If any leftovers
            $this->rows[] = array_merge($this->emptyLine(), $this->addedLine($line));
        }
    }
}
