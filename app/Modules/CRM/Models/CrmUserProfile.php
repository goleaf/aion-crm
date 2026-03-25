<?php

namespace App\Modules\CRM\Models;

use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Foundation\Enums\RecordVisibilityEnum;
use App\Modules\Shared\Models\User;
use Database\Factories\Modules\CRM\Models\CrmUserProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmUserProfile extends Model
{
    use HasFactory;

    protected $table = 'crm_user_profiles';

    protected static function newFactory(): CrmUserProfileFactory
    {
        return CrmUserProfileFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $profile): void {
            if (! $profile->role instanceof CrmRoleEnum) {
                return;
            }

            if ($profile->record_visibility === null) {
                $profile->record_visibility = $profile->role->defaultRecordVisibility();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'role' => CrmRoleEnum::class,
            'record_visibility' => RecordVisibilityEnum::class,
            'is_active' => 'boolean',
            'deactivated_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primaryTeam(): BelongsTo
    {
        return $this->belongsTo(CrmTeam::class, 'primary_team_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isDeactivated(): bool
    {
        return ! $this->is_active;
    }
}
