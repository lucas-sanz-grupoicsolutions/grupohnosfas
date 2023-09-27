<?php

declare(strict_types=1);


namespace App\Libraries;


class OdbcConnection
{
    protected $conection;

    public function __construct(string $mdbFile, string $dsn)
    {
        $dataSourceName = $this->getDriver($mdbFile, $dsn);
        $this->conection =  new \PDO($dataSourceName);
    }

    private function getDriver(string $mdbFile, string $dsn) :string
    {
        $uname = explode(" ",php_uname());
        $os = $uname[0];
        switch ($os){
            case 'Windows':
                return "odbc:$dsn";
                break;
            case 'Linux':
                return "odbc:Driver=MDBTools;DBQ=$mdbFile;";
                break;
            default:
                exit("Don't know about this OS");
        }
    }

    public function searchQuery(string $sql) :array
    {
        $prepare = $this->conection->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
