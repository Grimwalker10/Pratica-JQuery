<?php
require_once "db.php";
session_start();


if ( isset($_POST['correo']) 
	&& isset($_POST['pass'])) {


    //validar
    if ( strlen($_POST['correo']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = 'datos incompletos';
        header("Location: login.php");
        return;
    }

    if ( strpos($_POST['correo'],'@') === false ) {
        $_SESSION['error'] = 'Correo no válido';
        header("Location: login.php");
        return;
    }
	//$salt="XyZzy12*_";
	
	//$check = hash('md5', $salt.$_POST['pass']);
	$check = $_POST['pass'];
    $stmt = $pdo->prepare('select id_usuario, nombre from usuario WHERE correo = :em AND contrasenia = :pw');
	
	$stmt->execute(array( ':em' => $_POST['correo'], ':pw' => $check));
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $row !== false ) {
         $_SESSION['nombre'] = $row['nombre'];
         $_SESSION['id_usuario'] = $row['id_usuario'];
		 $_SESSION['success']="Bienvenido ". $row['nombre'];;
         // Redirect the browser to index.php
         header("Location: index.php");
         return;
	}else{
		$_SESSION['error'] = 'usuario no encontrado, revisar usuario y/o contraseña';
	}
} 

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}
?>
<html>
<head>
<title>Ingreso al sistema</title>
<?php include 'head.php'; ?>
</head><body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Ingrese sus credenciales</h1>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                <label for="f_correo">Correo:</label>
                <input type="text" class="form-control" name="correo" id="f_correo" required>
                </div>

                <div class="form-group">
                <label for="f_pass">Contraseña:</label>
                <input type="password" class="form-control" name="pass" id="f_pass" required>
                </div>
                <hr>
                <div class="form-group text-center mt-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Ingresar</button>
                    <a class="btn btn-danger" href="index.php"><i class="fas fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function doValidate() {
         console.log('Validating...');
         try {
             pw = document.getElementById('f_pass').value;
			 em = document.getElementById('f_correo').value;
             console.log("Validating pw="+pw);
			 console.log("Validating em="+em);
             if (pw == null || pw == "") {
                 alert("Debe especificar ambos campos");
                 return false;
             }
             return true;
         } catch(e) {
             return false;
         }
         return false;
     }
</script>
</div>
</body>

