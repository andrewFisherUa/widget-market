<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleXMLElement;
use DomDocument;

class TeaserOffersController extends Controller
{
    public function index(){
		$offers=\DB::connection('advertise')->table('teaser_offers')->inRandomOrder()->get();
		return $offers;
	}
}
