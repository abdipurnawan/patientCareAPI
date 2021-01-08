<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $fillable = [
        'id', 'id_user','poli','keluhan','tgl_regis','penyakit_bawaan','tinggi_badan','status','berat_badan','accepted_at','created_at','updated_at',
    ];
}
