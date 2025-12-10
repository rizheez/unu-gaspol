<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Summary;
use Inertia\Inertia;

use Illuminate\Support\Facades\Artisan;

class SummaryController extends Controller
{
    public function index()
    {
        // Fetch all summaries, maybe ordered by prodi name
        $summaries = Summary::orderBy('prodi_name')->get();
        return Inertia::render('Summary/Index', [
            'summaries' => $summaries
        ]);
    }

    public function sync()
    {
        try {
            Artisan::call('app:fetch-summary');
            return redirect()->back()->with('success', 'Data summary berhasil disinkronisasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyinkronkan data: ' . $e->getMessage());
        }
    }
}
