<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'street', 'phone', 'note', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
