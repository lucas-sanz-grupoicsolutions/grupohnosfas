<?php

namespace App\Libraries\Sepa;


use XMLWriter;
use DateInterval;
use App\Entities\Remesas;

use DateTime;
use DateTimeZone;

final class SepaPaymentInformation
{

    protected $customersModel;

    private array $bills;
    private String $bank;
    private $total;
    private $num_trans;
    private $bills_total;
    private $id_last_remesa_insert;
    protected $remesasModel;

    function __construct(array $bills, String $bank, $total, int $num_trans, $bills_total,$id_last_remesa_insert)
    {

        $this->bills =  $bills;
        $this->bank =  $bank;
        $this->customersModel = model('CustomersModel');
        $this->total =  $total;
        $this->num_trans =  $num_trans;
        $this->bills_total =  $bills_total;
        $this->id_last_remesa_insert =  $id_last_remesa_insert;
        $this->remesasModel = model('RemesasModel');

    }

    public function addToXml(XMLWriter $xmlWriter)
    {

        $num_bill = null;
        $array_effect = [];
        $recurrent_date = "";


        foreach ($this->bills as $b) {

                if ($b->recurrent_date === null) {
                    $recurrent_date = "00000000";
                }

                $remesas_effect =new Remesas();

                $effect = $this->id_last_remesa_insert . $b->num_bill;

                $remesas_effect->effect = $this->id_last_remesa_insert . $b->num_bill;
                $array_effect[] = $remesas_effect;
                $remesas_effect->id_remesas = $this->id_last_remesa_insert;


                if ($recurrent_date=== null || $recurrent_date === "00000000") {

                    // Calcular la diferencia en días entre la fecha seleccionada y la fecha actual
                    $current_date = new DateTime($recurrent_date);
                    // Establece la hora en 00:00:00
                    $current_date->setTime(0, 0, 0);

                    $current_date->setTimezone(new DateTimeZone('Europe/Madrid')); // Establece la zona horaria


                    $recurrent_date = new DateTime($recurrent_date);
                    // Establece la hora en 00:00:00
                    $recurrent_date->setTime(0, 0, 0);
                    $recurrent_date->setTimezone(new DateTimeZone('Europe/Madrid')); // Establece la zona horaria


                    // Calcula la diferencia entre las dos fechas
                    $diference = $current_date->diff($recurrent_date);

                    // Obtiene la cantidad de días desde la diferencia
                    $day_a_sum = $diference->days;

                    // Suma la cantidad de días a la fecha seleccionada
                    $current_date->add(new DateInterval('P' . $day_a_sum . 'D'));

                    // Formatea la fecha resultante
                    $recurrent_date = $current_date->format('Y-m-d');

                    $recurrent_date = str_replace("-", "", $recurrent_date);
                }  else {
                    // La fecha es válida, crea un objeto DateTime
                    $current_date = new DateTime($b->recurrent_date);
                }


                $num_bill =  $b->num_bill;

                $id_customer = $b->id_customer;

                $customer_iban = $b->customer_iban;
                $customer_bank = $b->customer_bank;
                $customer_office_bank = $b->customer_office_bank;
                $customer_digital_control = $b->customer_digital_control;
                $customer_bank_count = $b->customer_bank_count;

                $date_signing_mandate = $b->date_signing_mandate;

                $msgId = date("Ymd");
                $xmlWriter->startElement("PmtInf");

                $xmlWriter->writeElement("PmtInfId",  $msgId . "-RCUR" . $recurrent_date);
                $xmlWriter->writeElement("PmtMtd", "DD");
                $xmlWriter->writeElement("NbOfTxs",  $this->num_trans);
                $xmlWriter->writeElement("CtrlSum", $this->total);

                $this->PmtTpInf($xmlWriter);

                $xmlWriter->writeElement("ReqdColltnDt", $recurrent_date);

                $this->Cdtr($xmlWriter);

                $this->CdtrAcct($xmlWriter, $this->bank);

                $this->CdtrAgt($xmlWriter, $this->bank);

                $xmlWriter->writeElement("ChrgBr", "SLEV");

                $this->CdtrSchmeId($xmlWriter);

                $this->DrctDbtTxInf($xmlWriter, $b, $id_customer, $num_bill, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count, $recurrent_date, $date_signing_mandate,$effect);

                $xmlWriter->endElement(); // Cierra "PmtInf"

        }
        $json_numBills = json_encode($array_effect);
        $remesas_effect->effect = $json_numBills;
        $this->remesasModel->save($remesas_effect);
    }



