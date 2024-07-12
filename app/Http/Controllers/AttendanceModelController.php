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

        // Adding title
        $section->addText('LAPORAN KEHADIRAN SISWA PKL DI INSTANSI/PERUSAHAAN', array('bold' => true, 'size' => 14), array('alignment' => 'center'));
        $section->addTextBreak(1);
        
        // Adding student information
        $section->addText('Nama Peserta Didik: ', array('bold' => true));
        $section->addText('Industri Tempat PKL: ', array('bold' => true));
        $section->addText('Nama Instruktur/Pembimbing Industri: ', array('bold' => true));
        $section->addText('Nama Guru Pembimbing: ', array('bold' => true));

        // Add a line break
        $section->addTextBreak(1);

        // Adding table
        $tableStyle = array(
            'borderColor' => '000000',
            'borderSize' => 6,
            'cellMargin' => 50
        );
        $phpWord->addTableStyle('Attendance Table', $tableStyle);
        $table = $section->addTable('Attendance Table');

        // Adding table header row
        $table->addRow();
        $table->addCell(1000)->addText('Hari Ke', array('bold' => true));
        $table->addCell(2000)->addText('Hari/Tanggal', array('bold' => true));
        $table->addCell(2000)->addText('PARAF PEMBIMBING', array('bold' => true), array('gridSpan' => 2));
        $table->addCell(2000);
        $table->addCell(2000)->addText('Keterangan Tidak Hadir', array('bold' => true));
        $table->addRow();
        $table->addCell(1000);
        $table->addCell(2000);
        $table->addCell(1000)->addText('Datang', array('bold' => true));
        $table->addCell(1000)->addText('Pulang', array('bold' => true));
        $table->addCell(2000);

        // Adding table content rows
        for ($i = 1; $i <= 10; $i++) {
            $table->addRow();
            $table->addCell(1000)->addText($i);
            $table->addCell(2000)->addText('');
            $table->addCell(1000)->addText('');
            $table->addCell(1000)->addText('');
            $table->addCell(2000)->addText('');
        }

        $section->addTextBreak(1);

        // Adding instructor's signature part
        $section->addText('................................................ 2024', array('bold' => true), array('alignment' => 'right'));
        $section->addText('Instruktur/Pembimbing Industri', array('bold' => true), array('alignment' => 'right'));
        $section->addTextBreak(3);
        $section->addText('(................................................)', array('bold' => true), array('alignment' => 'right'));

        // Save the file
        $fileName = 'Laporan_PKL_' . now()->format('Y-m-d_H-i-s') . '.docx';
        $filePath = storage_path('app/public/' . $fileName);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);

    }
}
