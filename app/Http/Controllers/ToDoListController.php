<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\ToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToDoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = ToDoList::orderBy('date')->get()->groupBy('date');

        return view('ToDoList.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ToDoList.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'status' => 'required|string',
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        ToDoList::create([
            'content' => $request->content,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'date' => $request->date,
            'user_id' => auth()->user()->id,
            'pesan' => null,
        ]);

        return redirect()->route('ToDoList.index')
            ->with('success', 'To-Do List berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $todolist = ToDoList::findOrFail($id);
        return view('ToDoList.edit', compact('todolist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'status' => 'required|string',
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $todolist = ToDoList::findOrFail($id);
        $todolist->update([
            'content' => $request->content,
            'status' => $request->status,
            'date' => $request->date,
            'keterangan' => $request->keterangan,
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('ToDoList.index')
            ->with('success', 'To-Do List berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $todolist = ToDoList::findOrFail($id);
        $todolist->delete();

        return redirect()->route('ToDoList.index')
            ->with('success', 'To-Do List berhasil dihapus.');
    }

    public function updateStatus($id)
    {
        $todo = ToDoList::find($id);
        $todo->status = $todo->status == 'Completed' ? 'Pending' : 'Completed';
        $todo->save();

        return redirect()->route('ToDoList.index')->with('success', 'Status berhasil diupdate.');
    }


    public function pesan(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $todo = ToDoList::findOrFail($id);
        $todo->pesan = $request->pesan;
        $todo->save();

        return redirect()->route('ToDoList.index')->with('success', 'Pesan berhasil disimpan.');
        // return redirect()->route('show-user')->with('success', 'Pesan berhasil disimpan.');

    }   

//     public function showUser()
// {
//     $user = Auth::user(); // Ambil data user saat ini
//     $todos = ToDoList::all(); // Ambil data ToDoList
//     $attendance = AttendanceModel::all(); // Ambil data kehadiran

//     return view('show-user', [
//         'todos' => $todos, // Kirimkan data yang diperlukan ke view show-user.blade.php
//     ]);
// }

}
