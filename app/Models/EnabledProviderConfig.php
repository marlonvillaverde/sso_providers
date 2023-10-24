<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AvailableProvider;

class EnabledProviderConfig extends Model
{

    protected $table = 'enabled_providers_config';

    protected $fillable = [
        'provider_id',
        'uuid',
        'cfg_template',
        'cfg_user',
        'describe',
    ];


    /**
     * Todos los providers disponibles para una compania
     * @param string $uuid [description]
     */
    public static function FindByCompany(string $uuid)
    {
        return Self::where('company_uuid', '=',  $uuid)->where('status','=','1')->get();
    }


    public static function FindByUuid($uuid)
    {
        return Self::where('uuid', $uuid)->first();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AvailableProvider::class );
    }
}