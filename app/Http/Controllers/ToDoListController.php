<?php

namespace App\Http\Controllers;

use App\Models\ToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'content' => 'required|string',
            'status' => 'required|string',
            'date' => 'required|date',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Mengambil id pengguna yang sedang masuk
        ToDoList::create($data);

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'status' => 'required|string',
            'date' => 'required|date',
        ]);

        $todolist = ToDoList::findOrFail($id);
        $todolist->update($request->all());

        return redirect()->route('ToDoList.index')
            ->with('success', 'To-Do List berhasil diupdate.');
    }

    public function show()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $todolist = ToDoList::findOrFail($id);
        $todolist->delete();

        return redirect()->route('ToDoList.index')
            ->with('success', 'To Do List berhasil dihapus.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus($id)
    {
        $todo = ToDoList::find($id);
        $todo->status = $todo->status == 'Completed' ? 'Pending' : 'Completed';
        $todo->save();

        return redirect()->route('ToDoList.index')->with('success', 'To Do List Berhasil Di Update.');
    }
}
