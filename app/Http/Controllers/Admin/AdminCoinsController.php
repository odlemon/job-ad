<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinPackage;
use App\Models\Employer;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Admin: Coin Management – dashboard stats, packages list, create, update, delete.
 */
class AdminCoinsController extends Controller
{
    /**
     * Dashboard: Total Coins Sold, Coins in Circulation, Active Packages, Revenue (This Month).
     */
    public function dashboard(): JsonResponse
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalCoinsSold = 0;
        if (Schema::hasColumn((new Payment)->getTable(), 'coins_amount')) {
            $totalCoinsSold = (int) Payment::where('category', 'coins')
                ->where('status', 'completed')
                ->sum('coins_amount');
        }

        $coinsInCirculation = (int) Employer::sum('coin_balance');
        $employersWithCoins = (int) Employer::where('coin_balance', '>', 0)->count();

        $activePackagesCount = CoinPackage::where('status', 'active')->count();
        $totalPackagesCount = CoinPackage::count();

        $revenueThisMonth = (float) Payment::where('category', 'coins')
            ->where('status', 'completed')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                        $q2->whereNull('paid_at')->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                    });
            })
            ->sum('amount');

        return response()->json([
            'stats' => [
                'total_coins_sold' => $totalCoinsSold,
                'coins_in_circulation' => $coinsInCirculation,
                'employers_with_coins' => $employersWithCoins,
                'active_packages' => $activePackagesCount,
                'total_packages' => $totalPackagesCount,
                'revenue_this_month' => $revenueThisMonth,
                'currency' => 'SCR',
            ],
        ]);
    }

    /**
     * List coin packages (for Packages tab). Optionally filter by status.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CoinPackage::orderBy('sort_order')->orderBy('name');
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $packages = $query->get()->map(fn ($p) => $this->packageToItem($p));
        return response()->json(['packages' => $packages]);
    }

    /**
     * Get one package (for edit form or detail).
     */
    public function show(int $id): JsonResponse
    {
        $package = CoinPackage::find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }
        return response()->json(['package' => $this->packageToItem($package)]);
    }

    /**
     * Create a new coin package (Add Package).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coins_amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string|max:2000',
            'status' => 'nullable|string|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:64',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'SCR';
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $package = CoinPackage::create($validated);
        return response()->json([
            'message' => 'Package created',
            'package' => $this->packageToItem($package),
        ], 201);
    }

    /**
     * Update a coin package (Edit).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $package = CoinPackage::find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'coins_amount' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'description' => 'nullable|string|max:2000',
            'status' => 'nullable|string|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:64',
        ]);

        $package->update($validated);
        return response()->json([
            'message' => 'Package updated',
            'package' => $this->packageToItem($package->fresh()),
        ]);
    }

    /**
     * Delete a coin package (soft: only if no purchases reference it, or hard delete).
     */
    public function destroy(int $id): JsonResponse
    {
        $package = CoinPackage::find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }
        $package->delete();
        return response()->json(['message' => 'Package deleted']);
    }

    /**
     * Toggle package status (active/inactive).
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $package = CoinPackage::find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }
        $package->status = $package->status === 'active' ? 'inactive' : 'active';
        $package->save();
        return response()->json([
            'message' => 'Package status updated',
            'package' => $this->packageToItem($package->fresh()),
        ]);
    }

    private function packageToItem(CoinPackage $p): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'coins_amount' => (int) $p->coins_amount,
            'price' => (float) $p->price,
            'currency' => $p->currency ?? 'SCR',
            'description' => $p->description,
            'status' => $p->status,
            'sort_order' => (int) $p->sort_order,
            'icon' => $p->icon,
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];
    }
}
