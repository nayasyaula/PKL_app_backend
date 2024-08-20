<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    // public function showPageWithQRCodeButton()
    // {
    //     $userId = auth()->user()->id; // Atau ID yang sesuai
    //     return view('qrcode', ['userId' => $userId]);
    // }
    public function showPageWithQRCodeButton()
    {
        return view('qrcode');
    }

    // public function showQRCode($userId)
    // {
    //     $url = route('attendance', ['userId' => $userId]);

    //     // Generate QR code for the given user ID
    //     $qrCode = QrCode::size(300)->generate($url);

    //     // Log the QR code and user ID
    //     Log::info('QR Code generated:', ['userId' => $userId, 'qrCode' => $qrCode]);

    //     // Return view with qrCode and userId
    //     return view('qrCode', [
    //         'qrCode' => $qrCode,
    //         'userId' => $userId
    //     ]);
    // }

    public function generateQRCode()
    {
        // URL yang digunakan untuk semua pengguna
        $url = route('mark-attendance'); // URL untuk menandai absensi

        // Generate QR Code
        $qrCode = QrCode::size(300)->generate($url);

        // Return view dengan QR Code
        return view('qr_code', ['qrCode' => $qrCode]);
    }

}
