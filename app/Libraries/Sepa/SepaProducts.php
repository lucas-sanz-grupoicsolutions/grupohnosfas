<?php
namespace App\Libraries\Sepa;

use App\Entities\Bills;
use XMLWriter;


final class SepaProducts
{
    private array $bills;

    function __construct(Bills ...$bills)
    {
        $this->bills =  $bills;
    }

    public function addToXml(XMLWriter $xmlWriter) {
        $xmlWriter->startElement("products");

        foreach ($this->bills as $product) {
            $xmlWriter->startElement("product");
            $xmlWriter->writeElement("id", $product->id_bills);
            $xmlWriter->endElement();
        }
    }
}
