<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\CampaignType;
use App\Models\CoinTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployerCoinsController extends Controller
{
    public const SCR_PER_COIN = 20;

    /** Bolt amount tiles (not admin named packages). */
    public const AMOUNTS = [50, 100, 250, 500, 1000];

    public static function bulkDiscountPercent(int $coins): int
    {
        if ($coins >= 1000) {
            return 20;
        }
        if ($coins >= 500) {
            return 15;
        }
        if ($coins >= 200) {
            return 10;
        }
        if ($coins >= 100) {
            return 5;
        }

        return 0;
    }

    public static function priceBreakdown(int $coins): array
    {
        $base = $coins * self::SCR_PER_COIN;
        $discount = self::bulkDiscountPercent($coins);
        $discountAmount = (int) round($base * $discount / 100);
        $final = $base - $discountAmount;

        return [
            'coins' => $coins,
            'rate' => self::SCR_PER_COIN,
            'base_scr' => $base,
            'discount_percent' => $discount,
            'discount_amount' => $discountAmount,
            'final_scr' => $final,
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $coinBalance = (int) ($employer->coin_balance ?? 0);

        $thisMonthSpent = (int) CoinTransaction::where('employer_id', $employer->employer_id)
            ->where('type', CoinTransaction::TYPE_SPEND)
            ->where('created_at', '>=', now()->copy()->startOfMonth())
            ->sum('amount');

        $rewardsEarned = (int) CoinTransaction::where('employer_id', $employer->employer_id)
            ->where('type', CoinTransaction::TYPE_REWARD)
            ->sum('amount');

        $packages = collect(self::AMOUNTS)->map(function (int $coins) {
            $label = number_format($coins);
            $breakdown = self::priceBreakdown($coins);

            return array_merge($breakdown, [
                'label' => $label,
            ]);
        });

        $campaignTypes = CampaignType::orderBy('sort_order')->get();
        $referralUrl = url('/register?ref=' . $employer->employer_id);

        return view('employer.coins.index', [
            'coinBalance' => $coinBalance,
            'thisMonthSpent' => $thisMonthSpent,
            'rewardsEarned' => $rewardsEarned,
            'packages' => $packages,
            'scrPerCoin' => self::SCR_PER_COIN,
            'defaultAmount' => 100,
            'campaignTypes' => $campaignTypes,
            'referralUrl' => $referralUrl,
        ]);
    }

    public function purchase(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;

        if (!$employer) {
            return response()->json(['message' => 'Employer profile required.'], 403);
        }

        $validated = $request->validate([
            'coins' => ['required', 'integer', Rule::in(self::AMOUNTS)],
        ]);

        $coins = (int) $validated['coins'];
        $breakdown = self::priceBreakdown($coins);

        $payment = null;
        $newBalance = 0;

        DB::transaction(function () use ($employer, $coins, $breakdown, &$payment, &$newBalance) {
            $employer->refresh();
            $employer->coin_balance = (int) ($employer->coin_balance ?? 0) + $coins;
            $employer->save();
            $newBalance = (int) $employer->coin_balance;

            $txnId = 'TXN-COIN-' . strtoupper(substr(uniqid(), -8));
            $total = (float) $breakdown['final_scr'];
            $tax = round($total - ($total / 1.09), 2);

            $payment = Payment::create([
                'transaction_id' => $txnId,
                'category' => 'coins',
                'payer_name' => $employer->company_name ?? ($employer->company->name ?? 'Employer'),
                'description' => number_format($coins) . ' coins purchase',
                'payment_method' => 'credit_card',
                'card_brand' => $employer->billing_card_brand,
                'card_last4' => $employer->billing_card_last4,
                'amount' => $total,
                'tax_amount' => $tax,
                'coins_amount' => $coins,
                'currency' => 'SCR',
                'status' => 'completed',
                'paid_at' => now(),
                'company_id' => $employer->company_id,
            ]);

            CoinTransaction::create([
                'employer_id' => $employer->employer_id,
                'type' => CoinTransaction::TYPE_PURCHASE,
                'amount' => $coins,
                'description' => 'Purchased ' . number_format($coins) . ' coins',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
            ]);
        });

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => number_format($coins) . ' coins added to your balance.',
                'coins' => $coins,
                'new_balance' => $newBalance,
                'amount_scr' => $breakdown['final_scr'],
                'transaction_id' => $payment->transaction_id ?? null,
            ]);
        }

        return redirect()->route('employer.coins.index')
            ->with('success', number_format($coins) . ' coins added to your balance.');
    }
}
