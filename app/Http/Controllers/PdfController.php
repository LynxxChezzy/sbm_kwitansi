<?php

namespace App\Http\Controllers;

use App\Models\SaldoCustomer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function pdf($id)
    {
        // Retrieve SaldoCustomer data with the necessary relationships
        $saldoCustomer = SaldoCustomer::with([
            'customer',
            'statusFollowUp' // Assuming statusFollowUp is a relation
        ])->findOrFail($id);

        // Convert the logo image to Base64 for embedding it into the PDF
        $logoPath = public_path('img/Logo_SBM2.png');
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $logoMimeType = mime_content_type($logoPath); // e.g., "image/png"

        // Prepare data for the view
        $data = [
            'saldoCustomer' => $saldoCustomer,
            'logoBase64' => "data:$logoMimeType;base64,$logoBase64",
        ];

        // Clean the filename to remove invalid characters
        $filename = 'SaldoCustomer-' . preg_replace('/[\/\\\\]/', '-', $saldoCustomer->nomor) . '.pdf';

        // Render the PDF view
        $pdf = Pdf::loadView('pdf.cetak', $data);

        // Stream the PDF
        return $pdf->stream($filename);
    }
}
