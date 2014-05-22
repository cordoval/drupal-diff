<?php


class _DiffOp_Add extends _DiffOp {
    var $type = 'add';

    function _DiffOp_Add($lines) {
        $this->closing = $lines;
        $this->orig = FALSE;
    }

    function reverse() {
        return new _DiffOp_Delete($this->closing);
    }
}