<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AttendanceModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendance = AttendanceModel::all();

        return view('admin.pages', compact('attendance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $attendance = AttendanceModel::all();

        $userId = Auth::id();

        AttendanceModel::create([
            'user_id' => $userId,
            'in' => $request->input('in'),
            'status' => 'masuk', 
        ]);

        $validated = $request->validate([
            'in' => 'nullable|date',
            'out' => 'nullable|date'
        ]);
        
        // $attendance = new AttendanceModel();
        // $attendance->in = $validated['in'];
        // $attendance->out = $validated['out'];
        // $attendance->status = $attendance->out ? 'keluar' : 'masuk';
        // $attendance->save();

        return view('admin.pages', compact('attendance'));
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
        $validated = $request->validate([
            'out' => 'nullable|date'
        ]);

        $userId = Auth::id();

        // Find today's attendance record for the current user
        $attendance = AttendanceModel::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->first();

        if ($attendance) {
            // If 'out' is null, use the current time
            $outTime = $validated['out'] ?? now();

            // Update the attendance record
            $attendance->update([
                'out' => $outTime,
                'status' => 'keluar',
            ]);

            return view('admin.pages', compact('attendance'));
        } else {
            // Handle the case where the attendance record does not exist
            return redirect()->back()->withErrors(['message' => 'Attendance record not found.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceModel $attendanceModel)
    {
        //
    }

}
