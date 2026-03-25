<?php

namespace App\Modules\CRM\Models;

use Database\Factories\Modules\CRM\Models\CrmTeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmTeam extends Model
{
    use HasFactory;

    protected $table = 'crm_teams';

    protected static function newFactory(): CrmTeamFactory
    {
        return CrmTeamFactory::new();
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(CrmUserProfile::class, 'primary_team_id');
    }
}
