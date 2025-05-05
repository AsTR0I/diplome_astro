<?php

namespace App\Http\Controllers;

use App\AsteriskCommand;
use App\Http\Resources\AsteriskCommandResource;
use Illuminate\Http\Request;

class AsteriskCommandsController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
        
    public function index()
    {
        $commands = AsteriskCommand::orderBy('id')
        ->paginate(10);
        return AsteriskCommandResource::collection($commands);
    }

    public function execute($id)
    {
        // pw groupmod asterisk -m www
        $command = AsteriskCommand::findOrFail($id);

        $escapedCommand = escapeshellarg($command->command);
        $output = shell_exec("/usr/local/sbin/asterisk -rx '$escapedCommand'");

        return response()->json(['output' => $output]);
    }
}
