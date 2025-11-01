<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function readAll(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return back();
    }

    public function open(Request $request, string $id)
    {
        $user = $request->user();
        $n = $user->notifications()->where('id', $id)->firstOrFail();
        if (is_null($n->read_at)) $n->markAsRead();
        $reportId = $n->data['report_id'] ?? null;
        if ($reportId) {
            return redirect()->route('kerusakan.show', $reportId);
        }
        return back();
    }

    public function poll(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }
}
