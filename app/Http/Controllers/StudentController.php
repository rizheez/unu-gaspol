<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;
use Inertia\Inertia;

use Illuminate\Support\Facades\Artisan;

use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('prodi')) {
            $query->where('prodi_name', $request->prodi);
        }

        if ($request->filled('search')) {
             $search = $request->search;
             $query->where(function($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                   ->orWhere('nik', 'like', "%{$search}%")
                   ->orWhere('nim', 'like', "%{$search}%");
             });
        }

        $students = $query->paginate(10)->withQueryString();
        
        $statuses = Student::select('status')->distinct()->whereNotNull('status')->pluck('status');
        $prodis = Student::select('prodi_name')->distinct()->whereNotNull('prodi_name')->orderBy('prodi_name')->pluck('prodi_name');

        return Inertia::render('Students/Index', [
            'students' => $students,
            'statuses' => $statuses,
            'prodis' => $prodis,
            'filters' => $request->only(['status', 'prodi', 'search']),
        ]);
    }

    public function export() 
    {
        return Excel::download(new StudentExport, 'update-gaspol.xlsx');
    }

    public function sync()
    {
        try {
            Artisan::call('app:fetch-students');
            return redirect()->back()->with('success', 'Data mahasiswa berhasil disinkronisasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyinkronkan data: ' . $e->getMessage());
        }
    }
}
