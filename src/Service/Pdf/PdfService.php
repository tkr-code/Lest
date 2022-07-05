<?php 
namespace App\Service\Pdf;

use App\Entity\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfService extends AbstractController{
    private $domPdf;
    public function __construct()
    {
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'Arial');
        $pdfOption->setIsRemoteEnabled(true);

        $domPdf = new Dompdf($pdfOption);
        $context = stream_context_create([
            'ssl'=>[
                'verify_peer'=>false,
                'verify_peer_name'=>false,
                'allow_self_signed'=>true
                
            ]
        ]);
        $domPdf->setHttpContext($context);
        $this->domPdf = $domPdf;
    }
    public function loadPdf($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4','portrait');
        $this->domPdf->render();
    }
    
    public function orderToPdf(Order $order, $file = false){
        $html = $this->renderView('pdf/order.html.twig',['order'=>$order]);
        $this->loadPdf($html);
        $fichier = 'Facture n°'.$order->getNumber().'.pdf';
        $this->domPdf->stream($fichier,[
            'Attachment'=>true
        ]);
        if ($file) {
            file_put_contents($this->getParameter('order_pdf_directory').DIRECTORY_SEPARATOR.'Facture - N°'.$order->getNumber().' - Lest.pdf',$this->domPdf->output());
        }
    }
}