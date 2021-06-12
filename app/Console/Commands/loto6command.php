<?php

namespace App\Console\Commands;

use App\Models\Loto6;
use Illuminate\Console\Command;

/**
 * ロト6
 *
 * @see https://stillat.com/blog/2016/12/03/custom-command-styles-with-laravel-artisan
 * @package App\Console\Commands
 */
class loto6Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:loto6 {--m|mode=standard : 算出方法} {--c|count=5 : 購入数} {--g|getresolt=0 : 抽選回当選結果取得} {--a|getall : 未取得結果取得}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ロト6の1購入分の数値をランダムで出します';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		mt_srand(time());
		$mode = $this->option('mode');
		$cnt = $this->option('count');
		$no = $this->option('getresolt');
		if ($no > 0) {
			$this->getResolt($no);
			return;
		}
		$all = $this->option('getall');
		if ($all) {
			$this->line("全取得");
			$this->getResoltAll();
			return;
		}
		$this->line("mode[$mode]");
		$func = "run".ucwords($mode);
		if (!method_exists($this, $func)) {
			throw new \Exception("不正なモード({$mode})です");
		}
		for ($i = 0; $i < $cnt; $i++) {
			$w = $this->$func();
			sort($w);
			$res[] = $w;
			$buf = "";
			for ($j = 0; $j < 6; $j++) {
				$buf .= sprintf("[%02d]", $w[$j]);
			}
			$this->line($buf);
		}
	}

	protected function getResoltAll()
	{
		$maxNo = Loto6::max('id');
		$no = $maxNo;
		do {
			$no++;
			$sts = $this->getResolt($no);
			if ($sts) {
				$this->info("第{$no}回の結果を取り込みました");
			}
		} while($sts);
	}

	protected function downloadResultCSV(int $no)
	{
		//https://www.mizuhobank.co.jp/retail/takarakuji/loto/loto6/csv/A1021506.CSV
		$url = sprintf("https://www.mizuhobank.co.jp/retail/takarakuji/loto/loto6/csv/A102%04d.CSV", $no);
		try {
			$csv = file_get_contents($url);
		} catch(\Exception $e) {
			$msg = $e->getMessage();
			if (strpos($msg, "404") === false) {
				$this->error($msg);
			}
			return [];
		}
		$file = mb_convert_encoding($csv, "utf8", "sjis");
		$file = explode("\n", $file);
		return $file;
	}

	protected function getResolt(int $no)
	{
		$loto6 = Loto6::find($no);
		if (empty($loto6) === false) {
			$this->error("第{$no}回は既に取り込んでいます");
			return false;
		}
		$file = $this->downloadResultCSV($no);
		if (empty($file)) {
			$this->error("第{$no}回の結果CSVを読み込めませんでした");
			return false;
		}
		$ptnList = [
			["第([0-9]+)回ロト６", "id"],
			["数字選択式全国自治宝くじ,平成([0-9]+)年([0-9]+)月([0-9]+)日", "hnen","tuki","hi"],
			["数字選択式全国自治宝くじ,令和([0-9]+)年([0-9]+)月([0-9]+)日", "rnen","tuki","hi"],
			["本数字,([0-9]+),([0-9]+),([0-9]+),([0-9]+),([0-9]+),([0-9]+),ボーナス数字,([0-9]+)", "num1","num2","num3","num4","num5","num6","numb"],
			["１等,([0-9]+)口,([0-9]+)円","hitnum1","reward1"],
			["２等,([0-9]+)口,([0-9]+)円","hitnum2","reward2"],
			["３等,([0-9]+)口,([0-9]+)円","hitnum3","reward3"],
			["４等,([0-9]+)口,([0-9]+)円","hitnum4","reward4"],
			["５等,([0-9]+)口,([0-9]+)円","hitnum5","reward5"],
			["キャリーオーバー,([0-9]+)円","carryover"],
		];

		$ret = [
			"hitnum1"   => 0,
			"reward1"   => 0,
			"hitnum2"   => 0,
			"reward2"   => 0,
			"carryover" => 0,
		];
		foreach($file as $line) {
			foreach($ptnList as  $param) {
				$ptn = array_shift($param);
				if(preg_match("/{$ptn}/",$line, $match)) {
					array_shift($match);
					foreach($param as $field) {
						$val = array_shift($match);
						$ret[$field] = $val;
					}
				}
			}
		}
		if (isset($ret["hnen"])) {
			$ret["date"] = mktime(0,0,0,$ret["tuki"],$ret["hi"],$ret["hnen"]+1988);
		}
		if (isset($ret["rnen"])) {
			$ret["date"] = mktime(0,0,0,$ret["tuki"],$ret["hi"],$ret["rnen"]+2018);
		}
		if (!isset($ret["date"])) {
			$this->error("日付を解析出来ませんでした");
			$this->line( implode("",$file));
			return false;
		}
		$loto6 = new Loto6($ret);
		$loto6->save();

		return true;
	}

	protected function runStandard()
	{
		static $list = [];
		if (count($list) < 6) {
			$list = range(1,43);
		}
		$ret = [];
		for($i=0; $i<6;$i++) {
			$cnt = count($list);
			$idx = mt_rand(0, $cnt-1);
			$no = $list[$idx];
			unset($list[$idx]);
			$list = array_values($list);
			$ret[] = $no;
		}
		asort($ret);
		return $ret;
	}

	protected function runOld()
	{
		static $func_idx = 0;
		$funcs = [
			"runMaxCountsWeight",
			"runMaxCountsWeight",
			"runMinCountsWeight",
			"runMinCountsWeight",
			"runRandom",
		];
		$func = $funcs[$func_idx % count($funcs)];
		$func_idx++;
		return $this->$func();
	}

	protected function runMaxMin()
	{
		static $func_idx = 0;
		$funcs = [
			"runMaxCounts",
			"runMaxCountsWeight",
			"runMinCounts",
			"runMinCountsWeight",
			"runRandom",
		];
		$func = $funcs[$func_idx % count($funcs)];
		$func_idx++;
		return $this->$func();
	}

	/**
	 * 出現回数の多い数字
	 */
	protected function runMaxCounts()
	{
		$nums = $this->getNumCounts();
		asort($nums);
		return array_slice(array_keys($nums), 0, 6);
	}

	/**
	 * 出現回数の少ない数字
	 */
	protected function runMinCounts()
	{
		$nums = $this->getNumCounts();
		arsort($nums);
		return array_slice(array_keys($nums), 0, 6);
	}

	/**
	 * 出現回数の多い数字(割合)
	 */
	protected function runMaxCountsWeight()
	{
		$nums = $this->getNumCounts();
		asort($nums);
		$ret = [];
		for ($i = 0; $i < 6; $i++) {
			$sum = array_sum($nums);
			$wheight = 0;
			$rand = rand() % $sum;
			foreach ($nums as $no => $num_wheight) {
				$wheight += $num_wheight;
				if ($wheight > $rand) {
					$ret[] = $no;
					unset($nums[$no]);
					break;
				}
			}
		}
		return $ret;
	}

	/**
	 * 出現回数の少ない数字(割合)
	 */
	protected function runMinCountsWeight()
	{
		$nums = $this->getNumCounts();
		arsort($nums);
		$keys = array_keys($nums);
		$vals = array_reverse(array_values($nums));
		$nums = array_combine($keys, $vals);
		$ret = [];
		for ($i = 0; $i < 6; $i++) {
			$sum = array_sum($nums);
			$wheight = 0;
			$rand = rand() % $sum;
			foreach ($nums as $no => $num_wheight) {
				$wheight += $num_wheight;
				if ($wheight > $rand) {
					$ret[] = $no;
					unset($nums[$no]);
					break;
				}
			}
		}
		return $ret;
	}

	protected function getNumCounts()
	{
		if (!empty($this->numCounts)) {
			return $this->numCounts;
		}
		$num = array_fill(1, 43, 0);
		$fields = ["num1", "num2", "num3", "num4", "num5", "num6", "numb"];
		$data = Loto6::all($fields);
		foreach ($data as $rec) {
			foreach ($fields as $field) {
				$num[$rec[$field]]++;
			}
		}
		$this->numCounts = $num;
		return $num;
	}

	protected function runRandom()
	{
		static $num = [];
		if (count($num) < 6) {
			$num = range(1,43);
		}
		$res = [];
		for ($j = 0; $j < 6; $j++) {
			shuffle($num);
			$n = (rand() % count($num));
			$res[] = $num[$n];
			unset($num[$n]);
		}
		return $res;
	}

	public function runGetLoto6($backNumber)
	{
		print "aaa\n";
		$path = getenv("HOME")."/.loto6";
		if (!file_exists($path) || !is_dir($path)) {
			mkdir($path);
		}
		$db = $this->getDB();
		$file = $path . "/cache";
		$cache_file_exists = false;
		if (file_exists($file)) {
			$cache_file_exists = true;
			$stat = stat($file);
		}
		if (is_null($backNumber) && $cache_file_exists && (time() - $stat['mtime']) < 24 * 60 * 60) {
			print "キャッシュ有効\n";
			$html = file_get_contents($file);
		} else {
			// https://www.mizuhobank.co.jp/retail/takarakuji/loto/loto6/csv/A1021273.CSV?1538789081994
			if (is_null($backNumber)) {
				$html = file_get_contents("https://www.mizuhobank.co.jp/retail/takarakuji/loto/loto6/index.html");
			} else {
				$year  = (int)substr($backNumber, 0, 4);
				$month = (int)substr($backNumber, 4, 2);
				$html  = file_get_contents("https://www.mizuhobank.co.jp/retail/takarakuji/loto/loto6/index.html?year={$year}&month={$month}");
			}
			print "キャッシュ無効\n";
			file_put_contents($file, $html);
		}

		if(!preg_match_all('/(<table(?:.+?)<\/table>)/ms', $html, $match)) {
			die("見つかりませんでした\n");
		}
		print "$html\n";
		foreach ($match[1] as $table) {
		//	print "[$table]\n";
			$domDocument = new DOMDocument();
			$domDocument->loadHTML(mb_convert_encoding($table, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
			$xmlString = $domDocument->saveXML();
			$sxml = simplexml_load_string($xmlString);
			$sxml = $sxml->body->table;
			if (!preg_match('/^第([0-9]+)回$/', (string)$sxml->thead->tr->th[1], $match)) {
				continue;
			}
			$no = $match[1];
			$res = $db->getRow("SELECT * FROM loto6 WHERE id = ?", [$no], DB_FETCHMODE_ASSOC);
			if (!is_null($res)) {
				continue;
			}
			print date("Y/m/d H:i:s") . " no[$no]\n";
			preg_match("/([0-9]+)年([0-9]+)月([0-9]+)日/", (string)$sxml->tbody->tr[0]->td, $match);
			$date = strtotime("{$match[1]}/{$match[2]}/{$match[3]}");

			preg_match("/\(([0-9]+)\)/", (string)$sxml->tbody->tr[2]->td->strong, $match);
			$numb = $match[1];

			$data = [
				$no,
				$date,
				(string)$sxml->tbody->tr[1]->td[0]->strong,
				(string)$sxml->tbody->tr[1]->td[1]->strong,
				(string)$sxml->tbody->tr[1]->td[2]->strong,
				(string)$sxml->tbody->tr[1]->td[3]->strong,
				(string)$sxml->tbody->tr[1]->td[4]->strong,
				(string)$sxml->tbody->tr[1]->td[5]->strong,
				$numb,

				(int)str_replace(",", "", (string)$sxml->tbody->tr[3]->td[0]),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[4]->td[0]),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[5]->td[0]),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[6]->td[0]),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[7]->td[0]),

				(int)str_replace(",", "", (string)$sxml->tbody->tr[3]->td[1]->strong),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[4]->td[1]->strong),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[5]->td[1]->strong),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[6]->td[1]->strong),
				(int)str_replace(",", "", (string)$sxml->tbody->tr[7]->td[1]->strong),

				(int)str_replace(",", "", (string)$sxml->tbody->tr[9]->td[0]->strong)
			];
			$str = implode(",", array_fill(0, count($data),"?"));
			$res = $db->query("INSERT INTO loto6 VALUES ($str)", $data);
			if (DB::isError($res)) {
				print $res->getUserInfo()."\n";
			}
		}
	}
}

