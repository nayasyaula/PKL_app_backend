<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\User;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class AttendanceModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendance = AttendanceModel::all();

        return view('admin.pages.index', compact('attendance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('admin.pages.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'in' => 'required',
            'out' => 'required',
            'status' => 'required',
            'user_id' => 'required'
        ]);

        AttendanceModel::create([
            'in' => $request->in,
            'out' => $request->out,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('admin.pages.home')->with('success', 'Berhasil menambahkan data kehadiran');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceModel $attendanceModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceModel $attendanceModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceModel $attendanceModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceModel $attendanceModel)
    {
        //
    }
}
