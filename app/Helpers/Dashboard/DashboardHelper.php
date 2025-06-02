<?php

namespace App\Helpers\Dashboard;

use App\Models\Sonod;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardHelper
{
    public static function getReportsDetails($keyname, $value, $sonodName = null, $detials = null, $fromDate = null, $toDate = null)
    {
        $paymentKeyName = $keyname === 'unioun_name' ? 'union' : $keyname;

        // Format dates
        if ($fromDate) {
            $fromDate = date('Y-m-d', strtotime($fromDate));
        }
        if ($toDate) {
            $toDate = date('Y-m-d', strtotime($toDate));
        }

        // Common filters for Sonod queries
        $applySonodFilters = function ($query) use ($keyname, $value, $sonodName, $fromDate, $toDate) {
            $query->where($keyname, $value)
                ->when($sonodName, fn($q) => $q->where('sonod_name', $sonodName))
                ->when($fromDate && $toDate, fn($q) =>
                    $q->whereBetween('created_at', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
                )
                ->when($fromDate && !$toDate, fn($q) =>
                    $q->where('created_at', '>=', "{$fromDate} 00:00:00")
                )
                ->when(!$fromDate && $toDate, fn($q) =>
                    $q->where('created_at', '<=', "{$toDate} 23:59:59")
                );
        };

        if ($detials) {
            // Detailed sonod report grouped by unioun_name and sonod_name
            $detailedSonodReports = Sonod::query()
                ->tap($applySonodFilters)
                ->selectRaw("
                    unioun_name,
                    sonod_name,
                    MAX(division_name) as division_name,
                    MAX(district_name) as district_name,
                    MAX(upazila_name) as upazila_name,
                    COUNT(CASE WHEN stutus = 'Pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN stutus = 'approved' THEN 1 END) as approved_count,
                    COUNT(CASE WHEN stutus = 'cancel' THEN 1 END) as cancel_count
                ")
                ->groupBy('unioun_name', 'sonod_name')
                ->get();

            return [
                'detailed_sonod_reports' => $detailedSonodReports,
                'sonodName' => $sonodName,
                'totals' => self::calculateSonodTotals($detailedSonodReports),
            ];
        }

        // Summary: One row per sonod_name, grouped by sonod_name
        $sonodReports = Sonod::query()
            ->tap($applySonodFilters)
            ->selectRaw("
                sonod_name,
                MAX(division_name) as division_name,
                MAX(district_name) as district_name,
                MAX(upazila_name) as upazila_name,
                COUNT(CASE WHEN stutus = 'Pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN stutus = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN stutus = 'cancel' THEN 1 END) as cancel_count
            ")
            ->groupBy('sonod_name')
            ->get()
            ->map(function ($report) {
                $report->sonod_name = translateToBangla($report->sonod_name);
                return $report;
            });

        // Payment summary: One row per sonod_type
        $paymentReports = Payment::query()
            ->where($paymentKeyName, $value)
            ->where('status', 'Paid')
            ->when($sonodName, fn($q) => $q->where('sonod_type', $sonodName))
            ->when($fromDate && $toDate, fn($q) =>
                $q->whereBetween('created_at', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
            )
            ->when($fromDate && !$toDate, fn($q) =>
                $q->where('created_at', '>=', "{$fromDate} 00:00:00")
            )
            ->when(!$fromDate && $toDate, fn($q) =>
                $q->where('created_at', '<=', "{$toDate} 23:59:59")
            )
            ->selectRaw("
                sonod_type,
                MAX(division_name) as division_name,
                MAX(district_name) as district_name,
                MAX(upazila_name) as upazila_name,
                COUNT(*) as total_payments,
                SUM(amount) as total_amount
            ")
            ->groupBy('sonod_type')
            ->get()
            ->map(function ($report) {
                $report->sonod_type = translateToBangla($report->sonod_type);
                $report->total_amount = number_format((float) $report->total_amount, 2, '.', '');
                return $report;
            });

        return [
            'sonod_reports' => $sonodReports,
            'payment_reports' => $paymentReports,
            'totals' => array_merge(
                self::calculateSonodTotals($sonodReports),
                self::calculatePaymentTotals($paymentReports)
            ),
        ];
    }

    private static function calculateSonodTotals($reports)
    {
        return [
            'total_pending' => $reports->sum('pending_count'),
            'total_approved' => $reports->sum('approved_count'),
            'total_cancel' => $reports->sum('cancel_count'),
        ];
    }

    private static function calculatePaymentTotals($reports)
    {
        return [
            'total_payments' => $reports->sum('total_payments'),
            'total_amount' => number_format((float) $reports->sum('total_amount'), 2, '.', ''),
        ];
    }
}
