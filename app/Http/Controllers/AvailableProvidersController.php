<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AvailableProvider;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AvailableProvidersController extends Controller
{

	public function index(Request $request)
	{	
		if (is_null($request->type)) {
			$providers = AvailableProvider::all();
		}else{
			$providers = AvailableProvider::where('sso_type','=', $request->type)->get();	
		}
		
		return response( $providers, 200);
	}

}