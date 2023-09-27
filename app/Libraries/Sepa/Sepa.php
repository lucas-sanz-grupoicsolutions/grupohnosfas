<?php
namespace App\Libraries\Sepa;

use App\Entities\Bills;
use App\Entities\Customers;
use XMLWriter;

use function PHPSTORM_META\type;
use CodeIgniter\Files\Files;
use Exception;

final class Sepa
{
    private XMLWriter $xmlWriter;
    private SepaHeader $sepaHeader;
    private SepaPaymentInformation $sepaPaymentInformation;


    public function __construct(SepaHeader $sepaHeader, SepaPaymentInformation $sepaPaymentInformation)
    {
        $this->sepaHeader = $sepaHeader;
        $this->sepaPaymentInformation = $sepaPaymentInformation;

        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);


    }

    public function create() : void {

        $this->xmlWriter->startDocument("1.0", "utf-8", "no");

        // Incluir el espacio de nombres
        $this->xmlWriter->startElement("Document");
        $this->xmlWriter->writeAttribute("xmlns", "urn:iso:std:iso:20022:tech:xsd:pain.008.001.02");

    // Iniciar el elemento principal
        $this->xmlWriter->startElement("CstmrDrctDbtInitn");

        $this->sepaHeader->addToXml($this->xmlWriter);
        $this->sepaPaymentInformation->addToXml($this->xmlWriter);

        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
    }

    public function saveFile(string $bank,string $id_last_remesa_insert) {

        try {

            $fechaActual = date("Ymd_His");
            $bank = (int)$bank;

            $bank_selected = "";

            switch ($bank) {
                case 1:
                    $bank_selected = "santander";
                    break;
                case 2:
                    $bank_selected = "lacaixa";
                    break;
                case 3:
                    $bank_selected = "caixapopular";
                    break;
                case 4:
                    $bank_selected = "cajamar";
                    break;
                case 5:
                    $bank_selected = "bbva";
                    break;
            }

            $filename = 'remesas/' . $id_last_remesa_insert. "-" . $bank_selected . "_" . $fechaActual. '_sepa.xml'; // Reemplaza con la ruta y el nombre de archivo deseado

            $xmlContent = $this->xmlWriter->outputMemory();

          //  $remesasPath = APPPATH . 'remesas/' . $filename;

            if (file_put_contents($filename, $xmlContent) === false) {
                throw new Exception('Error al guardar el archivo SEPA.');

            }else{
                file_put_contents($filename, $xmlContent);

            }



        } catch (Exception $e) {

            echo 'Error: ' . $e->getMessage();
            return redirect()->route('remesas');
        }


    }


    // public function output() {
    //   return $this->xmlWriter->outputMemory();
    // }


    // public function forceDownload($filename, $content, $params = [])
    // {

    //     try {
    //         $headers = [
    //             "Content-Type" => "application/xml",
    //             "Content-Disposition" => "attachment; filename=\"" . $filename . "\"",
    //             "Content-Transfer-Encoding" => "binary",
    //             "Content-Length" => mb_strlen($content),
    //             "Connection" => "close"
    //         ];

    //         foreach ($params as $k => $v) {
    //             $headers[$k] = $v;
    //         }

    //         foreach ($headers as $k => $v) {
    //             header(sprintf("%s: %s", $k, $v));
    //         }

    //         $file_path = $filename;
    //        // readfile($file_path);

    //             // Descarga el archivo
    //         if (readfile($file_path)) {
    //            // return redirect()->route('remesas');
    //             exit();

    //         }else{
    //            exit();
    //         }


    //     } catch (Exception $e) {
    //         echo 'Error: ' . $e->getMessage();
    //     }
    // }


}
