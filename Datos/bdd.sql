CREATE DATABASE proyecto;

USE proyecto;

CREATE TABLE funcionario(
	idUsuario int PRIMARY KEY AUTO_INCREMENT,
    nombreUsuario VARCHAR(50) NOT null,
    contrasenia varchar(255) NOT null,
    rol varchar(20) NOT NULL DEFAULT 'usuario'
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
    archivo varchar(50) NOT null,
    estado varchar(20) NOT null DEFAULT 'Activo',
    
    FOREIGN KEY (idUsuario) REFERENCES funcionario(idUsuario),
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);

CREATE TABLE encuesta_respuesta(
    idEncuestaRespuesta INT PRIMARY KEY AUTO_INCREMENT,
    satisfaccion VARCHAR(30) NOT NULL,
    comentarios TEXT NULL,
    fechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);