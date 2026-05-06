<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Rule;
use App\Models\Symptom;

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
}
