<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemInfoController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }
    // resources - cpu, ram, cpu-info, uptime, cpu-speed, memory-usages
    public function resources()
    {
        $data = [
            'cpu' => $this->getCpuLoad(),
            'cpu_model' => $this->getCpuModel(),
            'ram' => $this->getRamInfo(),
            'uptime' => $this->getUptime(),
            'cpu_speed' => $this->getCpuSpeed(),
            'memory_usages' => $this->getMemoryUsagePercent(),
        ];

        return response()->json($data);
    }

    public function cpuChart()
    {
        $os = strtolower(PHP_OS);
        $usedPercent = 0;
        $freePercent = 0;

        if (str_contains($os, 'linux')) {
            $cpuCount = shell_exec("nproc") ?: 1; // количество логических CPU
            $load = sys_getloadavg()[0]; // нагрузка за 1 минуту

            $usedPercent = min(100, round($load / $cpuCount * 100, 2));
            $freePercent = round(100 - $usedPercent, 2);
        } else if (str_contains($os, 'freebsd') || str_contains($os, 'darwin')) {
            // Получаем количество логических CPU
            $cpuCount = (int) shell_exec("sysctl -n hw.ncpu") ?: 1;

            // Получаем среднюю нагрузку за 1 минуту
            $loadAvgRaw = shell_exec("sysctl -n vm.loadavg");
            preg_match('/{ (.*) }/', $loadAvgRaw, $matches);

            if (isset($matches[1])) {
                $loadParts = explode(' ', trim($matches[1]));
                $load = (float) $loadParts[0] ?? 0.0;

                $usedPercent = min(100, round($load / $cpuCount * 100, 2));
                $freePercent = round(100 - $usedPercent, 2);
            }
        }

        return response()->json([
            'labels' => ['Использовано', 'Свободно'],
            'datasets' => [
                [
                    'data' => [$usedPercent, $freePercent],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB']
                ]
            ]
        ]);
    }

    public function osResources()
    {
        $os = strtolower(PHP_OS);
        $data = [];

        $data['os_name'] = php_uname('s'); // Название ОС (например, Linux)
        $data['os_release'] = php_uname('r'); // Релиз ОС (например, 5.4.0-74-generic)
        $data['os_version'] = php_uname('v'); // Версия ОС (например, #83-Ubuntu SMP)
        $data['architecture'] = php_uname('m'); // Архитектура (например, x86_64)
        $data['hostname'] = gethostname(); // Имя хоста (например, my-hostname)

        return response()->json($data);

    }

    public function hardDriversResources()
    {
        $os = strtolower(PHP_OS);
        $data = [];
    
        // Для Linux
        if (str_contains($os, 'linux')) {
            $output = shell_exec('lsblk -o NAME,SIZE,TYPE,MOUNTPOINT,MODEL');
            $data = $this->parseLsblkOutput($output);
        } 
        // Для FreeBSD
        else if (str_contains($os, 'freebsd')) {
            $output = shell_exec('gpart show');
            $data = $this->parseGpartOutput($output);
        }
    
        // Возвращаем результат в формате JSON
        return response()->json($data);
    }
    
    // Разбор вывода команды lsblk для Linux
    private function parseLsblkOutput($output)
    {
        $lines = explode("\n", $output);
        $disks = [];
        $currentDisk = null;
    
        foreach ($lines as $line) {
            if (empty($line)) continue;
    
            // Разбиваем строку на колонки
            $columns = preg_split('/\s+/', $line);
    
            // Пропускаем заголовки
            if ($columns[0] === "NAME") continue;
    
            // Обрабатываем данные дисков
            if ($columns[2] === "disk") {
                // Если это диск, создаём новый объект для него
                if ($currentDisk !== null) {
                    $disks[] = $currentDisk;
                }
                $currentDisk = [
                    'name' => $columns[0],
                    'size' => $columns[1],
                    'type' => $columns[2],
                    'mountpoint' => $columns[3] ?? null,
                    'model' => $columns[4] ?? null,
                    'partitions' => []
                ];
            } elseif ($columns[2] === "part") {
                // Если это раздел, добавляем информацию в текущий диск
                if ($currentDisk !== null) {
                    $currentDisk['partitions'][] = [
                        'name' => $columns[0],
                        'size' => $columns[1],
                        'mountpoint' => $columns[3] ?? null,
                    ];
                }
            }
        }
    
        // Добавляем последний диск в массив
        if ($currentDisk !== null) {
            $disks[] = $currentDisk;
        }
    
        return $disks;
    }
    
    // Разбор вывода команды gpart для FreeBSD (если необходимо)
    private function parseGpartOutput($output)
    {
        $lines = explode("\n", $output);
        $disks = [];
    
        foreach ($lines as $line) {
            if (empty($line)) continue;
    
            $columns = preg_split('/\s+/', $line);
    
            if (count($columns) >= 4) {
                $disks[] = [
                    'device' => $columns[0],
                    'size' => $columns[1],
                    'type' => $columns[2],
                    'partitions' => $columns[3] ?? null,
                ];
            }
        }
    
        return $disks;
    }

    public function ramChart()
    {
        $os = strtolower(PHP_OS);
        $usedMB = 0;
        $freeMB = 0;

        if (str_contains($os, 'linux')) {
            // Используем команду free для Linux
            $memInfo = explode("\n", shell_exec('free -b'));

            if (isset($memInfo[1])) {
                $data = preg_split('/\s+/', $memInfo[1]);
                $total = (int) $data[1];
                $used = (int) $data[2];

                $usedMB = round($used / (1024 * 1024), 1);  // Преобразуем байты в МБ
                $freeMB = round(($total - $used) / (1024 * 1024), 1);  // Преобразуем байты в МБ
            }
        } elseif (str_contains($os, 'freebsd') || str_contains($os, 'darwin')) {
            // FreeBSD или macOS
            // Получаем общий объём памяти
            $total = (int) shell_exec("sysctl -n hw.physmem");
            // Получаем размер страницы памяти
            $pageSize = (int) shell_exec("sysctl -n hw.pagesize");
            // Получаем количество свободных страниц
            $freePages = (int) shell_exec("sysctl -n vm.stats.vm.v_free_count");
            // Общий объём свободной памяти
            $free = $freePages * $pageSize;

            // Вычисляем использованную память
            $used = $total - $free;
            $usedMB = round($used / (1024 * 1024), 1);  // Преобразуем байты в МБ
            $freeMB = round($free / (1024 * 1024), 1);  // Преобразуем байты в МБ
        }

        return response()->json([
            'labels' => ['Используется (МБ)', 'Свободно (МБ)'],
            'datasets' => [
                [
                    'data' => [$usedMB, $freeMB],
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB']
                ]
            ]
        ]);
    }

    protected function getCpuLoad()
    {
        return [
            '1min' => sys_getloadavg()[0],
            '5min' => sys_getloadavg()[1],
            '15min' => sys_getloadavg()[2],
        ];
    }

    protected function getRamInfo()
    {
        $os = strtolower(PHP_OS);

        if (str_contains($os, 'linux')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $meminfo, $total);
            preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $available);

            return [
                'total_kb' => (int) ($total[1] ?? 0),
                'available_kb' => (int) ($available[1] ?? 0),
                'total_mb' => round(($total[1] ?? 0) / 1024, 2),
                'available_mb' => round(($available[1] ?? 0) / 1024, 2),
            ];
        } elseif (str_contains($os, 'freebsd') || str_contains($os, 'darwin')) {
            $total = (int) shell_exec("sysctl -n hw.physmem") / 1024;
            $pageSize = (int) shell_exec("sysctl -n hw.pagesize");
            $freePages = (int) shell_exec("sysctl -n vm.stats.vm.v_free_count");
            $free = $freePages * $pageSize / 1024;

            return [
                'total_kb' => (int) $total,
                'available_kb' => (int) $free,
                'total_mb' => round($total / 1024, 2),
                'available_mb' => round($free / 1024, 2),
            ];
        }

        return [
            'total_kb' => 0,
            'available_kb' => 0,
            'total_mb' => 0,
            'available_mb' => 0,
        ];
    }

    protected function getCpuModel()
    {
        $os = strtolower(PHP_OS);

        if (str_contains($os, 'linux') && file_exists('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if (preg_match('/model name\s+:\s+(.*)/', $cpuinfo, $matches)) {
                return trim($matches[1]);
            }
        } elseif (str_contains($os, 'freebsd') || str_contains($os, 'darwin')) {
            return trim(shell_exec("sysctl -n hw.model"));
        }

        return 'Unknown';
    }

    protected function getUptime()
    {
        return trim(shell_exec('uptime -p')) ?: 'N/A';
    }

    protected function getCpuSpeed()
    {
        $os = strtolower(PHP_OS);

        if (str_contains($os, 'linux') && file_exists('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if (preg_match('/cpu MHz\s+:\s+(.*)/', $cpuinfo, $matches)) {
                return $matches[1] . ' MHz';
            }
        } elseif (str_contains($os, 'freebsd') || str_contains($os, 'darwin')) {
            return trim(shell_exec("sysctl -n hw.clockrate")) . ' MHz';
        }

        return 'Unknown';
    }

    protected function getMemoryUsagePercent()
    {
        $ram = $this->getRamInfo();

        if ($ram['total_kb'] === 0) {
            return 'N/A';
        }

        $used = $ram['total_kb'] - $ram['available_kb'];
        $percent = ($used / $ram['total_kb']) * 100;

        return number_format($percent, 2);
    }
}
