<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rss extends Model
{
    use HasFactory;
	protected $table = 'rss';

	protected $fillable = [
		'url',
		'title',
		'notice',
		'created_at',
		'updated_at',
	];
	protected $primaryKey = 'url';
}