    private function PmtTpInf(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("PmtTpInf");
        $this->SvcLvl($xmlWriter);
        $this->LclInstrm($xmlWriter);

        $xmlWriter->endElement();
    }


    private function SvcLvl(XMLWriter $xmlWriter): void
    {

        $xmlWriter->startElement("SvcLvl");
        $xmlWriter->writeElement("Cd", "SEPA");
        $xmlWriter->endElement();
    }

    private function LclInstrm(XMLWriter $xmlWriter): void
    {

        $xmlWriter->startElement("LclInstrm");
        $xmlWriter->writeElement("Cd", "CORE");
        $xmlWriter->endElement();
    }

    private function Cdtr(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("Cdtr");

        $xmlWriter->writeElement("Nm", "TOT PER CONSTRUIR 2012, S.L.");
        $this->PstlAdr($xmlWriter);


        $xmlWriter->endElement();
    }

    private function PstlAdr(XMLWriter $xmlWriter): void
    {

        $xmlWriter->startElement("PstlAdr");
        $xmlWriter->writeElement("Ctry", "ES");
        $xmlWriter->endElement();
    }

    private function CdtrAcct(XMLWriter $xmlWriter, $bank_selected): void
    {
        $xmlWriter->startElement("CdtrAcct");
        $this->Id($xmlWriter, $bank_selected);
        $xmlWriter->writeElement("Ccy", "EUR");

        $xmlWriter->endElement();
    }

    private function Id(XMLWriter $xmlWriter, $bank_selected): void
    {



        $iban_bank = "";

        switch ($bank_selected) {
            case "1":
                $iban_bank = "ES6500303301830001758271";
                break;
            case "2":
                $iban_bank = "ES3921001950780200211895";
                break;
            case "3":
                $iban_bank = "ES7131590026132701765824";
                break;
            case "4":
                $iban_bank = "ES3030587031382720007616";
                break;
            case "5":
                $iban_bank = "ES8401821072050201643134";
                break;
        }


        $xmlWriter->startElement("Id");
        $xmlWriter->writeElement("IBAN", $iban_bank);
        $xmlWriter->endElement();
    }

    private function CdtrAgt(XMLWriter $xmlWriter, $bank_selected): void
    {
        $xmlWriter->startElement("CdtrAgt");
        $this->FinInstnId($xmlWriter, $bank_selected);
        $xmlWriter->endElement();
    }

    private function FinInstnId(XMLWriter $xmlWriter, $bank_selected): void
    {

        $bic_bank = "";

        switch ($bank_selected) {
            case "1":
                $bic_bank = "BSCHESMMXXX";
                break;
            case "2":
                $bic_bank = "CAIXESBBXXX";
                break;
            case "3":
                $bic_bank = "BCOEESMM159";
                break;
            case "4":
                $bic_bank = "CCRIES2AXXX";
                break;
            case "5":
                $bic_bank = "BBVAESMM";
                break;
        }
        $xmlWriter->startElement("FinInstnId");
        $xmlWriter->writeElement("BIC", $bic_bank);
        $xmlWriter->endElement();
    }



    private function CdtrSchmeId(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("CdtrSchmeId");
        $this->CdtrSchmeId_ID($xmlWriter);

        $xmlWriter->endElement();
    }

    private function CdtrSchmeId_ID(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("Id");
        $this->PrvtId($xmlWriter);
        $xmlWriter->endElement();
    }

    private function PrvtId(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("PrvtId");
        $this->Othr($xmlWriter);
        $xmlWriter->endElement();
    }

    private function Othr(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("Othr");
        $xmlWriter->writeElement("Id", "ES07001B98472434");
        $this->SchmeNm($xmlWriter);
        $xmlWriter->endElement();
    }


    private function SchmeNm(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("SchmeNm");
        $xmlWriter->writeElement("Prtry", "SEPA");
        $xmlWriter->endElement();
    }

