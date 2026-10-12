<?php

namespace App\Modules\Transaction\Presentation\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ReportController
{
    public function categoryBreakdown(Request $request): JsonResponse
    {
        /** @var int $userId */
        $userId = (int) auth()->id();
        $type = (string) $request->input('type', 'expense');

        $baseQuery = DB::table('transactions')
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.user_id', $userId)
            ->where('transactions.type', $type)
            ->whereNull('transactions.deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = (string) $request->input('start_date');
            $endDate = (string) $request->input('end_date');

            $baseQuery->whereDate('transactions.transaction_date', '>=', $startDate)
                ->whereDate('transactions.transaction_date', '<=', $endDate);

            $period = [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
            if ($request->filled('month')) {
                $period['month'] = (int) $request->input('month');
            }
            if ($request->filled('year')) {
                $period['year'] = (int) $request->input('year');
            }
        } else {
            $month = (int) $request->input('month', now()->month);
            $year = (int) $request->input('year', now()->year);

            $baseQuery->whereYear('transactions.transaction_date', $year)
                ->whereMonth('transactions.transaction_date', $month);

            $period = [
                'month' => $month,
                'year' => $year,
            ];
        }

        $totalOverall = round((float) (clone $baseQuery)->sum('transactions.amount'), 2);

        $rows = (clone $baseQuery)
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_amount')
            ->get();

        $items = [];
        foreach ($rows as $row) {
            $totalAmount = round((float) $row->total_amount, 2);
            $percentage = $totalOverall > 0 ? round(($totalAmount / $totalOverall) * 100, 1) : 0.0;

            $items[] = [
                'category_id' => (int) $row->category_id,
                'category_name' => (string) $row->category_name,
                'total_amount' => $totalAmount,
                'percentage' => $percentage,
                'transaction_count' => (int) $row->transaction_count,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => $period,
                'total_overall' => $totalOverall,
                'items' => $items,
            ],
        ]);
    }

    public function cashflowTrend(Request $request): JsonResponse
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        $months = (int) $request->input('months', 6);
        if ($months < 1) {
            $months = 6;
        } elseif ($months > 12) {
            $months = 12;
        }

        $startDate = now()->startOfMonth()->subMonths($months - 1)->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        $transactions = DB::table('transactions')
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.user_id', $userId)
            ->whereNull('transactions.deleted_at')
            ->whereDate('transactions.transaction_date', '>=', $startDate)
            ->whereDate('transactions.transaction_date', '<=', $endDate)
            ->whereIn('transactions.type', ['income', 'expense'])
            ->select('transactions.type', 'transactions.amount', 'transactions.transaction_date')
            ->get();

        $monthlyData = [];
        for ($i = 0; $i < $months; $i++) {
            $dt = now()->startOfMonth()->subMonths($months - 1 - $i);
            $key = $dt->format('Y-m');
            $monthlyData[$key] = [
                'month' => (int) $dt->month,
                'year' => (int) $dt->year,
                'month_label' => $dt->format('M Y'),
                'income' => 0.0,
                'expense' => 0.0,
                'net' => 0.0,
            ];
        }

        foreach ($transactions as $tx) {
            $txDate = CarbonImmutable::parse($tx->transaction_date)->format('Y-m');
            if (isset($monthlyData[$txDate])) {
                $amount = (float) $tx->amount;
                if ($tx->type === 'income') {
                    $monthlyData[$txDate]['income'] += $amount;
                } elseif ($tx->type === 'expense') {
                    $monthlyData[$txDate]['expense'] += $amount;
                }
            }
        }

        foreach ($monthlyData as $key => $data) {
            $income = round($data['income'], 2);
            $expense = round($data['expense'], 2);
            $monthlyData[$key]['income'] = $income;
            $monthlyData[$key]['expense'] = $expense;
            $monthlyData[$key]['net'] = round($income - $expense, 2);
        }

        return response()->json([
            'status' => 'success',
            'data' => array_values($monthlyData),
        ]);
    }
}
