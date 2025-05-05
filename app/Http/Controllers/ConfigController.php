<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use SplFileInfo;
use Storage;

class ConfigController extends Controller
{
    // const BASE_PATH = "/home/astro";

    // prod
    const BASE_PATH = "/usr/local/etc/asterisk";

    public function __construct()
    {
        $this->middleware("auth");
    }
    public function index(Request $request)
    {
        $basePath = self::BASE_PATH; 
        $relativePath = $request->get("path", "");
        $realPath = realpath($basePath . DIRECTORY_SEPARATOR . $relativePath);
    
        if ($realPath === false || !str_starts_with($realPath, $basePath)) {
            return response()->json(['error' => 'Access denied.'], 403);
        }
    
        $filePaths = File::files($realPath);
        $dirPaths = File::directories($realPath);
    
        // Преобразуем файлы
        $files = collect($filePaths)->map(function ($path) {
            $file = new SplFileInfo($path);
            return [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'type' => 'file',
                'extension' => $file->getExtension(), // Получаем расширение файла
            ];
        });
    
        // Преобразуем директории
        $directories = collect($dirPaths)->map(function ($path) {
            $dir = new SplFileInfo($path);
            return [
                'name' => $dir->getFilename(),
                'modified' => date('Y-m-d H:i:s', $dir->getMTime()),
                'type' => 'directory',
            ];
        });
    
        $items = $directories->merge($files)->sortBy('type')->values();
    
        $perPage = 25;
        $page = max((int) $request->get('page', 1), 1);
        $total = $items->count();
        $pagedItems = $items->forPage($page, $perPage)->values();

        $cleanRelativePath = trim(str_replace($basePath, '', $realPath), DIRECTORY_SEPARATOR);
        $parentPath = dirname($cleanRelativePath);

        return response()->json([
            'data' => $pagedItems,
            'meta' => [
                'relative_path' => $cleanRelativePath,
                'parent_path' => $parentPath,
                'files_count' => $files->count(),
                'directories_count' => $directories->count(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                ],
            ],
        ]);
    }
    
    public function getfileContent(Request $request)
    {
        $path = $request->input('file_path');

        $filePath = realpath(self::BASE_PATH . DIRECTORY_SEPARATOR . $path);

        if(File::exists($filePath)) {
            $fileContent = File::get($filePath);
            return response()->json([
                'success' => true,
                'content' => $fileContent
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Файл не найден'
        ], 404);
    }

    public function updateFileContent(Request $request)
    {
        $request->validate([
            'name' =>  'required|string',
            'content' => 'nullable|string',
        ]);

        $name = $request->input('name');
        $filePath = $request->input('path', '/');
        $content = $request->input('content');

        $basePath = self::BASE_PATH;
        $relativePath = trim( $filePath, '/');
        $realPath  = realpath($basePath . '/' . $relativePath . '/' . $name);

        if (!str_starts_with($realPath, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещён. Неверный путь.'
            ], 403);
        }

        try {
            File::put($realPath, $content);
    
            return response()->json([
                'success' => true,
                'message' => 'Файл успешно сохранён.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении файла: ' . $e->getMessage()
            ], 500);
        }
    
    }
    public function rename(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string',
        ]);

        $basePath = self::BASE_PATH;
        $relativePath = trim($request->input('path'), '/');
        $oldName = $request->input('old_name');
        $newName = $request->input('new_name');

        $oldPath = realpath($basePath . '/' . $relativePath . '/' . $oldName);
        $newPath = $basePath . '/' . $relativePath . '/' . $newName;

        if (!$oldPath || !str_starts_with($oldPath, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Недопустимый путь.',
            ], 403);
        }
    
        if ($oldName === $newName) {
            return response()->json([
                'success' => false,
                'message' => 'Имя не изменилось.',
            ], 500);
        }

        if (File::exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл или директория с таким именем уже существует.',
            ], 500);
        }

        try {
            File::move($oldPath, $newPath);
    
            return response()->json([
                'success' => true,
                'message' => 'Переименование успешно.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при переименовании: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $filePath = $request->input('path', '/');
        $name = $request->input('name');

        $basePath = self::BASE_PATH;
        $relativePath = trim($request->input('path'), '/');
        $realPath  = realpath($basePath . '/' . $relativePath . '/' . $name);

        if (!str_starts_with($realPath, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещён. Неверный путь.'
            ], 403);
        }

        try {
            if (File::isDirectory($realPath)) {
                File::deleteDirectory($realPath);
            } else {
                File::delete($realPath);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Файл или папка успешно удалены.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
    
        $filePath = $request->input('path', '/');
        $name = $request->input('name');
    
        $basePath = self::BASE_PATH;
        $relativePath = trim($filePath, '/');
        $realPath = realpath($basePath . '/' . $relativePath . '/' . $name);
    
        if (!$realPath || !str_starts_with($realPath, $basePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещён. Неверный путь.'
            ], 403);
        }
    
        if (!file_exists($realPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден.'
            ], 404);
        }
    
        return response()->download($realPath, $name);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'path' => 'nullable|string'
        ]);
    
        $file = $request->file('file');
        $relativePath = trim($request->input('path'), '/');
        $uploadDir = realpath(self::BASE_PATH . '/' . $relativePath);
    
        if (!$uploadDir || !str_starts_with($uploadDir, realpath(self::BASE_PATH))) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный путь для загрузки.'
            ], 403);
        }

        $file->move($uploadDir, $file->getClientOriginalName());

        return response()->json([
            'success' => true,
            'message' => 'Файл загружен.'
        ]);
    }
    
}
