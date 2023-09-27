<?php
namespace App\Libraries\Sepa;

use XMLWriter;

final class SepaHeader
{

    private $id_last_remesa_insert;
    private $numTrans;
    private $total;

    function __construct( $id_last_remesa_insert,$NumTrans,$total)
    {
        $this->id_last_remesa_insert = $id_last_remesa_insert;
        $this->numTrans = $NumTrans;
        $this->total = $total;

    }

    public function addToXml(XMLWriter $xmlWriter) {

        $date = date("Y-m-d\TH:i:s");
        $msgId = date("YmdHis");
        $numTrans = $this->numTrans;


        $MsgId_code = $msgId . $this->id_last_remesa_insert;

        if (strlen($msgId) + strlen($this->id_last_remesa_insert) <= 35) {
            $MsgId_code = $msgId . $this->id_last_remesa_insert;

        } else {
             // Si la longitud supera los 35 caracteres, recorta los caracteres adicionales
             $MsgId_code = substr($msgId, 0, 35 - strlen($this->id_last_remesa_insert)) . $this->id_last_remesa_insert;

        }


       $xmlWriter->startElement("GrpHdr");

           $xmlWriter->writeElement("MsgId", "RE" .  $MsgId_code);
           $xmlWriter->writeElement("CreDtTm", $date);
           $xmlWriter->writeElement("NbOfTxs",  $numTrans);
           $xmlWriter->writeElement("CtrlSum", $this->total);

        $this->initiatorPart($xmlWriter);
        $xmlWriter->endElement();
    }

    private function initiatorPart(XMLWriter $xmlWriter):void
    {
        $xmlWriter->startElement("InitgPty");
            $xmlWriter->writeElement("Nm", "TOT PER CONSTRUIR 2012, S.L.");
            $this->Id($xmlWriter);
        $xmlWriter->endElement();
    }


    private function Id(XMLWriter $xmlWriter):void
    {
        $xmlWriter->startElement("Id");
             $this->OrgId($xmlWriter);
        $xmlWriter->endElement();
    }

    private function OrgId(XMLWriter $xmlWriter):void
    {
        $xmlWriter->startElement("OrgId");
             $this->Othr($xmlWriter);
        $xmlWriter->endElement();
    }

    private function Othr(XMLWriter $xmlWriter):void
    {
        $country = "ES";
        $dig_control = "70";
        $cod_sepa = "001";
        $cif ="B97311831";

        $xmlWriter->startElement("Othr");
            $xmlWriter->writeElement("Id", $country . $dig_control . $cod_sepa . $cif);
        $xmlWriter->endElement();
    }



}
