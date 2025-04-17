<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Vessel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $vesselsCount = Vessel::count();
        $pendingVessels = Vessel::where('status', '1')->count();
        $inProgressVessels = Vessel::where('status', '2')->count();
        $completedVessels = Vessel::where('status', '3')->count();

        $invoicesCount = Invoice::count();
        $totalRevenue = Invoice::where('invoice_type', 'final')->sum('grand_total');

        $draftInvoicesCount = Invoice::where('invoice_type', 'draft')->count();
        $proformaInvoicesCount = Invoice::where('invoice_type', 'proforma')->count();
        $preliminaryInvoicesCount = Invoice::where('invoice_type', 'preliminary')->count();
        $finalInvoicesCount = Invoice::where('invoice_type', 'final')->count();

        // آخر 7 أيام - عدد الفواتير
        $recentInvoicesCount = Invoice::whereDate('created_at', '>=', now()->subDays(7))->count();

        // عدد العملاء إن وجد جدول clients
        $clientsCount = class_exists('App\\Models\\Client') ? \App\Models\Client::count() : null;

        // العائدات الشهرية للسنة الحالية
        $monthlyRevenue = DB::table('invoices')
            ->selectRaw('DATE_FORMAT(invoice_date, "%b") as month, SUM(grand_total) as revenue')
            ->whereNotNull('invoice_number')
            ->where('invoice_type', 'final')
            ->whereYear('invoice_date', now()->year)
            ->groupBy(DB::raw('MONTH(invoice_date)'), DB::raw('DATE_FORMAT(invoice_date, "%b")'))
            ->orderBy(DB::raw('MONTH(invoice_date)'))
            ->get();

        $monthlyRevenueLabels = $monthlyRevenue->pluck('month')->toArray();
        $monthlyRevenueData = $monthlyRevenue->pluck('revenue')->toArray();

        return view('dashboard', compact(
            'vesselsCount',
            'pendingVessels',
            'inProgressVessels',
            'completedVessels',
            'invoicesCount',
            'totalRevenue',
            'draftInvoicesCount',
            'proformaInvoicesCount',
            'preliminaryInvoicesCount',
            'finalInvoicesCount',
            'monthlyRevenueLabels',
            'monthlyRevenueData',
            'recentInvoicesCount',
            'clientsCount'
        ));
    }
}
