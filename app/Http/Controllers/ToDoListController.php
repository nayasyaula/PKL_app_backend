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

    // Setting font styles
    $phpWord->addFontStyle('headerStyle', ['bold' => true, 'size' => 12]);
    $phpWord->addFontStyle('contentStyle', ['size' => 10]);

    // Setting paragraph styles
    $phpWord->addParagraphStyle('centered', ['alignment' => 'center']);
    $phpWord->addParagraphStyle('left', ['alignment' => 'left']);
    
    // Adding a section
    $section = $phpWord->addSection();

    // Adding the header
   // Tambahkan teks dengan format yang diinginkan
$section->addText('SMK WIKRAMA BOGOR', ['bold' => true, 'size' => 14, 'color' => '4F4F4F', 'alignment' => 'center']);
$section->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', ['size' => 10, 'color' => '4F4F4F', 'alignment' => 'center']);
$section->addText('Telp/Fax. (0251) 8242411', ['size' => 10, 'color' => '4F4F4F', 'alignment' => 'center']);
$section->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smk wikrama.sch.id', ['size' => 10, 'color' => '0000FF', 'alignment' => 'center']);

// Tentukan koordinat untuk posisi gambar di sebelah kiri teks di tengah halaman
$logoPath = 'assets/img/logo-wk.png'; // Ganti dengan path yang benar ke logo Anda

$section->addImage(
    $logoPath,
    array(
        'width' => 50,  // Lebar gambar
        'height' => 50, // Tinggi gambar
        'wrappingStyle' => 'square', // Gaya pembungkus (misalnya, persegi)
        'positioning' => 'absolute',
        'posHorizontal' => 'left', // Posisi horizontal di sebelah kiri
        'posVertical' => 'top', // Posisi vertikal di bagian atas halaman
        'marginTop' => 10,  // Jarak dari atas
        'marginLeft' => 0, // Jarak dari kiri
        'marginRight' => 0, // Jarak dari kanan
        'marginBottom' => 0 // Jarak dari bawah
    )
);


$section->addTextBreak(1);


    // Adding the report title
    $section->addText('LAPORAN KEGIATAN HARIAN', 'headerStyle', 'centered');
    $section->addText('PESERTA PRAKTIK KERJA LAPANGAN (PKL) TAHUN 2024', 'headerStyle', 'centered');
    $section->addTextBreak(1);

    // Fetching data from the database
    $user = Auth::user(); // Assuming the user is logged in
    $todos = ToDoList::where('user_id', $user->id)->orderBy('date')->get();

    // Adding the details
    $section->addText('Nama Peserta Didik     : ' . $user->name, 'contentStyle', 'left');
    $section->addText('Industri Tempat PKL    :', 'contentStyle', 'left');
    $section->addText('Nama Instruktur/Pembimbing Industri :', 'contentStyle', 'left');
    $section->addText('Nama Guru Pembimbing   :', 'contentStyle', 'left');
    $section->addTextBreak(1);

    // Adding the table
    $tableStyle = [
        'borderSize' => 6,
        'borderColor' => '000000',
        'cellMargin' => 50,
    ];
    $firstRowStyle = [
        'borderBottomSize' => 18,
        'borderBottomColor' => '000000',
    ];
    $cellStyle = [
        'valign' => 'center',
    ];

    $phpWord->addTableStyle('tableStyle', $tableStyle, $firstRowStyle);
    $table = $section->addTable('tableStyle');

    // Header row
    $table->addRow();
    $table->addCell(500, $cellStyle)->addText('No.', 'headerStyle', 'centered');
    $table->addCell(2000, $cellStyle)->addText('Hari/Tanggal', 'headerStyle', 'centered');
    $table->addCell(4000, $cellStyle)->addText('Unit Kerja/Pekerjaan', 'headerStyle', 'centered');
    $table->addCell(2000, $cellStyle)->addText('Catatan*', 'headerStyle', 'centered');

    // Data rows
    foreach ($todos as $index => $todo) {
        $table->addRow();
        $table->addCell(500, $cellStyle)->addText($index + 1, 'contentStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText($todo->date->format('d-m-Y'), 'contentStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText($todo->content, 'contentStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText($todo->keterangan, 'contentStyle', 'centered');
    }

    // Save the file
    $fileName = 'Laporan_PKL_' . now()->format('Y-m-d_H-i-s') . '.docx';
    $filePath = storage_path('app/public/' . $fileName);

    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
}
}
