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
    if (empty($errors)){
        die();
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
            echo count($errors);
            print_r($errors);
            if (!empty($errors)) {
                echo count($errors);
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

