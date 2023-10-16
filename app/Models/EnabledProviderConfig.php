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


    public static function FindByUuid($uuid)
    {
        return Self::where('uuid', $uuid)->get()->first();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AvailableProvider::class );
    }
}