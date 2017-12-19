<?php
$errors = array();
if (isset($_POST['send'])) {
    if (empty($_POST['address'])) {
        $errors[] = 'Informar IP';
    }
    if (empty($_POST['database'])) {
        $errors[] = 'Informar database';
    }
    if (empty($_POST['user'])) {
        $errors[] = 'Informar usuário';
    }
    if (empty($_POST['password'])) {
        $errors[] = 'Informar senha';
    }
    if (empty($errors)) {
        set_time_limit(240);
        ini_set('display_errors', 1);
        ini_set('display_startup_erros', 1);
        error_reporting(E_ALL);
        $user = $_POST['user'];
        $address = $_POST['address'];
        $db = $_POST['database'];
        $pass = $_POST['password'];
        
        $con = mysqli_init();
        mysqli_options($con, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($con, $address, $user, $pass);                        

        $diretorio_arquivos = '/home/tom/Downloads/BASE SOC';

        function createDatabase($connection) {
            $apagar_banco = 'DROP DATABASE IF EXISTS soc;';
            if (mysqli_query($connection, $apagar_banco)) {
                echo 'Banco apagado com sucesso';
            }
            $command = 'CREATE DATABASE soc;';
            if (mysqli_query($connection, $command)) {
                echo 'Banco Criado com sucesso';
            }
        }

        function generateCreateTable($xmlName, $diretorio) {
            $xmlNameArray = explode('.', $xmlName);
            $sql = "create table soc.{$xmlNameArray[0]} (\n";
            $reader = new XMLReader;
            $reader->open($diretorio . '/' . $xmlName);
            while ($reader->read()) {
                if ($reader->name == "record") {
                    $node = new SimpleXMLElement($reader->readOuterXML());
                    $json = json_encode($node);
                    $array = json_decode($json, TRUE);
                    $sql .= implode(" TEXT,\n", array_keys($array));
                    break;
                }
            }
            return $sql . ' TEXT);';
        }

        function createDatabaseStructure($connection, $diretorio) {
            $xmls = listFiles($diretorio);
            foreach ($xmls as $xml) {
                $create = generateCreateTable($xml, $diretorio);
                mysqli_query($connection, $create);
            }
        }

        function populateDatabase($connection, $diretorio) {
            $xmls = listFiles($diretorio);
            foreach ($xmls as $xml) {
                $xmlNameArray = explode('.', $xml);
                $file = $diretorio . '/' . $xml;
                $command = "LOAD XML LOCAL INFILE '{$file}' INTO TABLE soc.{$xmlNameArray[0]} ROWS IDENTIFIED BY '<record>';";
                echo $command;
                if (mysqli_query($connection, $command)) {
                    echo "Tabela {$xmlNameArray[0]} importada com sucesso!<br/>";
                } else {
                    print_r(mysqli_error($connection));
                    echo "Falha ao importar tabela {$xmlNameArray[0]} !";
                }
            }
        }

        function listFiles($path) {
            $files = array_diff(scandir($path), array(".", ".."));
            return $files;
        }

        function ajustarTabelas($connection) {
            //$command = 'ALTER TABLE site_setor MODIFY ds_site_setor text;';
            //$command .= 'ALTER TABLE ficha MODIFY obs1 text;';
            //$command .= 'ALTER TABLE ficha MODIFY obs3 text;';
            //$command .= 'ALTER TABLE med_tecnica MODIFY descricao text;';
            mysqli_select_db($connection,"soc");
            $tabelas = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'soc'";
            $result_tab = mysqli_query($connection, $tabelas);
            while ($row = mysqli_fetch_array($result_tab,MYSQLI_ASSOC)){
                 $tabela = $row['table_name'];
                 $colunas = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tabela';";
                 $result_col = mysqli_query($connection, $colunas);
                 while ($row1 = mysqli_fetch_array($result_col,MYSQLI_ASSOC)){
                     $col = $row1['column_name'];
                     $tamanho = "SELECT max(length($col)) FROM soc.$tabela;";
                     $result_tam = mysqli_query($connection, $tamanho);
                     $size = mysqli_fetch_row($result_tam)[0];    
                     if (!$size){
                         $size = 50;
                     }
                     $command = "ALTER TABLE $tabela MODIFY COLUMN $col VARCHAR($size);";
                     echo $command;                     
                     mysqli_query($connection, $command);
                 }
            }
        }

        //createDatabase($con);
        //createDatabaseStructure($con, $diretorio_arquivos);        
        //populateDatabase($con, $diretorio_arquivos);
        //ajustarTabelas($con);


        /*
         * 
         *  SELECT max(length(usuace)) FROM soc.aceage;
          ALTER TABLE soc.aceage MODIFY COLUMN usuace VARCHAR(6);
         * SELECT table_name FROM information_schema.tables WHERE table_schema = 'soc';

          SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'aceage';
         */
    }
}
?>
<html>
    <head>
        <title>XmlToSql 1.0</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">           
<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
}
?>
            <form action="form.php" method="POST">
                <div class="form-group">
                    <label for="address">Endereço IP:</label>
                    <input type="text" class="form-control" name="address" placeholder="999.999.999.999"/>
                </div>               
                <div class="form-group">
                    <label for="database">Banco:</label>
                    <input type="text" class="form-control" name="database" id="database" />
                </div>   
                <div class="form-group">
                    <label for="user">Usuário:</label>
                    <input type="text" class="form-control" name="user" />
                </div>   
                <div class="form-group">
                    <label for="user">Senha:</label>
                    <input type="password" class="form-control" name="password" />
                </div>  
                <input name="send" type="submit" value="Importar" class="btn btn-success"/>
            </form>
        </div>        
    </body>
</html>

