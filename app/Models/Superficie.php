<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Superficie extends Model
{
    public function campo()
    {
        return $this->belongsTo(Campo::class);
    }

}
