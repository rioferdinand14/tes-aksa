<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('division');

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('division_id')) {
            $query->where('division_id', $request->input('division_id'));
        }

        $employees = $query->paginate(5);

        $employeesData = $employees->items();
        $employeesData = array_map(function ($employee){
            return [
                'id' => $employee->id,
                'image' => $employee->image, // Pastikan URL gambar disimpan dengan benar
                'name' => $employee->name,
                'phone' => $employee->phone,
                'division' => [
                    'id' => $employee->division->id,
                    'name' => $employee->division->name,
                ],
                'position' => $employee->position,
            ];
        }, $employeesData);

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'employees' => $employeesData,
            ],
            'pagination' => [
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'next_page_url' => $employees->nextPageUrl(),
                'prev_page_url' => $employees->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'division' => 'required',
            'position' => 'required|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        Employee::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'image' => $imagePath,
            'division_id' => $request->input('division'), 
            'position' => $request->input('position'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Employee created successfully',
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($id);
            // Validasi input
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:15',
                'division' => 'required', // Validasi ID divisi
                'position' => 'required|string|max:255',
            ]);

            // Cari karyawan berdasarkan ID
            $employee = Employee::where('id', $id)->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found',
                ], 404);
            }

            // Proses upload gambar jika ada
            $imagePath = $employee->image; // Gunakan gambar lama jika tidak ada gambar baru
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($employee->image) {
                    Storage::disk('public')->delete($employee->image);
                }
                // Simpan gambar baru
                $imagePath = $request->file('image')->store('images', 'public');
            }

            // Perbarui data karyawan
            $employee->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'image' => $imagePath,
                'division_id' => $request->input('division'),
                'position' => $request->input('position'),
            ]);

            // Mengembalikan response
            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cari karyawan berdasarkan UUID
        $employee = Employee::where('id', $id)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        // Hapus gambar jika ada
        if ($employee->image) {
            Storage::disk('public')->delete($employee->image);
        }

        // Hapus data karyawan
        $employee->delete();

        // Mengembalikan response
        return response()->json([
            'status' => 'success',
            'message' => 'Employee deleted successfully',
        ]);
    }
}
