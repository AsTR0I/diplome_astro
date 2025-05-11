<?php

namespace App\Http\Controllers;

use App\Http\Resources\SipPacketResource;
use App\SipPacket;
use DB;
use Illuminate\Http\Request;

class SnifferController extends Controller
{
    public function index()
    {
        $packetsList = SipPacket::select('sip_packets.*')
            ->join(DB::raw('(
                SELECT MIN(id) as id
                FROM sip_packets
                GROUP BY call_id) as first_packets'),  'sip_packets.id', '=', 'first_packets.id')
                ->orderBy('captured_at', 'desc')
                ->paginate(50);
        return SipPacketResource::collection(($packetsList)); 
    }

    public function sipSession(Request $request) {
        $call_id = $request->get('call_id');
        $packetsList = SipPacket::where('call_id' , $call_id)->orderBy('captured_at', 'asc')->get();
        return SipPacketResource::collection($packetsList);
    }
}
