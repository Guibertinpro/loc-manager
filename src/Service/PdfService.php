<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
  private $domPdf;

  public function __construct()
  {
    $this->domPdf = new DomPdf();
  }

  public function showPdfFile($html, $id)
  {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $filename = 'contrat-reservation-n'. $id. '.pdf';
    $this->domPdf->stream($filename, ["Attachment" => false]);
  }
}