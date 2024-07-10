<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\ToDoList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all(); 

        return view('user', compact('users')); 
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
        //
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $attendance = AttendanceModel::where('user_id', $id)->get(); 
        $todos = ToDoList::orderBy('date')->where('user_id', $id)->get()->groupBy('date');

        return view('show-user', compact('user', 'attendance', 'todos'));
    }
    
    
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    
    public function destroy(string $id)
    {
        //
    }

    public function updateStatus($id)
    {
        $todo = ToDoList::findOrFail($id);
        $todo->status = $todo->status == 'Completed' ? 'Pending' : 'Completed';
        if ($todo->status == 'Pending') {
            $todo->pesan = null; 
        }
        $todo->save();

        return redirect()->route('show.user', ['id' => $todo->user_id])->with('success', 'Status berhasil diupdate.');
    }

    public function pesan(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $todo = ToDoList::findOrFail($id);
        $todo->pesan = $request->pesan;
        $todo->save();

        return redirect()->route('show.user', ['id' => $todo->user_id])->with('success', 'Pesan berhasil disimpan.');
    }
}
