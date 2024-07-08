<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $userId = Auth::id();
        echo ($userId);

        $todayDate = Carbon::today()->setTimezone('Asia/Jakarta')->toDateString();

        $attendance = AttendanceModel::where('user_id', $userId)->whereDate('created_at', $todayDate)->get();

        return view('pages', compact('attendance'));
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

        $inTime = Carbon::parse($request->input('in'))->setTimezone('Asia/Jakarta');

        AttendanceModel::create([
            'user_id' => $userId,
            'in' => $inTime,
            'status' => 'Masuk',
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

        return redirect()->route('pages');
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
    public function update(Request $request, AttendanceModel $attendanceModel, $id)
    {
        $request->validate([
            'in' => 'nullable|date',
            'out' => 'nullable|date'
        ]);

        $userId = Auth::id();

        $outTime = Carbon::parse($request->input('out'))->setTimezone('Asia/Jakarta');

        AttendanceModel::where('id', $id)->update([
            'user_id' => $userId,
            'out' => $outTime,
            'status' => 'Keluar',
        ]);

        

        return redirect()->route('pages');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceModel $attendanceModel)
    {
        //
    }
}
