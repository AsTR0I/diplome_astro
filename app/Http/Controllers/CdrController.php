<?php

namespace App\Http\Controllers;

use App\Http\Resources\CdrResource;
use App\Cdr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Response;

class CdrController extends Controller
{

    private const RECORDINGS_DIR = '/var/spool/asterisk/monitor/';

    public function __construct()
    {
        $this->middleware("auth");
    }
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
    
    public function exportToXLSX(Request $request)
    {

        $isPaginate = filter_var($request->input('paginate', false), FILTER_VALIDATE_BOOLEAN);
        $query = Cdr::orderBy('calldate', 'desc')
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
            });

            $calls = $isPaginate ?  $query->paginate(50) : $query->get();

            $calls_data = $isPaginate ? $calls->items() : $calls;

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
                'calldate', 'clid', 'src', 'dst', 'dcontext', 'channel', 'dstchannel',
                'lastapp', 'lastdata', 'duration', 'billsec', 'disposition',
                'amaflags', 'accountcode', 'uniqueid', 'userfield', 'did'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', strtoupper($header));
                $col++;
            }

            $rowNum = 2;
            foreach ($calls_data as $call) {
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . $rowNum, $call[$header] ?? '');
                    $col++;
                }
                $rowNum++;
            }

            $filename = 'cdr_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $filename);
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            return Response::download($tempFile, $filename)->deleteFileAfterSend(true);
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

    // Получить запись по uniqueid
    public function getRecording(string $uniqueid)
    {
        $files = glob(self::RECORDINGS_DIR . $uniqueid . '.wav');

        if (empty($files)) {
            abort(404, 'Recordint not found');
        }

        $filePath = $files[0];

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->streamDownload(function () use ($filePath) {
            readfile($filePath);
        }, basename($filePath), [
            'Content-Type' => 'audio/wav',
        ]);
    }
}
