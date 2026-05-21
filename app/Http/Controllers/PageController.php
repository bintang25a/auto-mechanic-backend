<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Damage;
use App\Models\Queue;
use App\Models\Rule;
use App\Models\Symptom;
use App\Models\User;
use Carbon\Carbon;

class PageController extends Controller
{
    public function adminPageRules()
    {
        $rules = Rule::all();

        $symptoms = Symptom::query()
            ->select(['symptom_code', 'name'])
            ->get()
            ->toArray();

        $damages = Damage::query()
            ->select(['damage_code', 'name'])
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Get all rules',
            'symptoms' => $symptoms,
            'damages' => $damages,
            'rules' => $rules,
        ], 200);
    }

    public function landingPageData()
    {
        $today = Carbon::today();

        $totalCustomers = User::query()->where('role', 'customer')->count();

        $totalServices = Complaint::query()->count();

        $todayQueue = Queue::query()->whereDate('created_at', $today)->count();

        $currentQueue = Queue::query()
            ->where('status', 'process')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (! $currentQueue) {
            $currentQueue = Queue::query()
                ->where('status', 'waiting')
                ->orderBy('created_at', 'asc')
                ->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Get dashboard statistics successfully',
            'data' => [
                'total_customers' => $totalCustomers,
                'total_services' => $totalServices,
                'current_queue' => $currentQueue->queue_number,
                'today_queue' => $todayQueue,
            ],
        ], 200);
    }
}
