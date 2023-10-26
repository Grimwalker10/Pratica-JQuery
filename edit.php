<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['id_usuario'])) {
    die("Ingresar al sistema");
}

if (isset($_POST['id_alumno'])) {
    $id_alumno = $_POST['id_alumno'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $aficiones = $_POST['aficiones'];

    // Validación de datos
    if (empty($nombres) || empty($apellidos) || empty($correo) || empty($aficiones)) {
        $_SESSION['error'] = 'Todos los campos son requeridos';
        header("Location: edit.php?id_alumno=$id_alumno");
        return;
    }

    if (strpos($correo, '@') === false) {
        $_SESSION['error'] = 'Correo electrónico no válido';
        header("Location: edit.php?id_alumno=$id_alumno");
        return;
    }

    $stmt = $pdo->prepare('UPDATE alumno SET nombres = :nom, apellidos = :ape, correo = :cor, aficiones = :afi WHERE id_alumno = :id');
    $stmt->execute(array(
        ':id' => $id_alumno,
        ':nom' => htmlentities($nombres),
        ':ape' => htmlentities($apellidos),
        ':cor' => htmlentities($correo),
        ':afi' => htmlentities($aficiones)
    ));

    // Eliminar cursos existentes para el estudiante
    $stmt = $pdo->prepare('DELETE FROM alumno_cursos WHERE id_alumno = :id');
    $stmt->execute(array(':id' => $id_alumno));

    // Insertar cursos actualizados
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
            header("Location: edit.php?id_alumno=$id_alumno");
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

    $_SESSION['success'] = 'Registro actualizado!!!';
    header("Location: index.php");
    return;
}

if (isset($_SESSION['error'])) {
    echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}

if (isset($_GET['id_alumno'])) {
    $id_alumno = $_GET['id_alumno'];
    $stmt = $pdo->prepare("SELECT id_alumno, nombres, apellidos, correo, aficiones FROM alumno WHERE id_alumno = :id");
    $stmt->execute(array(':id' => $id_alumno));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        // Obtiene los datos del estudiante y los muestra en el formulario
        $id_alumno = $row['id_alumno'];
        $nombres = $row['nombres'];
        $apellidos = $row['apellidos'];
        $correo = $row['correo'];
        $aficiones = $row['aficiones'];

        // Obtiene los cursos relacionados con el estudiante y los muestra en el formulario
        $stmt = $pdo->prepare("SELECT anio, c.nombre AS curso FROM alumno_cursos ac JOIN cursos c ON ac.id_curso = c.id_curso WHERE ac.id_alumno = :id");
        $stmt->execute(array(':id' => $id_alumno));
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<html>
<head>
<title>Editar estudiante</title>
<?php include 'head.php'; ?>
</head><body>
<div class="container">
    <h1 class="mt-4">Editar estudiante</h1>
    <hr>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p class="alert alert-danger">' . $_SESSION['error'] . "</p>\n";
        echo '<hr>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p class="alert alert-success">' . $_SESSION['success'] . "</p>\n";
        echo '<hr>';
        unset($_SESSION['success']);
    }
    ?>

    <form method="post">
        <input type="hidden" name="id_alumno" value="<?= $id_alumno ?>">
        <div class="form-group">
            <label for="nombres">Nombres:</label>
            <input type="text" name="nombres" class="form-control" value="<?= $nombres ?>" required>
        </div>
        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" name="apellidos" class="form-control" value="<?= $apellidos ?>" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo:</label>
            <input type="mail" name="correo" class="form-control" value="<?= $correo ?>" required>
        </div>
        <div class="form-group">
            <label for="aficiones">Aficiones:</label>
            <textarea name="aficiones" class="form-control" rows="8" cols="80"><?= $aficiones ?></textarea>
        </div>
        <div class="form-group">
            <p>Cursos y/o certificaciones:</p>
            <button type="button" id="addCurso" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
        </div>
        <div id="curso_fields">
            <?php
            $cuentaCur = 1;
            if (is_array($cursos) && count($cursos) > 0) {
                foreach ($cursos as $curso) {
                    $anio = $curso['anio'];
                    $nombre_curso = $curso['curso'];
                    echo '<div class="form-group" id="curso' . $cuentaCur . '">';
                    echo'<hr>';
                    echo '<p>Year: <input type="text" name="cur_anio' . $cuentaCur . '" value="' . $anio . '" /> ';
                    echo '<input type="button" class="btn btn-danger" value="-" onclick="$(\'#curso' . $cuentaCur . '\').remove();return false;"></p> ';
                    echo '<p>Curso: <input type="text" name="cur_nombre' . $cuentaCur . '" class="cursos" value="' . $nombre_curso . '" /> </div>';
                    $cuentaCur++;
                }
            }
            ?>
        </div>
        <hr>
        <div class="form-group">
            <input type="submit" value="Guardar" onclick="return doValidate();" class="btn btn-success">
            <a href="index.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>
<script>
    function doValidate() {
        console.log('Validando campos...');
        try {
            let nombres = document.querySelector('input[name="nombres"]').value;
            let apellidos = document.querySelector('input[name="apellidos"]').value;
            let correo = document.querySelector('input[name="correo"]').value;
            let aficiones = document.querySelector('textarea[name="aficiones"]').value;

            if (nombres === "" || apellidos === "" || correo === "" || aficiones === "") {
                alert("Todos los campos son requeridos");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }

    let cuentaCur = <?= $cuentaCur ?>; // Se establece el valor actual de cuentaCur
    $(document).ready(function () {
        window.console && console.log("Evento listo para el documento");

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
</body>
</html>
