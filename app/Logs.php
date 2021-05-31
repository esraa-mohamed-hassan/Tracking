<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Logs extends Model
{
    public static function logdata($line,$file,$dir,$function,$class,$trait,$method,$namespace,$subject,$msgbody) {
        $user_id = Auth::id();
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
            'user_id' => $user_id,
            'time' => date('Y-m-d H:i:s'),
        ];
        DB::table('logs')->insertGetId($logData);
    }
}
