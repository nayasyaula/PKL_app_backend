<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\ToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Response;

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
    }   

    public function createDocument()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('HAIIIIIIIIIIIII');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        $filePath = storage_path('app/public/helloWorld.docx');
        $objWriter->save($filePath);

        return Response::download($filePath);
    }
}
