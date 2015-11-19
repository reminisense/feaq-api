<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/17/2015
 * Time: 10:54 AM
 */

namespace App\Http\Controllers;

use App\Models\Business;

class BusinessController extends Controller
{
    public function search(){
        $arr = Business::searchBusiness($_GET);
        return json_encode($arr);
    }

    public function searchSuggest($keyword){
        $businesses = Business::searchSuggest($keyword);
        return json_encode(array('keywords' => $businesses));
    }
}