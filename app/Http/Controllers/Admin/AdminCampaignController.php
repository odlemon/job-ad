<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin: extend campaign, share (same as employer; no company or coin check).
 */
class AdminCampaignController extends Controller
{
    /**
     * Extend a campaign's end date. Admin: no coin deduction.
     */
    public function extend(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'days' => 'nullable|integer|min:0|max:365',
            'campaign_type_id' => 'nullable|exists:campaign_types,id',
        ]);

        $campaign = JobCampaign::with(['jobAdvertisement', 'campaignType'])->find($id);
        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        $days = (int) ($validated['days'] ?? 0);
        $newTypeId = isset($validated['campaign_type_id']) && $validated['campaign_type_id'] !== ''
            ? (int) $validated['campaign_type_id']
            : null;

        $upgraded = false;
        if ($newTypeId && $newTypeId !== (int) $campaign->campaign_type_id) {
            $campaign->campaign_type_id = $newTypeId;
            $campaign->save();
            $upgraded = true;
        }

        if ($days > 0) {
            $campaign->ends_at = $campaign->ends_at->addDays($days);
            $campaign->duration_days = ($campaign->duration_days ?? 0) + $days;
            $campaign->save();
        }

        $campaign->load('campaignType');
        $message = [];
        if ($upgraded) {
            $message[] = 'Campaign type updated.';
        }
        if ($days > 0) {
            $message[] = 'Listing extended by ' . $days . ' day(s).';
        }
        if (empty(array_filter([$upgraded, $days > 0]))) {
            $message[] = 'No changes made.';
        }

        return response()->json([
            'message' => implode(' ', $message),
            'campaign' => [
                'id' => $campaign->id,
                'ends_at' => $campaign->ends_at?->format('Y-m-d'),
                'duration_days' => (int) $campaign->duration_days,
                'campaign_type' => $campaign->campaignType ? ['id' => $campaign->campaignType->id, 'name' => $campaign->campaignType->name] : null,
            ],
        ]);
    }

    /**
     * Get share URL and optionally increment share count (same as employer share).
     */
    public function share(Request $request, int $id): JsonResponse
    {
        $campaign = JobCampaign::with('jobAdvertisement')->find($id);
        if (!$campaign) {
            return response()->json(['message' => 'Campaign not found'], 404);
        }

        $campaign->increment('shares_count');
        $job = $campaign->jobAdvertisement;
        $shareUrl = $job ? url('/jobs/' . $job->id) : null;

        return response()->json([
            'url' => $shareUrl,
            'message' => 'Share count updated.',
        ]);
    }
}
