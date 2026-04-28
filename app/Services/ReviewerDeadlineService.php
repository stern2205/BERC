<?php

namespace App\Services;

use App\Models\ResearchApplications;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewerDeadlineService
{
    public function syncExpiredReviewers(): void
    {
        $rows = DB::table('application_reviewer')
            ->join('research_applications', 'application_reviewer.protocol_code', '=', 'research_applications.protocol_code')
            ->where('research_applications.status', 'awaiting_reviewer_approval')
            ->whereIn('application_reviewer.status', ['Pending', 'Accepted'])
            ->select(
                'application_reviewer.*',
                'research_applications.review_classification'
            )
            ->get();

        foreach ($rows as $row) {
            $shouldExpire = false;
            $expiredAt = null;

            // 1. Invitation expiry: assigned but not accepted after 24 hours
            if ($row->status === 'Pending') {
                $deadline = Carbon::parse($row->date_assigned)->addHours(24);

                if (now()->greaterThanOrEqualTo($deadline)) {
                    $shouldExpire = true;
                    $expiredAt = $deadline;
                }
            }

            // 2. Assessment expiry: accepted but review period passed
            if ($row->status === 'Accepted' && $row->date_accepted) {
                $daysAllowed = match ($row->review_classification) {
                    'Expedited' => 10,
                    'Full Board' => 20,
                    default => null,
                };

                if ($daysAllowed) {
                    $deadline = Carbon::parse($row->date_accepted)->addDays($daysAllowed);

                    if (now()->greaterThanOrEqualTo($deadline)) {
                        $shouldExpire = true;
                        $expiredAt = $deadline;
                    }
                }
            }

            if (!$shouldExpire) {
                continue;
            }

            DB::transaction(function () use ($row, $expiredAt) {
                DB::table('application_reviewer')
                    ->where('id', $row->id)
                    ->update([
                        'status' => 'Expired',
                        'date_expired' => $expiredAt,
                        'updated_at' => now(),
                    ]);

                $this->removeReviewerFromAssessmentSlots(
                    $row->protocol_code,
                    $row->reviewer_id
                );

                ResearchApplications::where('protocol_code', $row->protocol_code)
                    ->update([
                        'status' => 'awaiting_reviewer_approval',
                        'updated_at' => now(),
                    ]);
            });
        }
    }

    private function removeReviewerFromAssessmentSlots(string $protocolCode, int $reviewerId): void
    {
        $assessment = DB::table('assessment_forms')
            ->where('protocol_code', $protocolCode)
            ->first();

        if ($assessment) {
            $updates = ['updated_at' => now()];

            foreach ([1, 2, 3] as $slot) {
                $reviewerCol = "reviewer_{$slot}_id";
                $doneCol = "reviewer_{$slot}_done";

                if ((int) $assessment->$reviewerCol === (int) $reviewerId) {
                    $updates[$reviewerCol] = null;
                    $updates[$doneCol] = null;
                }
            }

            DB::table('assessment_forms')
                ->where('protocol_code', $protocolCode)
                ->update($updates);
        }

        $icf = DB::table('icf_assessments')
            ->where('protocol_code', $protocolCode)
            ->first();

        if ($icf) {
            $updates = ['updated_at' => now()];

            foreach ([1, 2, 3] as $slot) {
                $reviewerCol = "reviewer_{$slot}_id";

                if ((int) $icf->$reviewerCol === (int) $reviewerId) {
                    $updates[$reviewerCol] = null;
                }
            }

            DB::table('icf_assessments')
                ->where('protocol_code', $protocolCode)
                ->update($updates);
        }
    }
}
