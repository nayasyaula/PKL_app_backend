<?php

namespace App\Http\Controllers;

use App\Models\AttendanceModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $userId = Auth::id();
        $inTime = Carbon::parse($request->input('in'))->setTimezone('Asia/Jakarta');

        $status = $inTime->hour < 8 ? 'Masuk' : 'Telat';

        AttendanceModel::create([
            'user_id' => $userId,
            'in' => $inTime,
            'status' => $status,
        ]);

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
        $userId = Auth::id();
        $outTime = Carbon::parse($request->input('out'))->setTimezone('Asia/Jakarta');

        $status = $outTime->hour >= 16 && $outTime->minute >= 55 ? 'Keluar' : 'Izin';

        AttendanceModel::where('id', $id)->update([
            'user_id' => $userId,
            'out' => $outTime,
            'status' => $status,
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

    public function createDocument()
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
    $section->addText('LAPORAN KEHADIRAN SISWA PKL DI INSTANSI/PERUSAHAAN', 'headerStyle', 'centered');
    $section->addTextBreak(1);

    // Fetching data from the database
    $user = Auth::user();
    $attendance = $user->attendances()->orderBy('created_at', 'asc')->get();

    // Adding the details
    $section->addText('Nama Peserta Didik     : ' . $user->name, 'contentStyle', 'left');
    $section->addText('Industri Tempat PKL    : PT Mitra Global Informatika', 'contentStyle', 'left');
    $section->addText('Nama Instruktur/Pembimbing Industri : Pak Andhira', 'contentStyle', 'left');
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

    // Space for signature
    $section->addTextBreak(1);
    $section->addText('Instruktur/Pembimbing Industri', 'contentStyle', 'right');
    $section->addTextBreak(2);

    // Save the file
    $fileName = 'Laporan_PKL_' . now()->format('Y-m-d_H-i-s') . '.docx';
    $filePath = storage_path('app/public/' . $fileName);

    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
}
}