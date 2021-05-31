<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportProducts extends Model
{
    protected $table = 'import_products';
    protected $fillable = [
        'symbol_product','ar_name','en_name','qty','sku','status'
    ];
}
