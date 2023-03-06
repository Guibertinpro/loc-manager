<?php

namespace App\Service;

use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class PdfService extends AbstractController
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

  public function generateAndSavePdfContract($html, $id)
  {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $output = $this->domPdf->output();
    $filename = 'contrat-reservation-n'. $id. '.pdf';
    $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/pdfcontracts-temp-folder/' . $filename;
    file_put_contents($filePath, $output);
  }

  public function getPdfContract($id)
  {
    $filename = 'contrat-reservation-n'. $id. '.pdf';
    $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/pdfcontracts-temp-folder/' . $filename;

    return $filePath;
  }

  public function removePdfContract($id)
  {
    $filename = 'contrat-reservation-n'. $id. '.pdf';
    $filesystem = new Filesystem();
    $filesystem->remove($this->getParameter('kernel.project_dir').'/public/uploads/pdfcontracts-temp-folder/'.$filename);
  }

  public function imageToBase64($path) {
    $path = $path;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
  }
}