<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CampaignType;
use Illuminate\View\View;

class PricingPageController extends Controller
{
    public function index(): View
    {
        $types = CampaignType::query()
            ->orderBy('sort_order')
            ->get();

        $colorBySlug = [
            'growthhire' => 'blue',
            'smarthire' => 'amber',
            'powerhire' => 'rose',
        ];

        $descriptions = [
            'growthhire' => 'For growing businesses that need cost-effective hiring with solid reach and brand presence.',
            'smarthire' => 'Standard visibility with smart scheduling and front-page promotion for general hiring needs.',
            'powerhire' => 'Maximum performance and premium features for urgent, critical and high-impact hiring.',
        ];

        $plans = $types->values()->map(function (CampaignType $type, int $index) use ($colorBySlug, $descriptions) {
            $slug = strtolower((string) $type->slug);
            $color = $colorBySlug[$slug] ?? ['blue', 'amber', 'rose'][$index % 3];

            return [
                'id' => $type->id,
                'slug' => $type->slug,
                'name' => $type->name,
                'scr_price' => (int) $type->scr_price,
                'coins_price' => (int) $type->coins_price,
                'duration_days' => (int) $type->duration_days,
                'features' => $type->features ?? [],
                'popular' => (bool) $type->is_popular,
                'color' => $color,
                'description' => $descriptions[$slug] ?? ('Promote your jobs with '.$type->name.'.'),
            ];
        });

        $featureMatrix = [
            [
                'label' => 'Job Advert',
                'tooltip' => 'The number of individual job listings included in this plan.',
                'values' => ['1', '1', '1'],
            ],
            [
                'label' => 'Duration',
                'tooltip' => 'How long your job ad stays live and visible to job seekers on the platform.',
                'values' => $plans->take(3)->map(function ($p) {
                    $days = (int) ($p['duration_days'] ?? 0);
                    if ($days <= 15) {
                        return '7 / 15 days';
                    }
                    if ($days <= 31) {
                        return '1 month';
                    }

                    return $days.' days';
                })->values()->all(),
            ],
            [
                'label' => 'Branded Ad',
                'tooltip' => 'Your company logo and branding are displayed prominently on the job listing to increase recognition and trust.',
                'values' => [true, true, true],
            ],
            [
                'label' => 'Applicant Applications',
                'tooltip' => 'The total number of applications you can receive from candidates for this ad. Unlimited means no cap.',
                'values' => ['Unlimited', 'Unlimited', 'Unlimited'],
            ],
            [
                'label' => 'Standard Listing',
                'tooltip' => 'Your ad appears in the regular job search results, visible to all active job seekers browsing the platform.',
                'values' => [true, true, true],
            ],
            [
                'label' => 'Social Media Posting',
                'tooltip' => 'Your job ad is automatically shared to Facebook, Instagram and WhatsApp at no extra cost, extending your reach beyond the platform.',
                'values' => [true, true, true],
            ],
            [
                'label' => 'Schedule Ad Posting',
                'tooltip' => 'Choose a specific future date and time for your ad to go live, so you can plan your hiring campaign in advance.',
                'values' => [null, true, true],
            ],
            [
                'label' => 'Front Page Promotion',
                'tooltip' => 'Your ad is pinned or featured on the homepage, giving it prime visibility to every visitor who lands on the site.',
                'values' => [null, true, true],
            ],
            [
                'label' => 'Promoted on Competitor Ad',
                'tooltip' => 'Your ad appears as a suggested alternative on similar job listings from other employers, capturing candidates already browsing related roles.',
                'values' => [null, null, true],
            ],
            [
                'label' => 'Exclusive Ad',
                'tooltip' => 'No competitor ads are shown alongside your job posting — making it exclusive to just you.',
                'values' => [null, null, true],
            ],
            [
                'label' => 'Featured Listing',
                'tooltip' => 'Your ad is highlighted with a special badge and ranked at the top of search results to stand out from standard listings.',
                'values' => [null, null, true],
            ],
            [
                'label' => 'Ad Sent to Fitting Candidates',
                'tooltip' => 'The platform proactively notifies job seekers whose profile and skills closely match your role requirements, bringing qualified applicants directly to you.',
                'values' => [null, null, true],
            ],
        ];

        $faqs = [
            [
                'q' => 'What is the difference between GrowthHire, SmartHire and PowerHire?',
                'a' => 'GrowthHire is our entry-level plan offering a 7 or 15-day branded listing with social media posting — ideal for straightforward hires on a budget. SmartHire adds scheduled posting and front-page promotion for a full month, giving your ad more visibility. PowerHire is our premium tier with exclusive listings, featured placement, competitor ad promotion, and direct matching to fitting candidates.',
            ],
            [
                'q' => 'How long does my job advert stay live?',
                'a' => 'GrowthHire ads run for 7 or 15 days depending on your selection. SmartHire and PowerHire ads are live for a full calendar month from the date of posting.',
            ],
            [
                'q' => 'What does "Ad Sent to Fitting Candidates" mean?',
                'a' => 'On the PowerHire plan, your job advert is actively pushed to job seekers whose profiles match your role requirements. This means qualified candidates are notified directly — you don\'t have to wait for them to find your listing.',
            ],
            [
                'q' => 'What are AdCredits (AC) and how do they work?',
                'a' => 'AdCredits are our in-platform currency that can be used to purchase job ad plans. They offer a convenient way to pre-load credit and post ads without going through a payment each time. 1 AC is equivalent to a fixed SCR value set at time of purchase.',
            ],
            [
                'q' => 'Can I post to multiple social media channels?',
                'a' => 'Yes — all three plans include automatic posting to Facebook, Instagram and WhatsApp at no extra cost, ensuring your advert reaches a wide audience across the most-used social platforms in the Seychelles.',
            ],
            [
                'q' => 'Can I upgrade my plan after posting an ad?',
                'a' => 'Upgrades are not available mid-run. If you need more visibility, you can post a new advert on a higher-tier plan once your current one expires. Contact our support team if you have urgent requirements and we will find the best solution.',
            ],
            [
                'q' => 'Is there a bulk discount for multiple job adverts?',
                'a' => 'Currently each advert is priced individually per plan. For organisations with ongoing or high-volume hiring needs, please reach out to our sales team to discuss a custom arrangement.',
            ],
            [
                'q' => 'What payment methods do you accept?',
                'a' => 'We accept major credit and debit cards, as well as AdCredit top-ups purchased through the platform. For enterprise billing or invoice-based payment, contact our sales team.',
            ],
        ];

        $ctaUrl = auth()->check() && auth()->user()->user_type === 'employer'
            ? route('employer.campaigns.create')
            : route('login');

        return view('pricing.index', [
            'plans' => $plans,
            'plansJson' => $plans->map(fn ($p) => [
                'name' => $p['name'],
                'scr_price' => $p['scr_price'],
                'coins_price' => $p['coins_price'],
                'popular' => $p['popular'],
                'color' => $p['color'],
            ])->values()->all(),
            'featureMatrix' => $featureMatrix,
            'faqs' => $faqs,
            'ctaUrl' => $ctaUrl,
        ]);
    }
}
