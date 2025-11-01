<?php

namespace App\Http\Controllers;

use App\Commodity;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Commodity::query();

        // Filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        // condition can be numeric (1,2,3) or string (Baik/Kurang Baik/Rusak Berat)
        if ($request->filled('condition')) {
            $conditionParam = $request->input('condition');
            $conditionCode = match (true) {
                is_numeric($conditionParam) => (int) $conditionParam,
                strcasecmp($conditionParam, 'Baik') === 0 => 1,
                strcasecmp($conditionParam, 'Kurang Baik') === 0 => 2,
                strcasecmp($conditionParam, 'Rusak Berat') === 0 => 3,
                default => null,
            };
            if ($conditionCode) {
                $query->where('condition', $conditionCode);
            }
        }

        if ($request->filled('material')) {
            $query->where('material', $request->input('material'));
        }

        $commodities = (clone $query)->latest()->get();

        // Summary counts
        $conditionCounts = (clone $query)
            ->selectRaw('`condition`, COUNT(*) AS count')
            ->groupBy('condition')
            ->get()
            ->map(function ($row) {
                // reuse getConditionName mapping
                $dummy = new Commodity([ 'condition' => $row->condition ]);
                return collect([
                    'condition' => (int) $row->condition,
                    'condition_name' => $dummy->getConditionName(),
                    'count' => (int) $row->count,
                ]);
            });

        $summary = [
            'total' => $conditionCounts->sum('count') ?? 0,
            'good' => $conditionCounts->firstWhere('condition', 1)['count'] ?? 0,
            'not_good' => $conditionCounts->firstWhere('condition', 2)['count'] ?? 0,
            'heavily_damage' => $conditionCounts->firstWhere('condition', 3)['count'] ?? 0,
        ];

        // Charts
        $countByYear = (clone $query)
            ->selectRaw('COUNT(*) AS count, year_of_purchase')
            ->groupBy('year_of_purchase')
            ->orderBy('year_of_purchase')
            ->get();

        $chartYear = [
            'categories' => $countByYear->pluck('year_of_purchase'),
            'series' => $countByYear->pluck('count'),
        ];

        $orderedConditions = [1 => 'Baik', 2 => 'Kurang Baik', 3 => 'Rusak Berat'];
        $pieSeries = [];
        $pieLabels = [];
        foreach ($orderedConditions as $code => $label) {
            $pieLabels[] = $label;
            $pieSeries[] = (int) ($conditionCounts->firstWhere('condition', $code)['count'] ?? 0);
        }

        $chartCondition = [
            'categories' => $pieLabels,
            'series' => $pieSeries,
        ];

        // Filter option sources
        $materials = Commodity::select('material')->distinct()->orderBy('material')->pluck('material');

        return view('reports.index', [
            'title' => 'Laporan',
            'page_heading' => 'Laporan',
            'commodities' => $commodities,
            'summary' => $summary,
            'charts' => [
                'year' => $chartYear,
                'condition' => $chartCondition,
            ],
            'materials' => $materials,
        ]);
    }
}

