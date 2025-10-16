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
        // chmod 770 /var/run/asterisk/asterisk.ctl
        // service php-fpm restart

        $command = AsteriskCommand::findOrFail($id);
        $escapedCommand = escapeshellarg($command->command);
        $fullCommand = "/usr/local/sbin/asterisk -rx $escapedCommand";

        $output = shell_exec($fullCommand);

        return response()->json(['output' => $output]);
    }
}