<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\ToDoList;
use Carbon\Carbon;
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

    $userId = Auth::id();

    $todayDate = Carbon::today()->setTimezone('Asia/Jakarta')->toDateString();

    // Ambil satu entitas Attendance sesuai dengan user_id dan tanggal hari ini
    $attendance = AttendanceModel::where('user_id', $userId)
                                  ->whereDate('created_at', $todayDate)
                                  ->first();

    // Pastikan attendance ditemukan sebelum mencoba mengambil id-nya
    $attendanceId = $attendance ? $attendance->id : null;

    // Simpan data ToDoList
    ToDoList::create([
        'content' => $request->content,
        'keterangan' => $request->keterangan,
        'status' => $request->status,
        'date' => $request->date,
        'user_id' => $userId,
        'attendance_id' => $attendanceId, // Gunakan id attendance yang sudah ditemukan
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
   // Buat section untuk header
    $header = $section->addHeader();

    // Buat table untuk mengatur layout gambar dan teks
    $table = $header->addTable();
    $table->addRow();

    // Kolom pertama untuk gambar
    $imageCell = $table->addCell(2000); // Sesuaikan ukuran lebar cell untuk gambar
    $imageCell->addImage('../public/assets/img/logo-wk.png', array('width' => 80, 'height' => 80));

    // Kolom kedua untuk teks
    $textCell = $table->addCell();
    $textCell->addText('SMK WIKRAMA BOGOR', array('bold' => true, 'size' => 10));
    $textCell->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', array('size' => 8));
    $textCell->addText('Telp/Fax. (0251) 8242411', array('size' => 10));
    $textCell->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smkwikrama.sch.id', array('size' => 8));

    // Atur alignment untuk cell
    $imageCell->addTextRun()->addText('', null, array('alignment' => 'right'));
    $textCell->addTextRun()->addText('', null, array('alignment' => 'left'));

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
