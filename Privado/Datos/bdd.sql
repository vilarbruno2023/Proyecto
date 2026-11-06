CREATE DATABASE proyecto;

USE proyecto;

CREATE TABLE funcionario(
	idUsuario int PRIMARY KEY AUTO_INCREMENT,
    nombreUsuario VARCHAR(50) NOT null UNIQUE,
    contrasenia varchar(255) NOT null,
    rol varchar(20) NOT NULL DEFAULT 'funcionario'
);

CREATE TABLE categoria(
	idCategoria int PRIMARY KEY AUTO_INCREMENT,
    nombreCategoria varchar(50) NOT null
);

CREATE TABLE documento(
	idDocumento int PRIMARY KEY AUTO_INCREMENT,
    idUsuario int NOT null,
    idCategoria int NOT null,
    titulo varchar(100) NOT null,
    resumen TEXT NULL,
    archivo varchar(100) NOT null,
    estado varchar(20) NOT null DEFAULT 'Revisar',
    
    FOREIGN KEY (idUsuario) REFERENCES funcionario(idUsuario),
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);

CREATE TABLE encuesta_respuesta(
    idEncuesta INT AUTO_INCREMENT PRIMARY KEY,
    fechaEncuesta DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    tipoEncuesta VARCHAR(30) NOT NULL,
    segmento VARCHAR(50) NOT NULL,
    satisfaccion TINYINT UNSIGNED NOT NULL,
    INDEX idx_encuesta_tipo_segmento_fecha (tipoEncuesta, segmento, fechaEncuesta),
    CONSTRAINT chk_encuesta_satisfaccion CHECK (satisfaccion BETWEEN 1 AND 5)
);

CREATE TABLE personal_traslado (
 idPersonal INT AUTO_INCREMENT PRIMARY KEY,
 nombre VARCHAR(100) NOT NULL,
 documento VARCHAR(20) NULL,
 funcion ENUM('doctor','chofer','enfermero','administrativo') NOT NULL,
 activo TINYINT(1) NOT NULL DEFAULT 1,
 INDEX idx_personal_funcion_activo (funcion, activo)
);

CREATE TABLE vehiculo (
 idVehiculo INT AUTO_INCREMENT PRIMARY KEY,
 codigo VARCHAR(30) NOT NULL UNIQUE,
 tipo ENUM('ambulancia','auto','utilitario','otro') NOT NULL,
 descripcion VARCHAR(150) NULL,
 capacidad INT NOT NULL DEFAULT 1,
 requiere_asistencia TINYINT(1) NOT NULL DEFAULT 0,
 activo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE traslado (
 idTraslado INT AUTO_INCREMENT PRIMARY KEY,
 cedulaPaciente VARCHAR(20) NULL,
 nombrePaciente VARCHAR(120) NULL,
 zonaRoja TINYINT(1) NOT NULL DEFAULT 0,
 pacienteReferencia VARCHAR(120) NULL,
 tipoTraslado ENUM('paciente','elemento') NOT NULL DEFAULT 'paciente',
 descripcionElemento VARCHAR(150) NULL,
 prioridad ENUM('normal','urgente','critica') NOT NULL DEFAULT 'normal',
 origen VARCHAR(255) NOT NULL,
 destino VARCHAR(255) NOT NULL,
 ruta VARCHAR(500) NOT NULL,
 observaciones TEXT NULL,
 idSolicitante INT NULL,
 idChofer INT NULL,
 idEnfermero INT NULL,
 idVehiculo INT NULL,
 estado ENUM('solicitado','programado','en_curso','demorado','completado','cancelado') NOT NULL DEFAULT 'solicitado',
 horaSolicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 horaSalida DATETIME NULL,
 horaEstimadaLlegada DATETIME NULL,
 horaLlegada DATETIME NULL,
 FOREIGN KEY (idSolicitante) REFERENCES funcionario(idUsuario),
 FOREIGN KEY (idChofer) REFERENCES personal_traslado(idPersonal),
 FOREIGN KEY (idEnfermero) REFERENCES personal_traslado(idPersonal),
 FOREIGN KEY (idVehiculo) REFERENCES vehiculo(idVehiculo),
 INDEX idx_traslado_estado_hora (estado, horaSolicitud)
);

INSERT INTO funcionario (nombreUsuario, contrasenia, rol) VALUES
('admin', '$2y$10$ZIiYpavxI34zoH.aF3tGWOJAfA0aWW2j1DwKM6e9mUMPK2JD53BhK', 'admin');

INSERT INTO funcionario (nombreUsuario, contrasenia, rol) VALUES
('documentacion_demo', '$2y$10$ZIiYpavxI34zoH.aF3tGWOJAfA0aWW2j1DwKM6e9mUMPK2JD53BhK', 'documentacion'),
('doctor_demo', '$2y$10$ZIiYpavxI34zoH.aF3tGWOJAfA0aWW2j1DwKM6e9mUMPK2JD53BhK', 'doctor'),
('operador_demo', '$2y$10$ZIiYpavxI34zoH.aF3tGWOJAfA0aWW2j1DwKM6e9mUMPK2JD53BhK', 'operador'),
('funcionario_demo', '$2y$10$ZIiYpavxI34zoH.aF3tGWOJAfA0aWW2j1DwKM6e9mUMPK2JD53BhK', 'funcionario');

INSERT INTO categoria (nombreCategoria) VALUES
('Salud Reproductiva'), ('Cardiología'), ('Nefrología'), ('Imagenología'), ('Prevención');

INSERT INTO encuesta_respuesta (tipoEncuesta, segmento, satisfaccion) VALUES
('trasplantado', 'trasplante', 5),
('trasplantado', 'seguimiento', 4),
('estandar', 'cardiologia', 4),
('estandar', 'prevencion', 3),
('fnr', 'fnr_trasplante', 5),
('fnr', 'fnr_otro', 4);

INSERT INTO personal_traslado (idPersonal, nombre, funcion) VALUES
(1, 'Chofer de prueba', 'chofer'),
(2, 'Enfermero de prueba', 'enfermero');

INSERT INTO vehiculo (idVehiculo, codigo, tipo, descripcion, capacidad) VALUES
(1, 'AMB-PRUEBA', 'ambulancia', 'Ambulancia de prueba', 4),
(2, 'UTIL-PRUEBA', 'utilitario', 'Utilitario de prueba', 2);

INSERT INTO traslado
(tipoTraslado, nombrePaciente, prioridad, origen, destino, ruta, idSolicitante, idChofer, idEnfermero, idVehiculo, estado)
VALUES
('paciente', 'Paciente de prueba', 'normal', 'Centro de salud', 'Hospital', 'Ruta de prueba', 1, 1, 2, 1, 'solicitado');

INSERT INTO traslado
(tipoTraslado, descripcionElemento, prioridad, origen, destino, ruta, idSolicitante, idChofer, idVehiculo, estado)
VALUES
('elemento', 'Material de prueba', 'normal', 'Depósito', 'Centro de salud', 'Ruta de prueba', 1, 1, 2, 'programado');
