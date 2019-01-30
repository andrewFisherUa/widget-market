<?php

namespace App\Widgets;
use Carbon\Carbon;
use Arrilot\Widgets\AbstractWidget;

class UserCompaniesTeaser extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //
		

	    $companies=\App\AdvertiseTeaser::where("user_id", $this->config["user"]->id)->orderBy("created_at","desc")->get();
        return view('widgets.user_companies_teaser', [
            'config' => $this->config,"companies"=>$companies,
        ]);
    }
}
