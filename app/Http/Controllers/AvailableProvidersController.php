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
		if (is_null($request->type)){
			return response(AvailableProvider::all(), 200);
		}
		
		return response(AvailableProvider::where('sso_type','=', $request->type)->get(), 200);
	
	}
}