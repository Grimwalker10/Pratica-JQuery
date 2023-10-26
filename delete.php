<?php
require_once "db.php";
session_start();
if ( !isset($_SESSION['id_usuario']) ) {
	die("Not logged in");
}
if ( isset($_POST['delete']) && isset($_POST['id_alumno']) ) {
    $sql = "DELETE FROM alumno WHERE id_alumno = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['id_alumno']));
    $_SESSION['success'] = 'Registro Eliminado!!';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that id_usuario is present
if ( ! isset($_GET['id_alumno']) ) {
$_SESSION['error'] = "no se especificó alumno a eliminar";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT id_alumno, nombres, apellidos, correo, aficiones FROM alumno where id_alumno = :idx");
$stmt->execute(array(":idx" => $_GET['id_alumno']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Alumno no encontrado';
    header( 'Location: index.php' ) ;
    return;
}
$fn = ($row['nombres'] .", " . $row['apellidos'] );
$em = ($row['correo']);
?>
<html>
<head>
<title>Eliminar alumno</title>
<?php include 'head.php'; ?>
</head><body>
<div class="container card shadow-lg border-0 rounded-lg mt-5">
    <h1 class="mt-4">Eliminar alumno</h1>
    <hr>
    <h3 class="mt-4">¿Desea eliminar al alumno: <?= ($row['nombres'] . ", " . $row['apellidos']) ?>?</h3>

    <form method="post">
        <div class="form-group">
            <label for="alumno">Alumno:</label>
            <input type="text" class="form-control" value="<?= $fn ?>" readonly>
        </div>
        <div class="form-group">
            <label for="correo">Correo:</label>
            <input type="text" class="form-control" value="<?= $em ?>" readonly>
        </div>
        <input type="hidden" name="id_alumno" value="<?= $row['id_alumno'] ?>">
        <hr>
        <button type="submit" class="btn btn-danger" name="delete"><i class="fas fa-trash"></i> Eliminar</button>
        <a href="index.php" class="btn btn-warning"><i class="fas fa-times"></i> Cancelar</a>
    </form>
</div>
