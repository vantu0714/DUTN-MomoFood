<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand;
use Carbon\Carbon;

class ServeFixedCommand extends ServeCommand
{
    /**
     * Parse the date from PHP server log line (fix lỗi Undefined array key).
     */
    protected function getDateFromLine($line)
    {
        $regex = '/^\[(.*?)\]/';
        preg_match($regex, $line, $matches);

        return isset($matches[1])
            ? Carbon::parse($matches[1])
            : now();
    }
}
