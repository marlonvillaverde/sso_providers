<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid as OriginUuid;

trait uuid 
{
	/**
	 * [Genera un codigo uuid4 que reemplaza los carateres "-" por un "0"]
	 * @return [string] [description]
	 */
	 public static function uuid(): string
    {
        //return Str::replace( "-","0",OriginUuid::uuid4());
        return OriginUuid::uuid4();
    }
}