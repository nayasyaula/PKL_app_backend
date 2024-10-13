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
use Illuminate\Support\Facades\Storage;

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
     * Test API endpoint to retrieve ToDoList items.
     */
    public function test(Request $request)
    {
        $user = $request->user();
        Log::info('User authenticated', ['user' => $user]);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Ambil userId dari pengguna
        $userId = $user->id;
        Log::info('User ID: ' . $userId);

        // Ambil todos berdasarkan userId
        $todos = ToDoList::where('user_id', $userId)->get();

        // Kembalikan respons JSON
        return response()->json(['todos' => $todos]);
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
    // Untuk Web
    public function storeWeb(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
            'status' => 'required|string',
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $userId = Auth::id();
        $todayDate = Carbon::today()->setTimezone('Asia/Jakarta')->toDateString();
        $attendance = AttendanceModel::where('user_id', $userId)
            ->whereDate('created_at', $todayDate)
            ->first();
        $attendanceId = $attendance ? $attendance->id : null;

        ToDoList::create([
            'content' => $request->content,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'date' => $request->date,
            'user_id' => $userId,
            'attendance_id' => $attendanceId,
            'pesan' => null,
        ]);

        return redirect()->route('ToDoList.index')->with('success', 'To-Do List berhasil dibuat.');
    }

    // Untuk API
    public function storeApi(Request $request)
    {
        Log::info('Received data', ['data' => $request->all()]);
        Log::info('Headers', ['headers' => $request->headers->all()]);

        $request->validate([
            'content' => 'required|string|max:255',
            'status' => 'required|string',
            'date' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        $userId = Auth::id();
        Log::info('userId', ['userId' => $userId]);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $todayDate = Carbon::today()->setTimezone('Asia/Jakarta')->toDateString();
        $attendance = AttendanceModel::where('user_id', $userId)
            ->whereDate('created_at', $todayDate)
            ->first();
        $attendanceId = $attendance ? $attendance->id : null;

        $todo = ToDoList::create([
            'content' => $request->content,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'date' => $request->date,
            'user_id' => $userId,
            'attendance_id' => $attendanceId,
            'pesan' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'To-Do List successfully created.',
            'data' => $todo
        ]);
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
     * Update the specified resource in storage for web.
     */
    public function updateWeb(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'content' => 'required|string|max:255',
        'keterangan' => 'required|string|max:255',
        'status' => 'required|in:Pending,Completed',
        'date' => 'required|date',
        'file' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,mp4,avi,mov', // Tambahkan validasi untuk file
    ]);

    // Temukan to-do list berdasarkan ID
    $todolist = ToDoList::findOrFail($id);

    // Update data lainnya
    $todolist->content = $request->input('content');
    $todolist->keterangan = $request->input('keterangan');
    $todolist->status = $request->input('status');
    $todolist->date = $request->input('date');

    // Jika ada file yang diunggah, simpan dan update path file
    if ($request->hasFile('file')) {
        // Hapus file lama jika ada
        if ($todolist->file_path) {
            Storage::delete($todolist->file_path);
        }
        
        // Simpan file baru
        $filePath = $request->file('file')->store('files', 'public');
        $todolist->file_path = $filePath; // Update path file
    }

    // Simpan perubahan ke database
    $todolist->save();

    // Redirect kembali ke daftar to-do list dengan pesan sukses
    return redirect()->route('ToDoList.index')->with('success', 'To-Do List updated successfully!');
}


    /**
     * Update the specified resource in storage for API.
     */
    public function updateApi(Request $request, $id)
    {

        // dd($request);
        Log::info('Received data', ['data' => $request->all()]);
        Log::info('Headers', ['headers' => $request->headers->all()]);

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

        return response()->json([
            'success' => true,
            'message' => 'To-Do List berhasil diupdate.',
            'data' => $todolist
        ]);
    }


    public function showApi($id)
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

    // Method untuk menampilkan form upload file
public function showUploadForm($id)
{
    $todo = ToDoList::findOrFail($id);
    return view('todolist.upload', compact('todo'));
}

// Method untuk menyimpan file setelah di-upload
public function uploadFile(Request $request, $id)
{
    $request->validate([
        'file' => 'nullable|mimes:jpg,jpeg,png,pdf,docx,mp4,avi,mov|max:51200', // Sesuaikan dengan tipe file yang diizinkan
    ]);

    $todo = ToDoList::find($id);

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filePath = $file->store('uploads', 'public'); // Simpan file ke folder 'uploads' di dalam folder 'storage/app/public'

        // Simpan path file ke database
        $todo->file_path = $filePath;
        $todo->save();
    }

    return redirect()->back()->with('success', 'File berhasil di-upload.');
}



    public function pesan(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $todo = ToDoList::findOrFail($id);
        $todo->pesan = $request->pesan;
        $todo->save();

        return redirect()->route('ToDoList.index')->with('success', 'message sent successfully');
    }

    public function createDocumentWeb()
    {
        $phpWord = new PhpWord();

        // Setting font styles
        $phpWord->addFontStyle('headerStyle', ['bold' => true, 'size' => 12]);
        $phpWord->addFontStyle('headerRowStyle', ['bold' => true, 'size' => 10]); // Ukuran font header row diperkecil dan ditambahkan bold
        $phpWord->addFontStyle('contentStyle', ['size' => 10]);
        $phpWord->addFontStyle('smallContentStyle', ['size' => 8]);

        // Setting paragraph styles
        $phpWord->addParagraphStyle('centered', ['alignment' => 'center']);
        $phpWord->addParagraphStyle('left', ['alignment' => 'left']);

        // Adding a section
        $section = $phpWord->addSection();

        // Buat section untuk header
        $header = $section->addHeader();

        // Buat table untuk mengatur layout gambar dan teks
        $table = $header->addTable();
        $table->addRow();

        // Kolom pertama untuk gambar
        $imageCell = $table->addCell(2000); // Sesuaikan ukuran lebar cell untuk gambar
        $imageCell->addImage('../public/assets/img/logo-wk.png', ['width' => 80, 'height' => 80]);

        // Kolom kedua untuk teks
        $textCell = $table->addCell();
        $textCell->addText('SMK WIKRAMA BOGOR', ['bold' => true, 'size' => 10]);
        $textCell->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', ['size' => 8]);
        $textCell->addText('Telp/Fax. (0251) 8242411', ['size' => 10]);
        $textCell->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smkwikrama.sch.id', ['size' => 8]);

        // Atur alignment untuk cell
        $imageCell->addTextRun()->addText('', null, ['alignment' => 'right']);
        $textCell->addTextRun()->addText('', null, ['alignment' => 'left']);

        // Adding the report title
        $section->addText('LAPORAN KEGIATAN HARIAN', 'headerStyle', 'centered');
        $section->addText('PESERTA PRAKTIK KERJA LAPANGAN (PKL) TAHUN 2024', 'headerStyle', 'centered');
        $section->addTextBreak(1);

        // Fetching data from the database
        $user = Auth::user(); // Assuming the user is logged in
        $todos = ToDoList::where('user_id', $user->id)->orderBy('date')->get();

        // Adding the details
        $section->addText('Nama Peserta Didik     : ' . $user->name, 'contentStyle', 'left');
        $section->addText('Industri Tempat PKL    : PT Mitra Global Informatika', 'contentStyle', 'left');
        $section->addText('Nama Instruktur / Pembimbing Industri : Pak Andhira', 'contentStyle', 'left');
        $section->addText('Nama Guru Pembimbing   : Pak Hendri', 'contentStyle', 'left');
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
        $table->addCell(500, $cellStyle)->addText('No.', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Hari/Tanggal', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Unit Kerja/Pekerjaan', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Catatan', 'headerRowStyle', 'centered');

        // Data rows
        foreach ($todos as $index => $todo) {
            $table->addRow();
            $table->addCell(500, $cellStyle)->addText($index + 1, 'contentStyle', 'centered');
            // Convert the date string to DateTime object before formatting
            $date = new \DateTime($todo->date);
            $table->addCell(2000, $cellStyle)->addText($date->format('d-m-Y'), 'contentStyle', 'centered');
            $table->addCell(4000, $cellStyle)->addText($todo->content, 'contentStyle', 'centered');
            $table->addCell(2000, $cellStyle)->addText($todo->pesan, 'contentStyle', 'centered');
        }

        $section->addTextBreak(1);
        // Adding instructor's signature part
        $section->addText('.......................................... 2024', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addText('Instruktur / Pembimbing Industri', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addTextBreak(3);
        $section->addText('(................................................)', array('bold' => true), array('alignment' => 'right', 'size' => '8'));

        // Save the file
        $fileName = 'assignment report-' . now()->format('d-m-Y') . '.docx';
        $filePath = storage_path('app/public/' . $fileName);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function createDocumentApi()
    {
        $phpWord = new PhpWord();

        // Setting font styles
        $phpWord->addFontStyle('headerStyle', ['bold' => true, 'size' => 12]);
        $phpWord->addFontStyle('headerRowStyle', ['bold' => true, 'size' => 10]);
        $phpWord->addFontStyle('contentStyle', ['size' => 10]);
        $phpWord->addFontStyle('smallContentStyle', ['size' => 8]);

        // Setting paragraph styles
        $phpWord->addParagraphStyle('centered', ['alignment' => 'center']);
        $phpWord->addParagraphStyle('left', ['alignment' => 'left']);

        // Adding a section
        $section = $phpWord->addSection();

        // Buat section untuk header
        $header = $section->addHeader();

        // Buat table untuk mengatur layout gambar dan teks
        $table = $header->addTable();
        $table->addRow();

        // Kolom pertama untuk gambar
        $imageCell = $table->addCell(2000); // Sesuaikan ukuran lebar cell untuk gambar
        $imageCell->addImage('../public/assets/img/logo-wk.png', ['width' => 80, 'height' => 80]);

        // Kolom kedua untuk teks
        $textCell = $table->addCell();
        $textCell->addText('SMK WIKRAMA BOGOR', ['bold' => true, 'size' => 10]);
        $textCell->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', ['size' => 8]);
        $textCell->addText('Telp/Fax. (0251) 8242411', ['size' => 10]);
        $textCell->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smkwikrama.sch.id', ['size' => 8]);

        // Atur alignment untuk cell
        $imageCell->addTextRun()->addText('', null, ['alignment' => 'right']);
        $textCell->addTextRun()->addText('', null, ['alignment' => 'left']);

        // Adding the report title
        $section->addText('LAPORAN KEGIATAN HARIAN', 'headerStyle', 'centered');
        $section->addText('PESERTA PRAKTIK KERJA LAPANGAN (PKL) TAHUN 2024', 'headerStyle', 'centered');
        $section->addTextBreak(1);

        // Fetching data from the database
        $user = Auth::user(); // Assuming the user is logged in
        $todos = ToDoList::where('user_id', $user->id)->orderBy('date')->get();

        // Adding the details
        $section->addText('Nama Peserta Didik     : ' . $user->name, 'contentStyle', 'left');
        $section->addText('Industri Tempat PKL    : PT Mitra Global Informatika', 'contentStyle', 'left');
        $section->addText('Nama Instruktur / Pembimbing Industri : Pak Andhira', 'contentStyle', 'left');
        $section->addText('Nama Guru Pembimbing   : Pak Hendri', 'contentStyle', 'left');
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
        $table->addCell(500, $cellStyle)->addText('No.', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Hari/Tanggal', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Unit Kerja/Pekerjaan', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Catatan', 'headerRowStyle', 'centered');

        // Data rows
        foreach ($todos as $index => $todo) {
            $table->addRow();
            $table->addCell(500, $cellStyle)->addText($index + 1, 'contentStyle', 'centered');
            // Convert the date string to DateTime object before formatting
            $date = new \DateTime($todo->date);
            $table->addCell(2000, $cellStyle)->addText($date->format('d-m-Y'), 'contentStyle', 'centered');
            $table->addCell(4000, $cellStyle)->addText($todo->content, 'contentStyle', 'centered');
            $table->addCell(2000, $cellStyle)->addText($todo->pesan, 'contentStyle', 'centered');
        }

        $section->addTextBreak(1);
        // Adding instructor's signature part
        $section->addText('.......................................... 2024', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addText('Instruktur / Pembimbing Industri', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addTextBreak(3);
        $section->addText('(................................................)', array('bold' => true), array('alignment' => 'right', 'size' => '8'));

        // Save the file to a temporary location
        $tempFilePath = sys_get_temp_dir() . '/' . 'assignment report-' . now()->format('Y-m-d_H-i-s') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFilePath);

        // Read the file content and encode it in base64
        $fileContent = file_get_contents($tempFilePath);
        $fileBase64 = base64_encode($fileContent);

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'To-Do List document created successfully.',
            'data' => [
                'fileName' => basename($tempFilePath),
                'fileContent' => $fileBase64,
            ],
        ]);
    }
}