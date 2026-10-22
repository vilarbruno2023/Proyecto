USE proyecto;

-- Ejecutar una sola vez sobre una base existente.
ALTER TABLE documento MODIFY archivo VARCHAR(100) NOT NULL;
ALTER TABLE documento MODIFY estado VARCHAR(20) NOT NULL DEFAULT 'Revisar';

ALTER TABLE traslado
    MODIFY cedulaPaciente VARCHAR(20) NULL,
    ADD COLUMN nombrePaciente VARCHAR(120) NULL AFTER cedulaPaciente,
    ADD COLUMN zonaRoja TINYINT(1) NOT NULL DEFAULT 0 AFTER nombrePaciente;

-- Normalizar los estados antiguos a los estados nuevos.
UPDATE documento SET estado = 'Revisar' WHERE estado IN ('Activo', 'activo', 'Pendiente', 'pendiente');
UPDATE documento SET estado = 'Vigente' WHERE estado IN ('vigente', 'VIGENTE');

-- Las categorías siguen en la tabla categoria: las nuevas se agregan desde el panel.
