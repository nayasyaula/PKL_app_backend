<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class AttendanceModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
    
        // Ambil semua data absensi untuk user yang sedang login
        $attendance = AttendanceModel::where('user_id', $userId)
            ->get();
    
        return view('pages', compact('attendance'));
    }
    

    public function indexApi(Request $request)
{
    $userId = Auth::id();

    $attendance = AttendanceModel::where('user_id', $userId)
        ->get();

    return response()->json($attendance);
}


    public function markAttendance()
    {
        $userId = Auth::id();
        $todayAttendance = AttendanceModel::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$todayAttendance) {
            AttendanceModel::create([
                'user_id' => $userId,
                'in' => Carbon::now(),
                'in_status' => Carbon::now()->hour == 8 && Carbon::now()->minute == 0 ? 'Masuk' : 'Telat',
            ]);

            return redirect()->route('pages')->with('success', 'Attendance IN marked successfully.');
        }

        return redirect()->route('pages')->with('info', 'You have already marked your attendance for today.');
    }

    public function markAttendanceApi(Request $request)
{
    $userId = Auth::id();
    $now = Carbon::now()->setTimezone('Asia/Jakarta');
    $todayDate = $now->toDateString();
    $cutOffHour = 8;
    $cutOffMinute = 0;

    $attendance = AttendanceModel::where('user_id', $userId)
        ->whereDate('created_at', $todayDate)
        ->first();

    if (!$attendance) {
        $status = ($now->hour < $cutOffHour || ($now->hour == $cutOffHour && $now->minute <= $cutOffMinute)) ? 'Masuk' : 'Telat';

        AttendanceModel::create([
            'user_id' => $userId,
            'in' => $now->toDateTimeString(),
            'in_status' => $status,
        ]);

        Log::info("Attendance created for user $userId. Time: {$now->toDateTimeString()}, Status: $status");

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked as present successfully.',
            'status' => $status
        ]);
    } else {
        if (!$attendance->out) {
            $status = ($now->hour >= 16 && $now->minute >= 55) ? 'Keluar' : 'Izin';

            $attendance->update([
                'out' => $now->toDateTimeString(),
                'out_status' => $status,
            ]);

            Log::info("Attendance updated for user $userId. Time: {$now->toDateTimeString()}, Status: $status");

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked as out successfully.',
                'status' => $status
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already marked for today.'
            ]);
        }
    }
}


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $inTime = Carbon::parse($request->input('in'))->setTimezone('Asia/Jakarta');

        $status = $inTime->hour < 8 ? 'Masuk' : 'Telat';

        AttendanceModel::create([
            'user_id' => $userId,
            'in' => $inTime,
            'in_status' => $status,
        ]);

        return redirect()->route('pages');
    }

    public function show(AttendanceModel $attendanceModel)
    {
        //
    }

    public function edit(AttendanceModel $attendanceModel)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $outTime = Carbon::parse($request->input('out'))->setTimezone('Asia/Jakarta');

        $status = ($outTime->hour >= 16 && $outTime->minute >= 55) ? 'Keluar' : 'Izin';

        $attendance = AttendanceModel::where('id', $id)->first();

        if ($attendance && $attendance->user_id == $userId) {
            $attendance->update([
                'out' => $outTime,
                'out_status' => $status,
            ]);
        }

        return redirect()->route('pages');
    }

    public function destroy(AttendanceModel $attendanceModel)
    {
        //
    }    public function createDocumentWeb()
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

        // Buat table untuk header
        $tableHeader = $header->addTable();
        $tableHeader->addRow();

        // Kolom pertama untuk gambar
        $imageCell = $tableHeader->addCell(2000); // Sesuaikan ukuran lebar cell untuk gambar
        $imageCell->addImage('../public/assets/img/logo-wk.png', ['width' => 80, 'height' => 80]);

        // Kolom kedua untuk teks
        $textCell = $tableHeader->addCell();
        $textCell->addText('SMK WIKRAMA BOGOR', ['bold' => true, 'size' => 10]);
        $textCell->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', ['size' => 8]);
        $textCell->addText('Telp/Fax. (0251) 8242411', ['size' => 10]);
        $textCell->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smkwikrama.sch.id', ['size' => 8]);

        // Atur alignment untuk cell
        $imageCell->addTextRun()->addText('', null, ['alignment' => 'right']);
        $textCell->addTextRun()->addText('', null, ['alignment' => 'left']);

        // Adding the report title
        $section->addText('LAPORAN KEHADIRAN SISWA PKL DI INSTANSI / PERUSAHAAN', 'headerStyle', 'centered');
        $section->addTextBreak(1);

        // Fetching data from the database
        $user = Auth::user();
        $attendance = $user->attendances()->orderBy('created_at', 'asc')->get();

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
        $table->addCell(500, $cellStyle)->addText('Ke-', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Hari/Tanggal', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Datang', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Pulang', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Keterangan Tidak Hadir', 'headerRowStyle', 'centered');

        // Data rows
        foreach ($attendance as $index => $item) {
            $table->addRow();
            $table->addCell(500, $cellStyle)->addText($index + 1, 'contentStyle', 'centered');
            $date = $item->in ? date('d-m-Y', strtotime($item->in)) : '-';
            $table->addCell(2000, $cellStyle)->addText($date, 'contentStyle', 'centered');
            $table->addCell(4000, $cellStyle)->addText($item->in, 'contentStyle', 'centered');
            $table->addCell(2000, $cellStyle)->addText($item->out, 'contentStyle', 'centered');
            $table->addCell(2000, $cellStyle)->addText($item->status, 'contentStyle', 'centered');
        }

        $section->addTextBreak(1);
        // Adding instructor's signature part
        $section->addText('.......................................... 2024', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addText('Instruktur / Pembimbing Industri', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addTextBreak(3);
        $section->addText('(................................................)', array('bold' => true), array('alignment' => 'right', 'size' => '8'));


        // Save the file
        $fileName = 'attendance report-' . now()->format('d-m-Y') . '.docx';
        $filePath = storage_path('app/public/' . $fileName);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function createDocApi()
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

        // Buat table untuk header
        $tableHeader = $header->addTable();
        $tableHeader->addRow();

        // Kolom pertama untuk gambar
        $imageCell = $tableHeader->addCell(2000); // Sesuaikan ukuran lebar cell untuk gambar
        $imageCell->addImage('../public/assets/img/logo-wk.png', ['width' => 80, 'height' => 80]);

        // Kolom kedua untuk teks
        $textCell = $tableHeader->addCell();
        $textCell->addText('SMK WIKRAMA BOGOR', ['bold' => true, 'size' => 10]);
        $textCell->addText('Jl. Raya Wangun Kelurahan Sindangsari Kecamatan Bogor Timur', ['size' => 8]);
        $textCell->addText('Telp/Fax. (0251) 8242411', ['size' => 10]);
        $textCell->addText('Email: prohumasi@smkwikrama.sch.id, Website: http://www.smkwikrama.sch.id', ['size' => 8]);

        // Atur alignment untuk cell
        $imageCell->addTextRun()->addText('', null, ['alignment' => 'right']);
        $textCell->addTextRun()->addText('', null, ['alignment' => 'left']);

        // Adding the report title
        $section->addText('LAPORAN KEHADIRAN SISWA PKL DI INSTANSI / PERUSAHAAN', 'headerStyle', 'centered');
        $section->addTextBreak(1);

        // Fetching data from the database
        $user = Auth::user();
        $attendance = $user->attendances()->orderBy('created_at', 'asc')->get();

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
        $table->addCell(500, $cellStyle)->addText('Ke-', 'headerRowStyle', 'centered');
        $table->addCell(2000, $cellStyle)->addText('Hari/Tanggal', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Datang', 'headerRowStyle', 'centered');
        $table->addCell(4000, $cellStyle)->addText('Pulang', 'headerRowStyle', 'centered');
        // $table->addCell(2000, $cellStyle)->addText('Keterangan ', 'headerRowStyle', 'centered');

        // Data rows
        foreach ($attendance as $index => $item) {
            $table->addRow();
            $table->addCell(500, $cellStyle)->addText($index + 1, 'contentStyle', 'centered');
            $date = $item->in ? date('d-m-Y', strtotime($item->in)) : '-';
            $table->addCell(2000, $cellStyle)->addText($date, 'contentStyle', 'centered');
            $table->addCell(4000, $cellStyle)->addText($item->in, 'contentStyle', 'centered');
            $table->addCell(2000, $cellStyle)->addText($item->out, 'contentStyle', 'centered');
            // $table->addCell(2000, $cellStyle)->addText($item->status, 'contentStyle', 'centered');
        }

        $section->addTextBreak(1);
        // Adding instructor's signature part
        $section->addText('.......................................... 2024', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addText('Instruktur / Pembimbing Industri', array('bold' => true), array('alignment' => 'right', 'size' => '8'));
        $section->addTextBreak(3);
        $section->addText('(................................................)', array('bold' => true), array('alignment' => 'right', 'size' => '8'));


        // Save the file to a temporary location
        $tempFilePath = sys_get_temp_dir() . '/' . 'attendance report-' . now()->format('Y-m-d_H-i-s') . '.docx';
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
    public function process(Request $request)
    {
        $type = $request->query('type', 'create');
        $id = $request->query('id', null);

        if ($type === 'create') {
            $userId = Auth::id();
            $inTime = Carbon::parse($request->input('in'))->setTimezone('Asia/Jakarta');

            $status = $inTime->hour < 8 ? 'Masuk' : 'Telat';

            AttendanceModel::create([
                'user_id' => $userId,
                'in' => $inTime,
                'status' => $status,
            ]);

            return response()->json([
                'message' => 'Create process triggered',
                'data' => [
                    'user_id' => $userId,
                    'in' => $inTime->toDateTimeString(),
                    'status' => $status,
                ]
            ]);
        } elseif ($type === 'update' && $id) {
            $userId = Auth::id();
            $outTime = Carbon::parse($request->input('out'))->setTimezone('Asia/Jakarta');

            $status = $outTime->hour >= 16 && $outTime->minute >= 55 ? 'Keluar' : 'Izin';

            AttendanceModel::where('id', $id)->update([
                'user_id' => $userId,
                'out' => $outTime,
                'status' => $status,
            ]);

            return response()->json([
                'message' => 'Update process triggered',
                'data' => [
                    'id' => $id,
                    'user_id' => $userId,
                    'out' => $outTime->toDateTimeString(),
                    'status' => $status,
                ]
            ]);
        } else {
            return response()->json(['message' => 'Invalid request'], 400);
        }
    }
}
