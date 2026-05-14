<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $queues = Queue::query()
            ->with('complaint')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->number, fn ($q) => $q->where('queue_number', $request->number))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Get all queue,'.$request->status,
            'data' => $queues,
        ]);
    }

    public function current()
    {
        $current = Queue::query()->where('status', 'processing')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (! $current) {
            $current = Queue::query()->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->first();
        }

        return response()->json([
            'success' => true,
            'data' => $current,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,done,skipped',
            'mechanic_id' => 'nullable|string|exists:users,uid',
        ]);

        $queue = Queue::query()->find($id);

        if (! $queue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
            ], 404);
        }

        $queue->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status antrian berhasil diperbarui',
            'data' => $queue,
        ]);
    }
}
