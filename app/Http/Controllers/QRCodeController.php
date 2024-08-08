<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeController extends Controller
{
    public function generateQrCode(Request $request)
    {
        $baseUrl = route('qr.route'); // Gantilah dengan route backend Anda yang akan memproses create atau update
        $type = $request->query('type', 'create'); // default ke 'create' jika tidak ada parameter 'type'
        $id = $request->query('id', null);

        $url = $baseUrl . '?type=' . $type;
        if ($id) {
            $url .= '&id=' . $id;
        }

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->build();

        return response($result->getString(), 200)
            ->header('Content-Type', 'image/png');
    }
}