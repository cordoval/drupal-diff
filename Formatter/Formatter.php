<?php

namespace Drupal\Component\Diff\Formatter;

/**
 * A class to format Diffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 */
class Formatter
{
    /**
     * Should a block header be shown?
     */
    protected $show_header = true;

    /**
     * Number of leading context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    protected $leading_context_lines = 0;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    protected $trailing_context_lines = 0;

    /**
     * Format a diff.
     *
     * @param $diff object A Diff object.
     * @return string The formatted output.
     */
    public function format($diff)
    {
        $xi = $yi = 1;
        $block = false;
        $context = array();

        $nlead = $this->leading_context_lines;
        $ntrail = $this->trailing_context_lines;

        $this->start_diff();

        foreach ($diff->edits as $edit) {
            if ($edit->type == 'copy') {
                if (is_array($block)) {
                    if (sizeof($edit->orig) <= $nlead + $ntrail) {
                        $block[] = $edit;
                    }
                    else {
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = new Copy($context);
                        }
                        $this->block($x0, $ntrail + $xi - $x0, $y0, $ntrail + $yi - $y0, $block);
                        $block = false;
                    }
                }
                $context = $edit->orig;
            }
            else {
                if (! is_array($block)) {
                    $context = array_slice($context, sizeof($context) - $nlead);
                    $x0 = $xi - sizeof($context);
                    $y0 = $yi - sizeof($context);
                    $block = array();
                    if ($context) {
                        $block[] = new Copy($context);
                    }
                }
                $block[] = $edit;
            }

            if ($edit->orig) {
                $xi += sizeof($edit->orig);
            }
            if ($edit->closing) {
                $yi += sizeof($edit->closing);
            }
        }

        if (is_array($block)) {
            $this->block($x0, $xi - $x0, $y0, $yi - $y0, $block);
        }
        $end = $this->end_diff();

        if (!empty($xi)) {
            $this->line_stats['counter']['x'] += $xi;
        }
        if (!empty($yi)) {
            $this->line_stats['counter']['y'] += $yi;
        }

        return $end;
    }

    protected function block($xbeg, $xlen, $ybeg, $ylen, &$edits)
    {
        $this->start_block($this->block_header($xbeg, $xlen, $ybeg, $ylen));
        foreach ($edits as $edit) {
            if ($edit->type == 'copy') {
                $this->context($edit->orig);
            }
            elseif ($edit->type == 'add') {
                $this->added($edit->closing);
            }
            elseif ($edit->type == 'delete') {
                $this->deleted($edit->orig);
            }
            elseif ($edit->type == 'change') {
                $this->changed($edit->orig, $edit->closing);
            }
            else {
                trigger_error('Unknown edit type', E_USER_ERROR);
            }
        }
        $this->end_block();
    }

    private function start_diff()
    {
        ob_start();
    }

    private function end_diff()
    {
        $val = ob_get_contents();
        ob_end_clean();

        return $val;
    }

    private function block_header($xbeg, $xlen, $ybeg, $ylen)
    {
        if ($xlen > 1) {
            $xbeg .= "," . ($xbeg + $xlen - 1);
        }
        if ($ylen > 1) {
            $ybeg .= "," . ($ybeg + $ylen - 1);
        }

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

    private function start_block($header)
    {
        if ($this->show_header) {
            echo $header . "\n";
        }
    }

    private function end_block()
    {
    }

    private function lines($lines, $prefix = ' ')
    {
        foreach ($lines as $line) {
            echo "$prefix $line\n";
        }
    }

    private function context($lines)
    {
        $this->lines($lines);
    }

    private function added($lines)
    {
        $this->lines($lines, '>');
    }

    private function deleted($lines)
    {
        $this->lines($lines, '<');
    }

    private function changed($orig, $closing)
    {
        $this->deleted($orig);
        echo "---\n";
        $this->added($closing);
    }
}
