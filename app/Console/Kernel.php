<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        #Commands\Videostatistic::class,
		#Commands\Videoframestatistic::class,
		#Commands\Videocomission::class,
		#Commands\Videorecalc::class,
		Commands\WidgetRewriteTemplate::class,
		Commands\Videofilter::class,
		Commands\Statistic\Video::class,
		Commands\Statistic\MultyVideo::class, 
		Commands\Videosources::class,
		Commands\PerenosUsers::class,
		Commands\Statistic\VideoStat::class,
		Commands\Statistic\VideoStatet::class,
		Commands\Statistic\NewVideo::class,
		Commands\Statistic\ViewableStat::class,
		Commands\Statistic\TeaserStat::class,
		Commands\Statistic\TeaserKokosStat::class,
		Commands\Statistic\Kokos::class,
		Commands\Statistic\YandexStat::class,
		Commands\Statistic\NadaviStat::class,
		Commands\Statistic\TeasernetStat::class,
		Commands\Statistic\Mystat::class,
		Commands\Statistic\Partner::class,
		Commands\Statistic\VideoIp::class,
		Commands\Statistic\NgDetail::class,
		 
		Commands\VideoError::class,
		Commands\Statistic\VideoStatOnDB::class,
		Commands\Statistic\InsertStat::class,
		Commands\Statistic\StatAdvert::class,
		Commands\Statistic\Util::class,
	    Commands\Statistic\Calculator::class,
		Commands\Statistic\CalculatorB::class,
		Commands\Widgets\Getfrom::class,
		Commands\Transaction\Calculate::class,
		Commands\Transaction\CalculateToday::class,
		Commands\PadClid::class,
		Commands\Statistic\ProductSumma::class,
        Commands\Statistic\AdvertSumma::class,		
		Commands\Statistic\Analysis\Advert::class,
		Commands\Statistic\Analysis\Partner::class,
		
		Commands\UserActive::class,
		Commands\UserActiveDown::class,
		Commands\NoConfirm::class,
		Commands\Transaction\AutoPayment::class,
		Commands\uuu::class,
		Commands\VideoBot::class,
		Commands\ProductBot::class,
		Commands\tes::class,
		Commands\Utraf::class,
		Commands\Statistic\Refer::class,
		//Commands\rt::class,
		//Commands\Statistic\PerenosStati::class
		
		Commands\Next\Product::class,
		Commands\Next\Vendor::class,
		Commands\Statistic\RequestTime::class,
		Commands\Statistic\BrandStat::class,
		Commands\IPtraf::class,
		
		Commands\Obmenneg\LocalBtcAllAds::class,
		Commands\Obmenneg\Sms::class,
		Commands\Obmenneg\Robot::class,
		Commands\Obmenneg\Balancing::class,
		Commands\Obmenneg\Parsers\Qiwi::class,
		Commands\Obmenneg\Parsers\Yandex::class,
		Commands\Obmenneg\Parsers\Banks::class,
		Commands\Obmenneg\Parsers\Banks_two::class,
		Commands\Obmenneg\Parsers\WMR::class,
		Commands\Obmenneg\Parsers\WMZ::class,
		Commands\Obmenneg\Parsers\Lbtc::class,
		Commands\Obmenneg\Parsers\BestChange::class,
		Commands\Obmenneg\Parsers\KursExpert::class,
		Commands\Obmenneg\Parsers\Obmenneg::class,
		Commands\Obmenneg\Parsers\MeLbtc::class,
		Commands\Obmenneg\Parsers\Bitfinex::class,
		
		Commands\Obmenneg\Table\KursExpert::class,
		Commands\Obmenneg\Table\BestChange::class,
		Commands\Obmenneg\Table\Lbtc::class,
		Commands\Obmenneg\Table\Obmenneg::class,
		Commands\Obmenneg\Table\Birges::class,
		Commands\Obmenneg\Table\Graf::class,
		
		Commands\Obmenneg\Robots\QiwiBuyNewV2::class,
		Commands\Obmenneg\Robots\QiwiSellNewV2::class,
		Commands\Obmenneg\LocalAds::class,
		Commands\Obmenneg\LocalAdsTwo::class,
		Commands\Obmenneg\ParsersV2\Qiwi::class,
		Commands\Obmenneg\ParsersV2\Yandex::class,
		Commands\Obmenneg\ParsersV2\Wmr::class,
		Commands\Obmenneg\ParsersV2\WmrOld::class,
		Commands\Obmenneg\ParsersV2\Bank::class,
		Commands\Obmenneg\Robots\YandexBuyNewV2::class,
		Commands\Obmenneg\Robots\YandexSellNewV2::class,
		Commands\Obmenneg\BalancingV2::class,
		Commands\Obmenneg\TestCripto::class,
		Commands\Obmenneg\CriptoTransactions::class,
		Commands\Crypto\Centrobank::class,
		Commands\Crypto\Bitfinex::class,
		Commands\Crypto\Birges::class,
		Commands\Report\Course::class,
		Commands\Report\Close::class,
		Commands\ReportRobot::class,
		Commands\Antivirus::class,
		Commands\ParserBitcoins::class,
         		
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
	
	$schedule->command('statistic:video')->withoutOverlapping();  
    #     $schedule->command('videofilter')->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