    private function DrctDbtTxInf(XMLWriter $xmlWriter, $b, $id_customer, $num_bill, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count, $recurrent_date, $date_signing_mandate,$effect): void
    {

        $xmlWriter->startElement("DrctDbtTxInf");
        $this->PmtId($xmlWriter, $recurrent_date,$effect);
        $this->InstdAmt($xmlWriter, $this->bills_total);
        $this->DrctDbtTx($xmlWriter, $id_customer, $date_signing_mandate);
        $this->DbtrAgt($xmlWriter, $b);

        $this->Dbtr($xmlWriter, $b);


        $this->Dbtr_PstlAdr($xmlWriter);
        $this->DbtrAcct($xmlWriter, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count);
        $this->RmtInf($xmlWriter, $num_bill);


        $xmlWriter->endElement();
    }

    private function PmtId(XMLWriter $xmlWriter, $recurrent_date,$effect): void
    {

        $xmlWriter->startElement("PmtId");
        $xmlWriter->writeElement("InstrId", $effect ."-" . $recurrent_date);
        $xmlWriter->writeElement("EndToEndId", $effect ."-" . $recurrent_date);
        $xmlWriter->endElement();
    }

    private function InstdAmt(XMLWriter $xmlWriter, $bills_total): void
    {
        $xmlWriter->startElement("InstdAmt");
        $xmlWriter->startAttribute("Ccy"); // Comienza el atributo "Ccy"
        $xmlWriter->text("EUR"); // Establece el valor del atributo "Ccy" a "EUR"
        $xmlWriter->endAttribute(); // Finaliza el atributo "Ccy"
        $xmlWriter->text($bills_total); // Agrega el contenido del elemento
        $xmlWriter->endElement(); // Finaliza el elemento "InstdAmt"

    }


    private function DrctDbtTx(XMLWriter $xmlWriter, $id_customer, $date_signing_mandate): void
    {
        $xmlWriter->startElement("DrctDbtTx");
        $this->MndtRltdInf($xmlWriter, $id_customer, $date_signing_mandate);
        $xmlWriter->endElement();
    }

    private function MndtRltdInf(XMLWriter $xmlWriter, $id_customer, $date_signing_mandate): void
    {


        $xmlWriter->startElement("MndtRltdInf");
        $xmlWriter->writeElement("MndtId", $id_customer);
        $xmlWriter->writeElement("DtOfSgntr", $date_signing_mandate);
        $xmlWriter->endElement();
    }

    private function DbtrAgt_FinInstnId(XMLWriter $xmlWriter, $b): void
    {

        $xmlWriter->startElement("FinInstnId");
        $xmlWriter->writeElement("BIC", $b->customer_bic);
        $xmlWriter->endElement();
    }


    private function DbtrAgt(XMLWriter $xmlWriter, $b): void
    {



        $xmlWriter->startElement("DbtrAgt");

        $this->DbtrAgt_FinInstnId($xmlWriter, $b);

        $xmlWriter->endElement();
    }

    private function Dbtr(XMLWriter $xmlWriter, $b): void
    {


        $xmlWriter->startElement("Dbtr");
        $xmlWriter->writeElement("Nm", $b->customer_name);

        $xmlWriter->endElement();
    }

    private function Dbtr_PstlAdr(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("PstlAdr");
        $this->Ctry($xmlWriter);
        $xmlWriter->endElement();
    }

    private function Ctry(XMLWriter $xmlWriter): void
    {
        $xmlWriter->startElement("Ctry");
        $xmlWriter->writeElement("Ctry", "ES");

        $xmlWriter->endElement();
    }



    private function DbtrAcct(XMLWriter $xmlWriter, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count): void
    {
        $xmlWriter->startElement("DbtrAcct");
        $this->DbtrAcct_ID($xmlWriter, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count);
        $xmlWriter->endElement();
    }

    private function DbtrAcct_ID(XMLWriter $xmlWriter, $customer_iban, $customer_bank, $customer_office_bank, $customer_digital_control, $customer_bank_count): void
    {



        $xmlWriter->startElement("Id");
        $xmlWriter->writeElement("IBAN",  $customer_iban . $customer_bank . $customer_office_bank . $customer_digital_control . $customer_bank_count);

        $xmlWriter->endElement();
    }


    private function RmtInf(XMLWriter $xmlWriter, $num_bill): void
    {


        $xmlWriter->startElement("RmtInf");
        $xmlWriter->writeElement("Ustrd", "Cobro Fra " . $num_bill . " - Cobro Fra." . $num_bill);
        $xmlWriter->endElement();
    }
}
