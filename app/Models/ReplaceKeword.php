<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReplaceKeword
 * 
 * @property int $id
 * @property string $pattern
 * @property string|null $keywoed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ReplaceKeword extends Model
{
	protected $table = 'replace_keword';

	protected $fillable = [
		'pattern',
		'keywoed'
	];
}
