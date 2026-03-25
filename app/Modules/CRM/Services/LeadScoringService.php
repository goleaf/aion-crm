<?php

namespace App\Modules\CRM\Services;

use App\Modules\CRM\DataTransferObjects\LeadScoreData;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;

class LeadScoringService
{
    public function score(
        ?string $email,
        ?string $phone,
        LeadSourceEnum $leadSource,
        LeadStatusEnum $status,
    ): LeadScoreData {
        $score = $this->leadSourceScore($leadSource)
            + $this->contactabilityScore($email, $phone)
            + $this->statusScore($status);

        $normalizedScore = min(100, max(0, $score));

        return new LeadScoreData(
            score: $normalizedScore,
            rating: $this->ratingForScore($normalizedScore),
        );
    }

    private function leadSourceScore(LeadSourceEnum $leadSource): int
    {
        return match ($leadSource) {
            LeadSourceEnum::WalkIn => 20,
            LeadSourceEnum::ColdCall => 5,
            LeadSourceEnum::Referral => 30,
            LeadSourceEnum::InternalForm => 25,
            LeadSourceEnum::Event => 22,
        };
    }

    private function contactabilityScore(?string $email, ?string $phone): int
    {
        $hasEmail = filled($email);
        $hasPhone = filled($phone);

        return match (true) {
            $hasEmail && $hasPhone => 30,
            $hasEmail => 18,
            $hasPhone => 16,
            default => 0,
        };
    }

    private function statusScore(LeadStatusEnum $status): int
    {
        return match ($status) {
            LeadStatusEnum::New => 5,
            LeadStatusEnum::Contacted => 15,
            LeadStatusEnum::Qualified => 25,
            LeadStatusEnum::Unqualified => -20,
            LeadStatusEnum::Converted => 20,
        };
    }

    private function ratingForScore(int $score): LeadRatingEnum
    {
        return match (true) {
            $score >= 80 => LeadRatingEnum::Hot,
            $score >= 50 => LeadRatingEnum::Warm,
            default => LeadRatingEnum::Cold,
        };
    }
}
