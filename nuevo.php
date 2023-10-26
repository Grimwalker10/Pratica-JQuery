<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("Ingresar al sistema");
}

if (isset($_POST['nombres']) && isset($_POST['apellidos']) && isset($_POST['correo']) && isset($_POST['aficiones'])) {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $aficiones = $_POST['aficiones'];

    // Validación de datos
    if (empty($nombres) || empty($apellidos) || empty($correo) || empty($aficiones)) {
        $_SESSION['error'] = 'Todos los campos son requeridos';
        header("Location: nuevo.php");
        return;
    }

    if (strpos($correo, '@') === false) {
        $_SESSION['error'] = 'Correo electrónico no válido';
        header("Location: nuevo.php");
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO alumno(id_usuario, nombres, apellidos, correo, aficiones) VALUES (:ide, :nom, :ape, :cor, :afi)');
    $stmt->execute(array(
        ':ide' => $_SESSION['id_usuario'],
        ':nom' => htmlentities($nombres),
        ':ape' => htmlentities($apellidos),
        ':cor' => htmlentities($correo),
        ':afi' => htmlentities($aficiones)
    ));

    $id_alumno = $pdo->lastInsertId();

    // Insertar cursos y curso si es nuevo
    for ($i = 1; $i <= 9; $i++) {
        $anio_key = 'cur_anio' . $i; // Nombre del campo de año
        $curso_key = 'cur_nombre' . $i; // Nombre del campo de curso

        if (!isset($_POST[$anio_key]) || !isset($_POST[$curso_key])) {
            continue;
        }

        $anio = $_POST[$anio_key];
        $curso = $_POST[$curso_key];

        if (empty($anio) || empty($curso) || !is_numeric($anio)) {
            $_SESSION['error'] = 'Datos de cursos no válidos';
            header("Location: nuevo.php");
            return;
        }

        $stmt = $pdo->prepare('SELECT id_curso FROM cursos WHERE nombre = :nom');
        $stmt->execute(array(':nom' => $curso));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            $id_curso = $row['id_curso'];
        } else {
            $stmt = $pdo->prepare('INSERT INTO cursos(nombre) VALUES (:nom)');
            $stmt->execute(array(':nom' => $curso));
            $id_curso = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO alumno_cursos(id_alumno, anio, id_curso) VALUES (:ide, :anio, :idc)');
        $stmt->execute(array(
            ':ide' => $id_alumno,
            ':anio' => $anio,
            ':idc' => $id_curso
        ));
    }

    $_SESSION['success'] = 'Registro agregado!!!';
    header('Location: index.php');
    return;
}

if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}
?>
<html>
<head>
<?php include 'head.php'; ?>	
<title>Ingreso de nuevo estudiante</title>
</head><body>
<div class="container">
    <h1 class="mt-4">Ingreso de nuevo estudiante</h1>
    <hr>
    <form method="post">
        <div class="form-group">
            <label for="nombres">Nombres:</label>
            <input type="text" name="nombres" id="fn" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" name="apellidos" id="ln" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo:</label>
            <input type="mail" name="correo" id="em" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="aficiones">Aficiones:</label>
            <textarea name="aficiones" rows="8" cols="80" id="afi" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <p>Cursos y/o certificaciones:</p>
            <button type="button" id="addCurso" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
        </div>
        <div id="curso_fields"></div>
        <hr>
        <div class="form-group">
            <input type="submit" value="Add" onclick="return doValidate();" class="btn btn-success">
            <a href="index.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
<script>
	function doValidate() {
         console.log('validar campos...');
         try {
             fn = document.getElementById('fn').value;
			 ln = document.getElementById('ln').value;
			 em = document.getElementById('em').value;
			 afi = document.getElementById('afi').value;
			 
             //console.log("validando pw="+pw);
			 console.log("validando em="+em);
             if (fn == null || fn == "" || 
				 ln ==null || ln == "" ||
				 em ==null || em == "" ||
				 afi ==null || afi == ""
				 ) {
                 alert("Todos los campos son requeridos");
                 return false;
             }
             return true;
         } catch(e) {
             return false;
         }
         return false;
     }
</script>
<script>
    let cuentaCur = 0;
    $(document).ready(function () {
        window.console && console.log("Document ready event");

        $('#addCurso').click(function () {
            if (cuentaCur >= 9) {
                alert("Número máximo de cursos ingresados");
                return;
            }
            cuentaCur++;
            window.console && console.log("Agregando curso| " + cuentaCur);
            $('#curso_fields').append(
                '<div class="form-group" id="curso' + cuentaCur + '"> \
                <hr> \
                <p>Year: <input type="text" name="cur_anio' + cuentaCur + '" value="" /> \
                <input type="button" class="btn btn-danger" value="-" onclick="$(\'#curso' + cuentaCur + '\').remove();return false;"></p> \
                <p>Curso: <input type="text" name="cur_nombre' + cuentaCur + '" class="cursos" value="" /> \
                </div>'
            );
            $('.cursos').autocomplete({
                source: "cursos.php"
            });
        });
    });
</script>



