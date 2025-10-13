<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function showForm()
    {
        return view('certificate.form');
    }

    public function generateCertificate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $name = $request->input('name');
        $requestedDate = $request->input('date');
        $originalImagePath = public_path('certificate_template.jpg'); // Ensure you upload the certificate image to public/

        $img = Image::make($originalImagePath);

        // Assuming the original name "Jacob Doris" is at a specific position
        $img->text($name, 400, 300, function ($font) {
            $font->file(public_path('fonts/arial.ttf')); // Use a font similar to the certificate
            $font->size(50);
            $font->color('#1e3a8a');
            $font->align('center');
            $font->valign('top');
        });

        // Replace the date "Aug. 4th, 2024" at its position
        $img->text($requestedDate, 400, 400, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(20);
            $font->color('#dc2626');
            $font->align('center');
            $font->valign('top');
        });

        $fileName = 'certificate_' . str_replace(' ', '_', strtolower($name)) . '_' . now()->timestamp . '.jpg';
        $img->save(storage_path('app/public/certificates/' . $fileName));

        return response()->download(storage_path('app/public/certificates/' . $fileName))->deleteFileAfterSend(true);
    }
}
