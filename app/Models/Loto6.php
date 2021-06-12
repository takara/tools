<?php

/**
 * Created by Reliese Model.
 * artisan code:models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Loto6
 * 
 * @property int $id
 * @property int $date
 * @property int $num1
 * @property int $num2
 * @property int $num3
 * @property int $num4
 * @property int $num5
 * @property int $num6
 * @property int $numb
 * @property int $hitnum1
 * @property int $hitnum2
 * @property int $hitnum3
 * @property int $hitnum4
 * @property int $hitnum5
 * @property int $reward1
 * @property int $reward2
 * @property int $reward3
 * @property int $reward4
 * @property int $reward5
 * @property int $carryover
 *
 * @package App\Models
 */
class Loto6 extends Model
{
	protected $table = 'loto6';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'date' => 'int',
		'num1' => 'int',
		'num2' => 'int',
		'num3' => 'int',
		'num4' => 'int',
		'num5' => 'int',
		'num6' => 'int',
		'numb' => 'int',
		'hitnum1' => 'int',
		'hitnum2' => 'int',
		'hitnum3' => 'int',
		'hitnum4' => 'int',
		'hitnum5' => 'int',
		'reward1' => 'int',
		'reward2' => 'int',
		'reward3' => 'int',
		'reward4' => 'int',
		'reward5' => 'int',
		'carryover' => 'int'
	];

	protected $fillable = [
		'date',
		'num1',
		'num2',
		'num3',
		'num4',
		'num5',
		'num6',
		'numb',
		'hitnum1',
		'hitnum2',
		'hitnum3',
		'hitnum4',
		'hitnum5',
		'reward1',
		'reward2',
		'reward3',
		'reward4',
		'reward5',
		'carryover'
	];
}
