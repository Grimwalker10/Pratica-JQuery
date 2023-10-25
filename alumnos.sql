-- Usar la base de datos ALUMNOS
USE ALUMNOS;

-- Crear la tabla USUARIO
CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(30),
    nombre VARCHAR(35),
    contrasenia VARCHAR(20)
);

-- Crear la tabla ALUMNO
CREATE TABLE ALUMNO (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(50),
    apellidos VARCHAR(50),
    correo VARCHAR(30),
    aficiones VARCHAR(100),
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
);

-- Crear la tabla CURSOS
CREATE TABLE CURSOS (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

-- Crear la tabla ALUMNO_CURSOS
CREATE TABLE ALUMNO_CURSOS (
    id_alumno INT,
    id_curso INT,
    anio INT,
    FOREIGN KEY (id_alumno) REFERENCES ALUMNO(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES CURSOS(id_curso) ON DELETE CASCADE
);


-- Inserciones en la tabla USUARIO
INSERT INTO USUARIO (correo, nombre, contrasenia) VALUES
    ('usuario1@gmail.com', 'usuario1', '123'),
    ('usuario2@gmail.com', 'usuario2', '456'),
    ('usuario3@gmail.com', 'usuario3', '789');

-- Inserciones en la tabla ALUMNO
INSERT INTO ALUMNO (nombres, apellidos, correo, aficiones, id_usuario) VALUES
    ('Juan', 'Pérez', 'juan@gmail.com', 'Deportes', 1),
    ('María', 'González', 'maria@gmail.com', 'Música', 2),
    ('Pedro', 'Rodríguez', 'pedro@gmail.com', 'Lectura', 3);

-- Inserciones en la tabla CURSOS
INSERT INTO CURSOS (nombre) VALUES
    ('Matemáticas'),
    ('Historia'),
    ('Ciencias'),
    ('Inglés');

-- Inserciones en la tabla ALUMNO_CURSOS
INSERT INTO ALUMNO_CURSOS (id_alumno, id_curso, anio) VALUES
    (1, 1, 2023),
    (1, 2, 2023),
    (2, 3, 2023),
    (3, 4, 2023);