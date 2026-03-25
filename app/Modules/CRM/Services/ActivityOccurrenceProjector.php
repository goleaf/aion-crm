<?php

namespace App\Modules\CRM\Services;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Models\Activity;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ActivityOccurrenceProjector
{
    /**
     * @param  Collection<int, Activity>  $activities
     * @return list<array{activity: Activity, occurs_at: CarbonImmutable, ends_at: CarbonImmutable}>
     */
    public function projectForRange(Collection $activities, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $occurrences = [];

        foreach ($activities as $activity) {
            $currentStart = CarbonImmutable::instance($activity->start_at);
            $currentEnd = CarbonImmutable::instance($activity->end_at);

            while ($currentStart->lte($to)) {
                if ($currentEnd->gte($from)) {
                    $occurrences[] = [
                        'activity' => $activity,
                        'occurs_at' => $currentStart,
                        'ends_at' => $currentEnd,
                    ];
                }

                if ($activity->recurrence === ActivityRecurrenceEnum::None) {
                    break;
                }

                [$currentStart, $currentEnd] = $this->advanceOccurrence(
                    $activity->recurrence,
                    $currentStart,
                    $currentEnd,
                );
            }
        }

        usort(
            $occurrences,
            fn (array $leftOccurrence, array $rightOccurrence): int => $leftOccurrence['occurs_at']->getTimestamp() <=> $rightOccurrence['occurs_at']->getTimestamp(),
        );

        return $occurrences;
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function advanceOccurrence(
        ActivityRecurrenceEnum $recurrence,
        CarbonImmutable $startAt,
        CarbonImmutable $endAt,
    ): array {
        return match ($recurrence) {
            ActivityRecurrenceEnum::Daily => [$startAt->addDay(), $endAt->addDay()],
            ActivityRecurrenceEnum::Weekly => [$startAt->addWeek(), $endAt->addWeek()],
            ActivityRecurrenceEnum::Monthly => [$startAt->addMonthNoOverflow(), $endAt->addMonthNoOverflow()],
            ActivityRecurrenceEnum::None => [$startAt, $endAt],
        };
    }
}
