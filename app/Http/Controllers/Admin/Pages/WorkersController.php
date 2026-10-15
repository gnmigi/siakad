<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkersController extends Controller
{
    public function indexStudent()
    {
        return view('admin.pages.workers.student-index');
    }

    public function createStudent()
    {
        return view('admin.pages.workers.student-create');
    }

    public function editStudent($code)
    {
        return view('admin.pages.workers.student-edit', compact('code'));
    }

    public function storeStudent(Request $request)
    {
        return redirect()->back()->with('success', 'Data mahasiswa berhasil disimpan');
    }

    public function updateStudent(Request $request, $code)
    {
        return redirect()->back()->with('success', 'Data mahasiswa berhasil diupdate');
    }

    public function destroyStudent($code)
    {
        return redirect()->back()->with('success', 'Data mahasiswa berhasil dihapus');
    }
}
