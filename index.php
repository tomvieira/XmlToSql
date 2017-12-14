<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        set_time_limit(240);
        ini_set('display_errors', 1);
        ini_set('display_startup_erros', 1);
        error_reporting(E_ALL);        
        $con = mysqli_init();
        mysqli_options($con, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($con,'localhost', 'root', 'crotalus');
        
        $diretorio_arquivos = '/home/tom/Downloads/BASE SOC/consulta';

        function createDatabase($connection) {            
            $apagar_banco = 'DROP DATABASE IF EXISTS soc;';            
            if (mysqli_query($connection, $apagar_banco)){
                echo 'Banco apagado com sucesso';
            }
            $command = 'CREATE DATABASE soc;';
            if (mysqli_query($connection, $command)){
                echo 'Banco Criado com sucesso';
            }
        }

        function generateCreateTable($xmlName,$diretorio) {
            $xmlNameArray = explode('.',$xmlName);
            $sql = "create table soc.{$xmlNameArray[0]} (\n";
            $reader = new XMLReader;            
            $reader->open($diretorio.'/'.$xmlName);
            while ($reader->read()) {
                if ($reader->name == "record") {
                    $node = new SimpleXMLElement($reader->readOuterXML());
                    $json = json_encode($node);
                    $array = json_decode($json, TRUE);
                    $sql .= implode(" varchar(300),\n", array_keys($array));
                    break;
                }
            }
            return $sql . ' varchar(300));';
        }
        
        function createDatabaseStructure($connection,$diretorio){
            $xmls = listFiles($diretorio);
            foreach ($xmls as $xml) {
                $create = generateCreateTable($xml, $diretorio);
                mysqli_query($connection, $create);
            }
        }
        
        function populateDatabase($connection,$diretorio){
            $xmls = listFiles($diretorio);            
            foreach ($xmls as $xml) {
                $xmlNameArray = explode('.',$xml);
                $file = $diretorio.'/'.$xml;
                $command = "LOAD XML LOCAL INFILE '{$file}' INTO TABLE soc.{$xmlNameArray[0]} ROWS IDENTIFIED BY '<record>';";
                echo $command;
                if (mysqli_query($connection, $command)){
                    echo "Tabela {$xmlNameArray[0]} importada com sucesso!<br/>";
                }else{
                    print_r(mysqli_error($connection));
                    echo "Falha ao importar tabela {$xmlNameArray[0]} !";
                }
            }
        }
        
        function listFiles($path){         
            $files = array_diff(scandir($path),array(".", "..") );            
            return $files;
        }
        
        function ajustarTabelas($connection){
            $command = 'ALTER TABLE site_setor MODIFY ds_site_setor text;';
            $command .= 'ALTER TABLE ficha MODIFY obs1 text;';
            $command .= 'ALTER TABLE ficha MODIFY obs3 text;';
            $command .= 'ALTER TABLE med_tecnica MODIFY descricao text;';
            mysqli_query($connection, $command);
        }
        
        createDatabase($con);
        createDatabaseStructure($con, $diretorio_arquivos);
        ajustarTabelas($con);
        populateDatabase($con, $diretorio_arquivos);
        
        
        /*
         * 
         *  SELECT max(length(usuace)) FROM soc.aceage;
            ALTER TABLE soc.aceage MODIFY COLUMN usuace VARCHAR(6);
         */
        
        
        ?>
    </body>
</html>
