USE proyecto;

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
('paciente', 'Paciente de prueba', 'normal', 'Centro de salud', 'Hospital', 'Ruta de prueba', 1, 1, 2, 1, 'Pendiente');

INSERT INTO traslado
(tipoTraslado, descripcionElemento, prioridad, origen, destino, ruta, idSolicitante, idChofer, idVehiculo, estado)
VALUES
('elemento', 'Material de prueba', 'normal', 'Depósito', 'Centro de salud', 'Ruta de prueba', 1, 1, 2, 'Pendiente');
