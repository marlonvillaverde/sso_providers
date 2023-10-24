<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\EnabledProviderConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Validator;

class EnabledProvidersController extends Controller
{

	public function index(): Response
	{		
		$providers = EnabledProviderConfig::all();
		
		return response($providers, 200);
	}


	/**
	 * Get the enabled providers for company uuid
	 * @param  string $uuid [description]
	 * @return [type]       [description]
	 */
	public function byCompany(string $uuid)
	{

		$configProviders = EnabledProviderConfig::FindByCompany($uuid);

		$urls = collect([]);

		foreach($configProviders as $provider){

			$urls->push([
				"button_info" => $provider->button_info,
				"uuid" => $provider->uuid,				
			]);

		}

		return response()->json([
						"base_url" => env('LOGIN_PROVIDER_URL'),
						"urls" => $urls,
					],200);

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


	public function store (Request $request)
	{
		
		$record = EnabledProviderConfig::create([
			'company_id' => $request->company_id,
			'provider_id' => $request->provider->id,
			'uuid' => Uuid::uuid4(),
			'sso_type' => $request->provider->sso_type,
			'cfg_template' => $request->template,
			'cfg_user' => $request->cfg_user_template,
			'describe' => $request->describe,
			'button_info' => $request->button_info,
		]);


		return response($record, 200);
	}





}