<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\EnabledProviderConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class EnabledProvidersController extends Controller
{

	public function index(): Response
	{		
		$providers = EnabledProviderConfig::all();
		
		return response($providers, 200);
	}


	/**
	 * [Datos del provider seleccionado segun su uuid]
	 * @param  [string] $uuid 			[codigo uuid del provider que se selecciono]
	 * @return [EnabledProviderConfig]  [Datos del provider seleccionado]
	 */
	public function provider(Request $request, $uuid): Response
	{
		$EnabledProvider = EnabledProviderConfig::FindByUuid($uuid);

		return $EnabledProvider = EnabledProviderConfig::FindByUuid($uuid) ? 
					response($EnabledProvider, 200) :
					response(json_encode(['message' => 'Not Found!']), 404);
	}

}