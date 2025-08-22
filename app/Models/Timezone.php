<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Timezone.php
class Timezone extends Model
{
    protected $fillable = ['name', 'display_name', 'offset'];

    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }
}

