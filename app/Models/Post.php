<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    // Kolom yang dapat diisi secara massal
    protected $fillable = ['title', 'content', 'status', 'user_id'];

    // Mengaktifkan update created_at & updated_at otomatis
    public $timestamps = true;
}