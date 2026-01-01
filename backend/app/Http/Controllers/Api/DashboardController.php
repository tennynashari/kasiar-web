<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $outletId = $request->outlet_id ?? auth()->user()->outlet_id;
        $dateFrom = $request->date_from ?? now()->startOfDay();
        $dateTo = $request->date_to ?? now()->endOfDay();

        // Today's stats
        $todayStats = $this->getTodayStats($outletId);

        // Sales chart (last 7 days)
        $salesChart = $this->getSalesChart($outletId, 7);

        // Top products
        $topProducts = $this->getTopProducts($outletId, $dateFrom, $dateTo);

        // Payment method breakdown
        $paymentBreakdown = $this->getPaymentBreakdown($outletId, $dateFrom, $dateTo);

        // Low stock products
        $lowStockProducts = $this->getLowStockProducts();

        return response()->json([
            'today' => $todayStats,
            'sales_chart' => $salesChart,
            'top_products' => $topProducts,
            'payment_breakdown' => $paymentBreakdown,
            'low_stock_products' => $lowStockProducts,
        ]);
    }

    private function getTodayStats($outletId)
    {
        $today = now()->startOfDay();
        
        $query = Transaction::where('status', 'completed')
            ->whereDate('created_at', $today);
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        
        $transactions = $query->get();

        $totalRevenue = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Cash in hand (today's cash only)
        $cashFlowQuery = CashFlow::whereDate('created_at', $today);
        
        if ($outletId) {
            $cashFlowQuery->where('outlet_id', $outletId);
        }
        
        $cashInHand = $cashFlowQuery->sum(DB::raw("CASE WHEN type = 'in' THEN amount ELSE -amount END"));

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'cash_in_hand' => $cashInHand,
        ];
    }

    private function getSalesChart($outletId, $days)
    {
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            
            $query = Transaction::where('status', 'completed')
                ->whereDate('created_at', $date);
            
            if ($outletId) {
                $query->where('outlet_id', $outletId);
            }
            
            $total = $query->sum('total');

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'total' => $total,
            ];
        }

        return $data;
    }

    private function getTopProducts($outletId, $dateFrom, $dateTo, $limit = 10)
    {
        $query = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.status', 'completed')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
        
        if ($outletId) {
            $query->where('transactions.outlet_id', $outletId);
        }
        
        return $query->select(
                'products.id',
                'products.name',
                DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getPaymentBreakdown($outletId, $dateFrom, $dateTo)
    {
        $query = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        
        return $query->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();
    }

    private function getLowStockProducts($limit = 10)
    {
        return Product::with('category')
            ->where('track_stock', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock', 'asc')
            ->limit($limit)
            ->get();
    }

    public function salesReport(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'nullable|exists:outlets,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'nullable|in:day,week,month',
        ]);

        $user = auth()->user();
        $outletId = $validated['outlet_id'] ?? $user->outlet_id;
        $groupBy = $validated['group_by'] ?? 'day';

        $query = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$validated['date_from'], $validated['date_to']]);
        
        // Only filter by outlet if user has specific outlet or outlet_id provided
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        $dateFormat = match($groupBy) {
            'day' => 'YYYY-MM-DD',
            'week' => 'YYYY-WW',
            'month' => 'YYYY-MM',
        };

        $report = $query->select(
            DB::raw("TO_CHAR(created_at, '{$dateFormat}') as period"),
            DB::raw('COUNT(*) as transaction_count'),
            DB::raw('SUM(total) as total_revenue'),
            DB::raw('SUM(discount) as total_discount'),
            DB::raw('AVG(total) as average_transaction')
        )
        ->groupBy('period')
        ->orderBy('period')
        ->get();

        return response()->json($report);
    }
}
