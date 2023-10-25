<?php
require_once "db.php";
session_start();

// verificar que se envió parametro
if ( ! isset($_GET['id_alumno']) ) {
  $_SESSION['error'] = "Alumno no especificado";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM alumno where id_alumno = :idx");
$stmt->execute(array(":idx" => $_GET['id_alumno']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Alumno no encontrado';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['nombres']);
$ln = htmlentities($row['apellidos']);
$em = htmlentities($row['correo']);
$af = htmlentities($row['aficiones']);
$id = $row['id_alumno'];


?>
<html>
<head>
<title>Consulta de Alumno</title>
<?php include 'head.php'; ?>
</head><body>
<div class="container">
        <h1 class="mt-4">Consulta de Alumno</h1>
        <hr>
        <form class="mt-4">
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <p class="form-control" id="nombres"><?php echo($fn); ?></p>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <p class="form-control" id="apellidos"><?php echo($ln); ?></p>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <p class="form-control" id="correo"><?php echo($em); ?></p>
            </div>

            <div class="form-group">
                <label for="aficiones">Aficiones:</label>
                <p class="form-control" id="aficiones"><?php echo($af); ?></p>
            </div>

            <p>Cursos y certificaciones:</p>
            <ul>
<?php
	$stmt = $pdo->prepare("SELECT ac.anio, c.nombre FROM alumno_cursos ac inner join cursos c 
							on c.id_curso = ac.id_curso
							where ac.id_alumno = :idx");
	$stmt->execute(array(":idx" => $_GET['id_alumno']));
	$result = $stmt -> fetchAll();

	foreach( $result as $row ) {
		echo "<li>".$row['anio'] ;
		echo ": ";
		echo $row['nombre'];
		echo "</li>";
	}
?>
</ul>


<input type="hidden" id="id_alumno" name="id_alumno" value="<?php echo($id);?>">
<hr>
<a class="btn btn-primary" href="index.php"><i class="fas fa-arrow-left"></i> Regresar</a>
</form>
