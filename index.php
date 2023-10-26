<?php
require_once "db.php";
session_start();
?>
<html>
<head>
<title>Lista de estudiantes</title>
<?php include 'head.php'; ?>
</head><body>
<?php
        echo "<center>";
        echo "<h1>Diego Jose Lutin Miranda, Carnet: 5190-20-16218</h1>";
        echo "</center>";

        echo "<p>";
        echo "<strong>Archivo:</strong> {$_SERVER['PHP_SELF']}<br>";
        echo "<strong>Servidor:</strong> {$_SERVER['SERVER_NAME']}<br>";
        echo "<strong>Cliente IP:</strong> {$_SERVER['REMOTE_ADDR']}<br>";

        if (isset($_SERVER['REMOTE_HOST'])) {
            echo "<strong>Cliente Nombre:</strong> {$_SERVER['REMOTE_HOST']}<br>";
        } else {
            echo "<strong>Cliente Nombre:</strong> No disponible<br>";
        }

        echo "<strong>User Agent:</strong> {$_SERVER['HTTP_USER_AGENT']}<br>";
        echo "</p>";
        echo "<hr>"
    ?>
<div class="container">
<h1 class="mt-4">Lista de estudiantes</h1>

<?php
if (isset($_SESSION['id_usuario'])) {
    echo '<a class="btn btn-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>';
} else {
    echo '<a class="btn btn-success" href="login.php"><i class="fas fa-sign-in-alt"></i> Ingresar al sistema</a>';
}

if ( isset($_SESSION['error']) ) {
    echo '<hr>';
    echo '<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<hr>';
    echo '<p class="alert alert-success">' . $_SESSION['success'] . "</p>\n";
    unset($_SESSION['success']);
}
?>
<hr>
<div class="table-responsive">
<table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Aficiones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>

<?php
$stmt = $pdo->query("SELECT id_alumno, nombres, apellidos, correo, aficiones FROM alumno");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(($row['nombres']));
	echo "</td><td>";
    echo(($row['apellidos']));
    echo("</td><td>");
    echo(($row['correo']));
    echo("</td><td>");
    echo(($row['aficiones']));
    echo("</td><td>");

	echo '<a class="btn btn-info" href="ver.php?id_alumno=' . $row['id_alumno'] . '"><i class="fas fa-eye"></i> Ver</a> ';

	if ( isset($_SESSION['id_usuario']) ) {
        echo '<a class="btn btn-warning" href="edit.php?id_alumno=' . $row['id_alumno'] . '"><i class="fas fa-edit"></i> Editar</a> ';
		echo '<a class="btn btn-danger" href="delete.php?id_alumno=' . $row['id_alumno'] . '"><i class="fas fa-trash"></i> Eliminar</a>';
	}
	echo("</td></tr>\n");
}
?>
</tbody>
</table>
</div>
<?php
	if ( isset($_SESSION['id_usuario']) ) {
        echo '<hr>';
		echo '<a class="btn btn-success" href="nuevo.php"> <i class="fas fa-plus"></i> Agregar alumno</a>';
	}
?>
</div>
