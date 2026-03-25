<?php

namespace App\Modules\Auth\DataTransferObjects;

use App\Modules\Auth\Models\UserTwoFactorAuth;
use App\Modules\Shared\Models\User;
use InvalidArgumentException;

final readonly class TwoFactorChallengeData
{
    public function __construct(
        public ?UserTwoFactorAuth $twoFactorAuth,
        public ?string $code,
        public ?string $recoveryCode = null,
    ) {
        throw_if($code === null && $this->recoveryCode === null, InvalidArgumentException::class, 'At least a 2fa code or a recovery code must be provided');

        throw_if($code !== null && $this->recoveryCode !== null, InvalidArgumentException::class, 'A 2fa code and a recovery code cannot be provided at the same time.');
    }

    public static function fromUser(User $user, ?string $code = null, ?string $recoveryCode = null): self
    {
        return new self($user->twoFactorAuth, $code, $recoveryCode);
    }
}
