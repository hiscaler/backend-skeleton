<?php

namespace app\modules\admin\components;

class ApplicationHelper
{

    public static function friendlyFileSize($bytes)
    {
        $size = null;
        $bytes = (int) $bytes;
        if ($bytes) {
            $kb = 1024;
            $m = 1048576;
            $gb = 1073741824;
            if ($bytes < $kb) {
                $size = $bytes . ' Bit';
            } elseif ($bytes < $m) {
                $size = $bytes / $kb . ' KB';
            } elseif ($bytes < $gb) {
                $size = $bytes / $m . ' MB';
            } else {
                $size = $bytes / $gb . ' GB';
            }
        }

        return $size;
    }

}
