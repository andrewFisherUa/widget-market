<?php

namespace App\Console\Commands\Obmenneg\Table;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminated\Console\WithoutOverlapping;
use ZipArchive;

class BestChange extends Command
{
	use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LocalBtc:best_change_v2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_btc (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_btc WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_btc set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);

		$temp_filename = "info.zip";
		$fp = fopen($temp_filename, "w");
		fputs($fp, file_get_contents("http://www.bestchange.ru/bm/info.zip"));
		fclose($fp);
		$zip = new ZipArchive;
		if (!$zip->open($temp_filename)) exit("error");
		$currencies = array();
		foreach (explode("\n", $zip->getFromName("bm_cy.dat")) as $value) {
			$entry = explode(";", $value);
			$currencies[$entry[0]] = $entry[2];
		}
		ksort($currencies);
		$exchangers = array();
		foreach (explode("\n", $zip->getFromName("bm_exch.dat")) as $value) {
			$entry = explode(";", $value);
			$exchangers[$entry[0]] = $entry[1];
		}
		ksort($exchangers);
		$rates = array();
		foreach (explode("\n", $zip->getFromName("bm_rates.dat")) as $value) {
			$entry = explode(";", $value);
			//var_dump($entry);
			if ($entry[4]>0){
				$rates[$entry[0]][$entry[1]][$entry[2]] = array("rate"=>$entry[3] / $entry[4], "reserve"=>$entry[5]);
			}
		}
		$zip->close();
		unlink($temp_filename);
		
		//Bitcoin -> Cash
		$buy_cash=0;
		$from_cy = 93;//btc
		$to_cy = 91;//наличка
			if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_cash=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Cash -> Bitcoin
		$sell_cash=0;
		$from_cy = 91;
		$to_cy = 93;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_cash=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Bitcoin -> Sber
		$buy_bank=0;
		$from_cy = 93;
		$to_cy = 42;//Сбер
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			$buy_bank=0;
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_bank=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Sber -> Bitcoin
		$sell_bank=0;
		$from_cy = 42;
		$to_cy = 93;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_bank=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Bitcoin -> Wmr
		$buy_wmr=0;
		$from_cy = 93;
		$to_cy = 2;//Wmr
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_wmr=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Wmr -> Bitcoin
		$sell_wmr=0;
		$from_cy = 2;
		$to_cy = 93;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_wmr=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Bitcoin -> Yandex
		$buy_ym=0;
		$from_cy = 93;
		$to_cy = 6;//Яндекс
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_ym=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Yandex -> Bitcoin
		$sell_ym=0;
		$from_cy = 6;
		$to_cy = 93;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_ym=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Bitcoin -> Qiwi
		$buy_qiwi=0;
		$from_cy = 93;
		$to_cy = 63;//Qiwi
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_qiwi=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Qiwi -> Bitcoin
		$sell_qiwi=0;
		$from_cy = 63;
		$to_cy = 93;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_qiwi=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'БестЧендж']);
		$sthInsert->execute(['БестЧендж',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'БестЧендж']);
		
		//Ethereum
		$pdo = \DB::connection("obmenneg")->getPdo();
		$sql="insert into table_eth (name,buy_cash,sell_cash,buy_bank,sell_bank,buy_wmr,sell_wmr,buy_ym,sell_ym,buy_qiwi,sell_qiwi)
			select ?,?,?,?,?,?,?,?,?,?,? WHERE NOT EXISTS (SELECT 1 FROM table_eth WHERE name=?)";
		$sthInsert=$pdo->prepare($sql);
		$sql="update table_eth set buy_cash=?,sell_cash=?,buy_bank=?,sell_bank=?,buy_wmr=?,sell_wmr=?,buy_ym=?,sell_ym=?,buy_qiwi=?,sell_qiwi=?
			WHERE name=?";
		$sthUpdate=$pdo->prepare($sql);
		
		//Ethereum -> Cash
		$buy_cash=0;
		$from_cy = 139;//btc
		$to_cy = 91;//наличка
			if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_cash=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Cash -> Ethereum
		$sell_cash=0;
		$from_cy = 91;
		$to_cy = 139;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_cash=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Ethereum -> Sber
		$buy_bank=0;
		$from_cy = 139;
		$to_cy = 42;//Сбер
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			$buy_bank=0;
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_bank=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Sber -> Ethereum
		$sell_bank=0;
		$from_cy = 42;
		$to_cy = 139;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_bank=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Ethereum -> Wmr
		$buy_wmr=0;
		$from_cy = 139;
		$to_cy = 2;//Wmr
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_wmr=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Wmr -> Ethereum
		$sell_wmr=0;
		$from_cy = 2;
		$to_cy = 139;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_wmr=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Ethereum -> Yandex
		$buy_ym=0;
		$from_cy = 139;
		$to_cy = 6;//Яндекс
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_ym=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Yandex -> Ethereum
		$sell_ym=0;
		$from_cy = 6;
		$to_cy = 139;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_ym=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		//Ethereum -> Qiwi
		$buy_qiwi=0;
		$from_cy = 139;
		$to_cy = 63;//Qiwi
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$buy_qiwi=($entry["rate"] < 1 ? 1 / $entry["rate"] : 1);
				break;
			}
		}
		
		//Qiwi -> Ethereum
		$sell_qiwi=0;
		$from_cy = 63;
		$to_cy = 139;
		if (isset($rates[$from_cy][$to_cy])){
			uasort($rates[$from_cy][$to_cy], function ($a, $b) {
				if ($a["rate"] > $b["rate"]) return 1;
				if ($a["rate"] < $b["rate"]) return -1;
				return(0);
			});
			foreach ($rates[$from_cy][$to_cy] as $key=>$entry) {
				$sell_qiwi=($entry["rate"] < 1 ? 1 : $entry["rate"]);
				break;
			}
		}
		
		$sthUpdate->execute([$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'БестЧендж']);
		$sthInsert->execute(['БестЧендж',$buy_cash,$sell_cash,$buy_bank,$sell_bank,$buy_wmr,$sell_wmr,$buy_ym,$sell_ym,$buy_qiwi,$sell_qiwi,'БестЧендж']);
	}
}
