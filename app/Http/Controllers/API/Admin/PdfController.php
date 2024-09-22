<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Queue;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class PdfController extends Controller
{
    public function download(string $id) {
        $queue = Queue::with([
            'user',
            'doctor',
            'appointment' => function ($query) {
                $query->withTrashed();
            },
        ])->withTrashed()->where('id', $id)->latest()->get();
        $pdf = Pdf::loadView('pdf.report', [
            'queue' => $queue
        ]);
        return $pdf->download('Reports PDF.pdf');
    }
}
