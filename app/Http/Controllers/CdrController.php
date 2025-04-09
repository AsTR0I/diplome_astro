<?php

namespace App\Http\Controllers;

use App\Http\Resources\CdrResource;
use App\Cdr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CdrController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $calls = Cdr::orderBy('calldate', 'desc')
            ->when($request->filled('disposition'), function ($query) use ($request) {
                return $query->where('disposition', $request->input('disposition'));
            })
            ->when($request->filled('src'), function ($query) use ($request) {
                $src = '%'.$request->input('src').'%';
                return $query->where('src', 'like', $src);
            })
            ->when($request->filled('dst'), function ($query) use ($request) {
                $dst = '%'.$request->input('dst').'%';
                return $query->where('dst', 'like', $dst);
            })
            ->paginate(50);

        return CdrResource::collection(($calls));
    }

    public function dashboardChart(Request $request)
    {
        $period = $request->input('period', 'today');
        $periodData = $this->getPeriod($period);
    
        $query = Cdr::orderBy('calldate', 'asc');
    
        if ($period !== 'all') { // Только если период не "all", применяем whereBetween
            $query->whereBetween('calldate', [$periodData['start'], $periodData['end']]);
        }
    
        $calls = $query->get();
    
        // Группируем звонки по дням
        $groupedCalls = $calls->groupBy(fn($call) => Carbon::parse($call->calldate)->format('Y-m-d'));
    
        $labels = $groupedCalls->keys();
        $data = $groupedCalls->map->count()->values();
    
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Количество звонков',
                    'backgroundColor' => 'rgba(51, 102, 204, 0.5)',
                    'borderColor' => 'rgba(51, 102, 204, 1)',
                    'data' => $data
                    
                ]
            ],
            'total' => $calls->count()
        ]);
    }
    
    private function getPeriod($period)
    {
        $now = Carbon::now();
    
        switch ($period) {
            case 'today':
                return ['start' => $now->startOfDay(), 'end' => $now->endOfDay()];
            case '-1day':
                return ['start' => $now->subDay()->startOfDay(), 'end' => $now->subDay()->endOfDay()];
            case 'month':
                return ['start' => $now->startOfMonth(), 'end' => $now->endOfMonth()];
            case 'half-of-year':
                return ['start' => $now->subMonths(6)->startOfMonth(), 'end' => $now->endOfMonth()];
            case 'all':
                return null; // Вернём null, чтобы код знал, что фильтр не нужен
            default:
                return ['start' => $now->startOfDay(), 'end' => $now->endOfDay()];
        }
    }
    
}
