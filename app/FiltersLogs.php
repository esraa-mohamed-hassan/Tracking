<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FiltersLogs extends Model
{
    public static function FiltersLogsData($line,$file,$dir,$function,$class,$trait,$method,$namespace,$subject,$msgbody) {
        $logData = [
            'line' => $line,
            'file' => $file,
            'dir' => $dir,
            'function' => $function,
            'class' => $class,
            'trait' => $trait,
            'method' => $method,
            'namespace' => $namespace,
            'subject' => $subject,
            'msgbody' => $msgbody,
            'time' => date('Y-m-d H:i:s'),
        ];
        DB::table('filter_logs')->insertGetId($logData);
    }
}
