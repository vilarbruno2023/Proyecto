CREATE DATABASE proyecto;

USE proyecto;

CREATE TABLE funcionario(
	idUsuario int PRIMARY KEY AUTO_INCREMENT,
    nombreUsuario VARCHAR(50) NOT null,
    contraseña varchar(50) NOT null,
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
    archivo varchar(50) NOT null,
    codigoQR varchar(200) NOT null UNIQUE,
    
    FOREIGN KEY (idUsuario) REFERENCES funcionario(idUsuario),
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);

CREATE TABLE encuesta(
	idEncuesta int PRIMARY KEY AUTO_INCREMENT,
    idCategoria int NOT null,
    fechaCreacion varchar(20) NOT null,
    nombreEncuesta varchar(50) NOT null,
    
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);

CREATE TABLE pregunta(
	idPregunta int PRIMARY KEY AUTO_INCREMENT,
    textoPregunta varchar(300) NOT null
);

CREATE TABLE respuesta(
	idRespuesta int PRIMARY KEY AUTO_INCREMENT,
    idPregunta int NOT null,
    textoRespuesta varchar(300) NOT null,
    
	FOREIGN KEY (idPregunta) REFERENCES pregunta(idPregunta)
);

CREATE TABLE contiene(
	idPregunta int,
    idEncuesta int,
    idCategoria int,
    
    FOREIGN KEY (idPregunta) REFERENCES pregunta(idPregunta),
    FOREIGN KEY (idEncuesta) REFERENCES encuesta(idEncuesta),
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);