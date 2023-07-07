<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $appends = [
        'full_name'  
    ];

    public function getFullNameAttribute() {
        return $this->first_name.' '.$this->last_name;
    }
}
