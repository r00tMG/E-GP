<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;
    protected $table = 'annonces';
    protected $guarded = [];
    public function gp()
    {
        return $this->belongsTo(User::class,  'gp_id');
    }

}
