CREATE DATABASE GYM_ACTIVAFIT;
SELECT DATABASE();
USE gym_activafit;
SHOW TABLES;
SELECT * FROM migrations;

USE gym_activafit;
SHOW TABLES;

INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1);

INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1);

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL PRIMARY KEY,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

DROP TABLE sessions;

CREATE TABLE Membresia (
    id_Membresia CHAR(6) CHECK(LEFT(id_Membresia,4) = 'MEB-')NOT NULL,
    nombre_plan VARCHAR(50) NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    duracion_dias INT NOT NULL,
    descripcion TEXT NOT NULL
);

CREATE TABLE Cliente (
    id_Cliente CHAR(8) CHECK(LEFT(id_Cliente, 4) = 'CLI-') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    edad INT CHECK(edad >= 15) NOT NULL,
    numero VARCHAR(15) NOT NULL,
    id_Membresia char(6) NOT NULL,
    fecha_vencimiento DATE NOT NULL
);

CREATE TABLE Recepcionista (
    id_Recepcionista CHAR(6) CHECK(LEFT(id_Recepcionista, 4) = 'REP-') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    turno VARCHAR(20) CHECK(turno IN ('Matutino', 'Vespertino')) NOT NULL,
    telefono VARCHAR(15) NOT NULL
);

CREATE TABLE Administrador (
    id_Admin CHAR(6) CHECK(LEFT(id_Admin, 4) = 'ADM-') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    nivelPermisos VARCHAR(20) CHECK(nivelPermisos IN ('Bajo', 'Medio', 'Alto')) NOT NULL
);

CREATE TABLE Instructor (
    id_Instructor CHAR(8) CHECK(LEFT(id_Instructor, 4) = 'INS-') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    horarioLaboral VARCHAR(50) NOT NULL
);

CREATE TABLE Clase (
    id_Clase CHAR(8) CHECK(LEFT(id_Clase, 4) = 'CLS-') NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    cupo_maximo INT NOT NULL,
    horario TIME NOT NULL,
    dias_semana VARCHAR(30) NOT NULL,
    salon VARCHAR(20) NOT NULL,
    id_Instructor CHAR(8) NOT NULL 
);

CREATE TABLE Inscripcion_Clase (
    id_Reserva CHAR(8) CHECK(LEFT(id_Reserva, 4) = 'RSV-') NOT NULL,
    fecha_Reserva DATETIME NOT NULL,
    estado VARCHAR(20) NOT NULL,
    id_Cliente CHAR(8) NOT NULL,
    id_Clase CHAR(8) NOT NULL
);

CREATE TABLE Asesoria (
    id_Asesoria CHAR(8) CHECK(LEFT(id_Asesoria, 4) = 'ASR-') NOT NULL,
    fecha_hora DATETIME NOT NULL,
    estado VARCHAR(20) NOT NULL,
    observaciones TEXT NOT NULL,
    id_Cliente CHAR(8) NOT NULL,
    id_Instructor CHAR(8) NOT NULL
);

CREATE TABLE SolicitudCambio (
    idSolicitudCambio CHAR(8) CHECK(LEFT(idSolicitudCambio, 4) = 'SCM-') NOT NULL,
    motivo TEXT NOT NULL,
    fecha DATETIME NOT NULL,
    estado VARCHAR(20) NOT NULL,
    id_Clase CHAR(8) NOT NULL,
    id_Instructor CHAR(8) NOT NULL
);

CREATE TABLE Pago (
    id_Pago CHAR(8) CHECK(LEFT(id_Pago, 4) = 'PAG-') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_Pago DATETIME NOT NULL,
    concepto VARCHAR(100) NOT NULL,
    id_Cliente CHAR(8) NOT NULL,
    id_Recepcionista CHAR(6) NOT NULL
);

/* Creacion de PK */

ALTER TABLE Membresia ADD CONSTRAINT pk_membresia PRIMARY KEY (id_Membresia);
ALTER TABLE Cliente ADD CONSTRAINT pk_cliente PRIMARY KEY (id_Cliente);
ALTER TABLE Recepcionista ADD CONSTRAINT pk_recepcionista PRIMARY KEY (id_Recepcionista);
ALTER TABLE Administrador ADD CONSTRAINT pk_admin PRIMARY KEY (id_Admin);
ALTER TABLE Instructor ADD CONSTRAINT pk_instructor PRIMARY KEY (id_Instructor);
ALTER TABLE Clase ADD CONSTRAINT pk_clase PRIMARY KEY (id_Clase);
ALTER TABLE Inscripcion_Clase ADD CONSTRAINT pk_reserva PRIMARY KEY (id_Reserva);
ALTER TABLE Asesoria ADD CONSTRAINT pk_asesoria PRIMARY KEY (id_Asesoria);
ALTER TABLE SolicitudCambio ADD CONSTRAINT pk_SCambio PRIMARY KEY (idSolicitudCambio);
ALTER TABLE Pago ADD CONSTRAINT pk_pago PRIMARY KEY (id_Pago);



/* Creacion de FK */

ALTER TABLE Cliente ADD CONSTRAINT fk_clientMembresia FOREIGN KEY (id_Membresia) REFERENCES Membresia (id_Membresia);
ALTER TABLE Clase ADD CONSTRAINT fk_classInstructor FOREIGN KEY (id_Instructor) REFERENCES Instructor (id_Instructor);
ALTER TABLE Inscripcion_Clase ADD CONSTRAINT fk_insClassClase FOREIGN KEY (id_Clase) REFERENCES Clase (id_Clase);
ALTER TABLE Inscripcion_Clase ADD CONSTRAINT fk_insClassCliente FOREIGN KEY (id_Cliente) REFERENCES Cliente (id_Cliente);
ALTER TABLE Asesoria ADD CONSTRAINT fk_asesCliente FOREIGN KEY (id_Cliente) REFERENCES Cliente (id_Cliente);
ALTER TABLE Asesoria ADD CONSTRAINT fk_asesInstructor FOREIGN KEY (id_Instructor) REFERENCES Instructor (id_Instructor);
ALTER TABLE SolicitudCambio ADD CONSTRAINT fk_solChangeClase FOREIGN KEY (id_Clase) REFERENCES Clase (id_Clase);
ALTER TABLE SolicitudCambio ADD CONSTRAINT fk_solChangeInstructor FOREIGN KEY (id_Instructor) REFERENCES Instructor (id_Instructor);
ALTER TABLE Pago ADD CONSTRAINT fk_pagoCliente FOREIGN KEY (id_Cliente) REFERENCES Cliente (id_Cliente);
ALTER TABLE Pago ADD CONSTRAINT fk_pagoRecepcionista FOREIGN KEY (id_Recepcionista) REFERENCES Recepcionista (id_Recepcionista);



/* Creacion de Inserts */

INSERT INTO Membresia (id_Membresia, nombre_plan, costo, duracion_dias, descripcion) VALUES
('MEB-01', 'Pase Diario', 100.00, 1, 'Acceso completo al gimnasio y clases del día por una sola sesión.'),
('MEB-02', 'Plan Semanal', 350.00, 7, 'Acceso ilimitado a instalaciones y clases grupales durante 7 días.'),
('MEB-03', 'Mensualidad Básica', 650.00, 30, 'Acceso a aparatos de musculación y área de cardio por 30 días.'),
('MEB-04', 'Mensualidad Total', 900.00, 30, 'Acceso ilimitado a todas las instalaciones, aparatos y clases grupales (Zumba, Calistenia, etc.).'),
('MEB-05', 'Plan Trimestral', 1800.00, 90, 'Paquete de 3 meses con acceso total a clases y descuentos en asesorías.'),
('MEB-06', 'Plan Estudiante Mensual', 500.00, 30, 'Tarifa especial para estudiantes con credencial vigente, acceso total.'),
('MEB-07', 'Plan Matutino', 550.00, 30, 'Acceso exclusivo en horario matutino de 6:00 AM a 1:00 PM.'),
('MEB-08', 'Plan Pareja Mensual', 1600.00, 30, 'Membresía dual para entrenar en compañía con acceso total.'),
('MEB-09', 'Plan Semestral', 1950.00, 180, 'Acceso por 6 meses con tarifa preferencial y una asesoría nutricional incluida.'),
('MEB-10', 'Plan Fines de Semana', 400.00, 30, 'Acceso limitado exclusivamente a los días sábados y domingos.');

INSERT INTO Cliente (id_Cliente, nombre, edad, numero, id_Membresia, fecha_vencimiento) VALUES
('CLI-0001', 'Carlos Pérez', 22, '555-1234', 'MEB-01', '2026-11-06'),
('CLI-0002', 'Ana Gómez', 25, '555-2345', 'MEB-02', '2026-11-12'),
('CLI-0003', 'Luis Martínez', 30, '555-3456', 'MEB-03', '2026-12-05'),
('CLI-0004', 'Sofía Rodríguez', 19, '555-4567', 'MEB-04', '2026-12-05'),
('CLI-0005', 'Miguel Ángel López', 28, '555-5678', 'MEB-05', '2027-02-05'),
('CLI-0006', 'Valeria Torres', 20, '555-6789', 'MEB-06', '2026-12-05'),
('CLI-0007', 'Jorge Ramírez', 35, '555-7890', 'MEB-07', '2026-12-05'),
('CLI-0008', 'Carla Hernández', 27, '555-8901', 'MEB-08', '2026-12-05'),
('CLI-0009', 'Daniel Morales', 40, '555-9012', 'MEB-09', '2027-05-05'),
('CLI-0010', 'Elena Castro', 24, '555-0123', 'MEB-10', '2026-12-05'),
('CLI-0011', 'Fernando Ruiz', 21, '555-1111', 'MEB-03', '2026-12-10'),
('CLI-0012', 'Gabriela Vargas', 26, '555-2222', 'MEB-04', '2026-12-10'),
('CLI-0013', 'Héctor Morales', 32, '555-3333', 'MEB-01', '2026-11-07'),
('CLI-0014', 'Isabel Jiménez', 29, '555-4444', 'MEB-05', '2027-02-10'),
('CLI-0015', 'Javier Peña', 18, '555-5555', 'MEB-06', '2026-12-10'),
('CLI-0016', 'Karla Mendoza', 23, '555-6666', 'MEB-02', '2026-11-15'),
('CLI-0017', 'Manuel Salas', 45, '555-7777', 'MEB-09', '2027-05-10'),
('CLI-0018', 'Natalia Ríos', 31, '555-8888', 'MEB-04', '2026-12-15'),
('CLI-0019', 'Oscar Blanco', 22, '555-9999', 'MEB-07', '2026-12-15'),
('CLI-0020', 'Patricia Silva', 25, '555-0000', 'MEB-08', '2026-12-15');

INSERT INTO Recepcionista (id_Recepcionista, nombre, turno, telefono) VALUES
('REP-01', 'Claudia Sánchez', 'Matutino', '555-4321'),
('REP-02', 'Roberto Díaz', 'Vespertino', '555-8765'),
('REP-03', 'Lucía Fernández', 'Matutino', '555-9876');

INSERT INTO Administrador (id_Admin, nombre, correo, nivelPermisos) VALUES
('ADM-01', 'Ricardo Silva', 'ricardo.silva@activafit.com', 'Alto'),
('ADM-02', 'Andrea Morales', 'andrea.morales@activafit.com', 'Medio'),
('ADM-03', 'David Pineda', 'david.pineda@activafit.com', 'Bajo'),
('ADM-04', 'Mariana Garza', 'mariana.garza@activafit.com', 'Alto'),
('ADM-05', 'Esteban Ruiz', 'esteban.ruiz@activafit.com', 'Medio');

INSERT INTO Instructor (id_Instructor, nombre, especialidad, horarioLaboral) VALUES
('INS-0001', 'Gabriel Mendoza', 'Zumba', 'Lunes a Viernes 06:00-14:00'),
('INS-0002', 'Rodrigo Benítez', 'Zumba', 'Lunes a Viernes 14:00-22:00'),
('INS-0003', 'Valeria Cruz', 'Calistenia', 'Lunes a Viernes 07:00-15:00'),
('INS-0004', 'Martín Paredes', 'Calistenia', 'Martes a Sábado 13:00-21:00'),
('INS-0005', 'Adriana Flores', 'Crossfit', 'Lunes a Viernes 06:00-14:00'),
('INS-0006', 'Esteban Aguilar', 'Crossfit', 'Miércoles a Domingo 14:00-22:00'),
('INS-0007', 'Camila Domínguez', 'Pilates', 'Lunes a Viernes 08:00-16:00'),
('INS-0008', 'Sebastián Navarro', 'Pilates', 'Lunes a Viernes 14:00-22:00'),
('INS-0009', 'Lorena Castillo', 'Ciclismo indoor', 'Lunes a Viernes 06:30-14:30'),
('INS-0010', 'Hugo Peralta', 'Ciclismo indoor', 'Martes a Sábado 15:00-22:00'),
('INS-0011', 'Daniela Ibarra', 'Yoga', 'Lunes a Viernes 07:00-15:00'),
('INS-0012', 'Felipe Ríos', 'Yoga', 'Sábado a Domingo 08:00-16:00'),
('INS-0013', 'Beatriz Medina', 'Boxeo', 'Lunes a Viernes 06:00-14:00'),
('INS-0014', 'Gonzalo Vega', 'Boxeo', 'Lunes a Viernes 14:00-22:00'),
('INS-0015', 'Patricia Cordero', 'Spinning', 'Martes a Sábado 07:00-15:00'),
('INS-0016', 'Alejandro Marín', 'Spinning', 'Lunes a Viernes 15:00-22:00'),
('INS-0017', 'Natalia Fuentes', 'TRX', 'Lunes a Viernes 08:00-16:00'),
('INS-0018', 'Javier Beltrán', 'TRX', 'Miércoles a Domingo 13:00-21:00'),
('INS-0019', 'Rocío Santillán', 'Entrenamiento Funcional', 'Lunes a Viernes 06:00-14:00'),
('INS-0020', 'Emilio Mercado', 'Entrenamiento Funcional', 'Lunes a Viernes 14:00-22:00');

INSERT INTO Clase (id_Clase, nombre, cupo_maximo, horario, dias_semana, salon, id_Instructor) VALUES
('CLS-0001', 'Zumba Fit Matutino', 30, '08:00:00', 'Lunes a Viernes', 'Salón A', 'INS-0001'),
('CLS-0002', 'Zumba Night Express', 35, '18:00:00', 'Lunes a Viernes', 'Salón A', 'INS-0002'),
('CLS-0003', 'Calistenia Pro', 25, '09:00:00', 'Lunes a Viernes', 'Área Exterior', 'INS-0003'),
('CLS-0004', 'Calistenia Básica', 20, '16:00:00', 'Martes a Sábado', 'Área Exterior', 'INS-0004'),
('CLS-0005', 'Crossfit WOD', 40, '07:00:00', 'Lunes a Viernes', 'Box 1', 'INS-0005'),
('CLS-0006', 'Crossfit Extreme', 40, '19:00:00', 'Miércoles a Domingo', 'Box 1', 'INS-0006'),
('CLS-0007', 'Pilates Reformer', 20, '10:00:00', 'Lunes a Viernes', 'Salón B', 'INS-0007'),
('CLS-0008', 'Pilates Core', 25, '17:00:00', 'Lunes a Viernes', 'Salón B', 'INS-0008'),
('CLS-0009', 'Ciclismo indoor Power', 30, '07:30:00', 'Lunes a Viernes', 'Salón Ciclismo', 'INS-0009'),
('CLS-0010', 'Ciclismo indoor Endurance', 30, '18:30:00', 'Martes a Sábado', 'Salón Ciclismo', 'INS-0010'),
('CLS-0011', 'Yoga Sunrise', 25, '08:00:00', 'Lunes a Viernes', 'Salón B', 'INS-0011'),
('CLS-0012', 'Yoga Weekend', 20, '09:00:00', 'Sábado a Domingo', 'Salón B', 'INS-0012'),
('CLS-0013', 'Boxeo Sparring', 35, '11:00:00', 'Lunes a Viernes', 'Ring Central', 'INS-0013'),
('CLS-0014', 'Boxeo Táctico', 30, '15:00:00', 'Lunes a Viernes', 'Ring Central', 'INS-0014'),
('CLS-0015', 'Spinning Cardio', 30, '08:30:00', 'Martes a Sábado', 'Salón Ciclismo', 'INS-0015'),
('CLS-0016', 'Spinning Night', 35, '19:30:00', 'Lunes a Viernes', 'Salón Ciclismo', 'INS-0016'),
('CLS-0017', 'TRX Suspension', 20, '09:30:00', 'Lunes a Viernes', 'Salón C', 'INS-0017'),
('CLS-0018', 'TRX High Intensity', 25, '15:30:00', 'Miércoles a Domingo', 'Salón C', 'INS-0018'),
('CLS-0019', 'Entrenamiento Funcional AM', 40, '06:30:00', 'Lunes a Viernes', 'Zona Funcional', 'INS-0019'),
('CLS-0020', 'Entrenamiento Funcional PM', 40, '20:00:00', 'Lunes a Viernes', 'Zona Funcional', 'INS-0020'),
('CLS-0021', 'Zumba Gold', 30, '11:00:00', 'Lunes a Viernes', 'Salón A', 'INS-0001'),
('CLS-0022', 'Crossfit Open Box', 35, '11:30:00', 'Lunes a Viernes', 'Box 1', 'INS-0005'),
('CLS-0023', 'Pilates Restaurativo', 20, '12:00:00', 'Lunes a Viernes', 'Salón B', 'INS-0007'),
('CLS-0024', 'Ciclismo indoor HIIT', 30, '12:00:00', 'Lunes a Viernes', 'Salón Ciclismo', 'INS-0009'),
('CLS-0025', 'Entrenamiento Funcional Elite', 35, '10:00:00', 'Lunes a Viernes', 'Zona Funcional', 'INS-0019');

INSERT INTO Asesoria (id_Asesoria, fecha_hora, estado, observaciones, id_Cliente, id_Instructor) VALUES
('ASR-0001', '2026-11-09 10:00:00', 'Realizada', 'Evaluación de composición corporal y ajuste de rutina de hipertrofia.', 'CLI-0001', 'INS-0002'),
('ASR-0002', '2026-11-09 11:30:00', 'Pendiente', 'Asesoria técnica sobre progresión de dominadas y muscle-ups.', 'CLI-0003', 'INS-0003'),
('ASR-0003', '2026-11-10 09:00:00', 'Cancelada', 'El cliente canceló por motivos de salud. Reagendar para la próxima semana.', 'CLI-0005', 'INS-0005'),
('ASR-0004', '2026-11-10 15:00:00', 'Realizada', 'Diseño de plan de movilidad articular y flexibilidad lumbar.', 'CLI-0007', 'INS-0007'),
('ASR-0005', '2026-11-11 10:00:00', 'Pendiente', 'Evaluación de capacidad aeróbica y control de ritmo cardiaco.', 'CLI-0009', 'INS-0009'),
('ASR-0006', '2026-11-11 11:30:00', 'Realizada', 'Ajuste de postura para prevención de lesiones en zona lumbar.', 'CLI-0011', 'INS-0011'),
('ASR-0007', '2026-11-12 09:30:00', 'Cancelada', 'Instructor con permiso médico. Se notificó al cliente para cambio de fecha.', 'CLI-0013', 'INS-0013'),
('ASR-0008', '2026-11-12 11:00:00', 'Realizada', 'Revisión de técnica de suspensión TRX para fortalecimiento de core.', 'CLI-0017', 'INS-0017'),
('ASR-0009', '2026-11-13 08:30:00', 'Pendiente', 'Planificación de acondicionamiento físico general para metabólico.', 'CLI-0019', 'INS-0019'),
('ASR-0010', '2026-11-13 16:00:00', 'Realizada', 'Evaluación de resistencia física y técnica de golpeo básico.', 'CLI-0004', 'INS-0014');

INSERT INTO Inscripcion_Clase (id_Reserva, fecha_Reserva, estado, id_Cliente, id_Clase) VALUES
-- CLS-0001 (Cupo: 30)
('RSV-0001', '2026-11-04 07:30:00', 'Asistio', 'CLI-0001', 'CLS-0001'),
('RSV-0002', '2026-11-05 07:45:00', 'Pendiente', 'CLI-0002', 'CLS-0001'),

-- CLS-0002 (Cupo: 35)
('RSV-0003', '2026-11-04 15:00:00', 'Asistio', 'CLI-0003', 'CLS-0002'),
('RSV-0004', '2026-11-05 17:00:00', 'Cancelada', 'CLI-0004', 'CLS-0002'),

-- CLS-0003 (Cupo: 25)
('RSV-0005', '2026-11-03 10:00:00', 'Asistio', 'CLI-0005', 'CLS-0003'),
('RSV-0006', '2026-11-05 08:30:00', 'Cambio', 'CLI-0006', 'CLS-0003'),

-- CLS-0004 (CLASE LLENA - Cupo: 20)
('RSV-0007', '2026-11-01 10:00:00', 'Asistio', 'CLI-0001', 'CLS-0004'),
('RSV-0008', '2026-11-01 11:00:00', 'Asistio', 'CLI-0002', 'CLS-0004'),
('RSV-0009', '2026-11-02 09:00:00', 'Asistio', 'CLI-0003', 'CLS-0004'),
('RSV-0010', '2026-11-02 12:00:00', 'Asistio', 'CLI-0004', 'CLS-0004'),
('RSV-0011', '2026-11-02 15:00:00', 'No asistio', 'CLI-0005', 'CLS-0004'),
('RSV-0012', '2026-11-03 08:00:00', 'Asistio', 'CLI-0006', 'CLS-0004'),
('RSV-0013', '2026-11-03 10:30:00', 'Asistio', 'CLI-0007', 'CLS-0004'),
('RSV-0014', '2026-11-03 14:00:00', 'Asistio', 'CLI-0008', 'CLS-0004'),
('RSV-0015', '2026-11-03 16:00:00', 'Pendiente', 'CLI-0009', 'CLS-0004'),
('RSV-0016', '2026-11-04 09:00:00', 'Asistio', 'CLI-0010', 'CLS-0004'),
('RSV-0017', '2026-11-04 11:00:00', 'Asistio', 'CLI-0011', 'CLS-0004'),
('RSV-0018', '2026-11-04 13:00:00', 'Cambio', 'CLI-0012', 'CLS-0004'),
('RSV-0019', '2026-11-04 15:00:00', 'Asistio', 'CLI-0013', 'CLS-0004'),
('RSV-0020', '2026-11-04 18:00:00', 'Asistio', 'CLI-0014', 'CLS-0004'),
('RSV-0021', '2026-11-05 07:00:00', 'Asistio', 'CLI-0015', 'CLS-0004'),
('RSV-0022', '2026-11-05 08:00:00', 'Asistio', 'CLI-0016', 'CLS-0004'),
('RSV-0023', '2026-11-05 09:30:00', 'Asistio', 'CLI-0017', 'CLS-0004'),
('RSV-0024', '2026-11-05 10:00:00', 'Asistio', 'CLI-0018', 'CLS-0004'),
('RSV-0025', '2026-11-05 11:00:00', 'Asistio', 'CLI-0019', 'CLS-0004'),
('RSV-0026', '2026-11-05 12:00:00', 'Pendiente', 'CLI-0020', 'CLS-0004'),

-- CLS-0005 (Cupo: 40)
('RSV-0027', '2026-11-04 06:00:00', 'Asistio', 'CLI-0007', 'CLS-0005'),
('RSV-0028', '2026-11-05 06:30:00', 'Pendiente', 'CLI-0008', 'CLS-0005'),

-- CLS-0006 (Cupo: 40)
('RSV-0029', '2026-11-04 12:00:00', 'Asistio', 'CLI-0009', 'CLS-0006'),

-- CLS-0007 (CLASE LLENA - Cupo: 20)
('RSV-0030', '2026-11-01 08:00:00', 'Asistio', 'CLI-0001', 'CLS-0007'),
('RSV-0031', '2026-11-01 09:00:00', 'Asistio', 'CLI-0002', 'CLS-0007'),
('RSV-0032', '2026-11-01 10:00:00', 'Asistio', 'CLI-0003', 'CLS-0007'),
('RSV-0033', '2026-11-02 08:00:00', 'Asistio', 'CLI-0004', 'CLS-0007'),
('RSV-0034', '2026-11-02 10:00:00', 'Asistio', 'CLI-0005', 'CLS-0007'),
('RSV-0035', '2026-11-02 14:00:00', 'No asistio', 'CLI-0006', 'CLS-0007'),
('RSV-0036', '2026-11-03 07:00:00', 'Asistio', 'CLI-0007', 'CLS-0007'),
('RSV-0037', '2026-11-03 09:00:00', 'Asistio', 'CLI-0008', 'CLS-0007'),
('RSV-0038', '2026-11-03 11:00:00', 'Asistio', 'CLI-0009', 'CLS-0007'),
('RSV-0039', '2026-11-03 15:00:00', 'Asistio', 'CLI-0010', 'CLS-0007'),
('RSV-0040', '2026-11-04 08:00:00', 'Asistio', 'CLI-0011', 'CLS-0007'),
('RSV-0041', '2026-11-04 09:30:00', 'Asistio', 'CLI-0012', 'CLS-0007'),
('RSV-0042', '2026-11-04 11:00:00', 'Cancelada', 'CLI-0013', 'CLS-0007'),
('RSV-0043', '2026-11-04 14:00:00', 'Asistio', 'CLI-0014', 'CLS-0007'),
('RSV-0044', '2026-11-04 16:00:00', 'Asistio', 'CLI-0015', 'CLS-0007'),
('RSV-0045', '2026-11-05 07:00:00', 'Asistio', 'CLI-0016', 'CLS-0007'),
('RSV-0046', '2026-11-05 08:00:00', 'Asistio', 'CLI-0017', 'CLS-0007'),
('RSV-0047', '2026-11-05 09:00:00', 'Asistio', 'CLI-0018', 'CLS-0007'),
('RSV-0048', '2026-11-05 09:30:00', 'Pendiente', 'CLI-0019', 'CLS-0007'),
('RSV-0049', '2026-11-05 10:00:00', 'Pendiente', 'CLI-0020', 'CLS-0007'),

-- CLS-0008 (Cupo: 25)
('RSV-0050', '2026-11-04 12:00:00', 'Asistio', 'CLI-0010', 'CLS-0008'),
('RSV-0051', '2026-11-05 15:00:00', 'Pendiente', 'CLI-0011', 'CLS-0008'),

-- CLS-0009 (Cupo: 30)
('RSV-0052', '2026-11-04 06:00:00', 'Asistio', 'CLI-0012', 'CLS-0009'),

-- CLS-0010 (Cupo: 30)
('RSV-0053', '2026-11-04 14:00:00', 'Asistio', 'CLI-0013', 'CLS-0010'),

-- CLS-0011 (Cupo: 25)
('RSV-0054', '2026-11-04 07:00:00', 'Asistio', 'CLI-0014', 'CLS-0011'),

-- CLS-0012 (CLASE LLENA - Cupo: 20)
('RSV-0055', '2026-11-01 10:00:00', 'Asistio', 'CLI-0001', 'CLS-0012'),
('RSV-0056', '2026-11-01 12:00:00', 'Asistio', 'CLI-0002', 'CLS-0012'),
('RSV-0057', '2026-11-02 09:00:00', 'Asistio', 'CLI-0003', 'CLS-0012'),
('RSV-0058', '2026-11-02 11:00:00', 'Asistio', 'CLI-0004', 'CLS-0012'),
('RSV-0059', '2026-11-02 15:00:00', 'Asistio', 'CLI-0005', 'CLS-0012'),
('RSV-0060', '2026-11-03 08:00:00', 'Asistio', 'CLI-0006', 'CLS-0012'),
('RSV-0061', '2026-11-03 10:00:00', 'Asistio', 'CLI-0007', 'CLS-0012'),
('RSV-0062', '2026-11-03 13:00:00', 'Asistio', 'CLI-0008', 'CLS-0012'),
('RSV-0063', '2026-11-03 16:00:00', 'Asistio', 'CLI-0009', 'CLS-0012'),
('RSV-0064', '2026-11-04 07:00:00', 'Asistio', 'CLI-0010', 'CLS-0012'),
('RSV-0065', '2026-11-04 09:00:00', 'Asistio', 'CLI-0011', 'CLS-0012'),
('RSV-0066', '2026-11-04 11:00:00', 'No asistio', 'CLI-0012', 'CLS-0012'),
('RSV-0067', '2026-11-04 14:00:00', 'Asistio', 'CLI-0013', 'CLS-0012'),
('RSV-0068', '2026-11-04 17:00:00', 'Asistio', 'CLI-0014', 'CLS-0012'),
('RSV-0069', '2026-11-05 06:00:00', 'Asistio', 'CLI-0015', 'CLS-0012'),
('RSV-0070', '2026-11-05 07:30:00', 'Asistio', 'CLI-0016', 'CLS-0012'),
('RSV-0071', '2026-11-05 08:00:00', 'Asistio', 'CLI-0017', 'CLS-0012'),
('RSV-0072', '2026-11-05 08:30:00', 'Pendiente', 'CLI-0018', 'CLS-0012'),
('RSV-0073', '2026-11-05 08:45:00', 'Pendiente', 'CLI-0019', 'CLS-0012'),
('RSV-0074', '2026-11-05 08:50:00', 'Pendiente', 'CLI-0020', 'CLS-0012'),

-- CLS-0013 (Cupo: 35)
('RSV-0075', '2026-11-04 10:00:00', 'Asistio', 'CLI-0015', 'CLS-0013'),

-- CLS-0014 (Cupo: 30)
('RSV-0076', '2026-11-04 13:00:00', 'Asistio', 'CLI-0016', 'CLS-0014'),

-- CLS-0015 (Cupo: 30)
('RSV-0077', '2026-11-04 07:00:00', 'Asistio', 'CLI-0017', 'CLS-0015'),

-- CLS-0016 (Cupo: 35)
('RSV-0078', '2026-11-04 14:00:00', 'Asistio', 'CLI-0018', 'CLS-0016'),

-- CLS-0017 (Cupo: 20)
('RSV-0079', '2026-11-04 08:00:00', 'Asistio', 'CLI-0019', 'CLS-0017'),

-- CLS-0018 (Cupo: 25)
('RSV-0080', '2026-11-04 11:00:00', 'Asistio', 'CLI-0020', 'CLS-0018'),

-- CLS-0019 (Cupo: 40)
('RSV-0081', '2026-11-04 05:30:00', 'Asistio', 'CLI-0001', 'CLS-0019'),

-- CLS-0020 (Cupo: 40)
('RSV-0082', '2026-11-04 18:00:00', 'Asistio', 'CLI-0002', 'CLS-0020'),

-- CLS-0021 (Cupo: 30)
('RSV-0083', '2026-11-04 09:00:00', 'Asistio', 'CLI-0003', 'CLS-0021'),

-- CLS-0022 (Cupo: 35)
('RSV-0084', '2026-11-04 10:00:00', 'Asistio', 'CLI-0004', 'CLS-0022'),

-- CLS-0023 (Cupo: 20)
('RSV-0085', '2026-11-04 11:00:00', 'Asistio', 'CLI-0005', 'CLS-0023'),

-- CLS-0024 (Cupo: 30)
('RSV-0086', '2026-11-04 11:30:00', 'Asistio', 'CLI-0006', 'CLS-0024'),

-- CLS-0025 (Cupo: 35)
('RSV-0087', '2026-11-04 09:00:00', 'Asistio', 'CLI-0007', 'CLS-0025');

INSERT INTO SolicitudCambio (idSolicitudCambio, motivo, fecha, estado, id_Clase, id_Instructor) VALUES
('SCM-0001', 'Solicitud de cambio de horario por cita médica urgente del instructor.', '2026-11-03 10:00:00', 'Pendiente', 'CLS-0001', 'INS-0001'),
('SCM-0002', 'Ajuste de hora por mantenimiento programado del salón principal.', '2026-11-03 11:30:00', 'Aprobada', 'CLS-0002', 'INS-0002'),
('SCM-0003', 'Modificación temporal del horario de inicio debido a capacitación técnica.', '2026-11-02 09:00:00', 'Rechazada', 'CLS-0003', 'INS-0003'),
('SCM-0004', 'Cambio de horario solicitado por cruce con evento institucional.', '2026-11-04 08:00:00', 'Pendiente', 'CLS-0004', 'INS-0004'),
('SCM-0005', 'Adelantar la clase 30 minutos por solicitud unánime de los alumnos inscritos.', '2026-11-03 14:00:00', 'Aprobada', 'CLS-0005', 'INS-0005'),
('SCM-0006', 'Cambio de hora por retraso en el transporte del instructor.', '2026-11-04 12:00:00', 'Rechazada', 'CLS-0006', 'INS-0006'),
('SCM-0007', 'Solicitud de modificación de turno matutino por motivos personales.', '2026-11-03 15:00:00', 'Pendiente', 'CLS-0007', 'INS-0007'),
('SCM-0008', 'Reubicación temporal de hora por revisión de equipos en el área.', '2026-11-04 10:30:00', 'Aprobada', 'CLS-0008', 'INS-0008'),
('SCM-0009', 'Cambio de horario por participación en torneo deportivo externo.', '2026-11-03 16:30:00', 'Rechazada', 'CLS-0009', 'INS-0009'),
('SCM-0010', 'Ajuste de horario por condiciones imprevistas de clima en área exterior.', '2026-11-04 14:00:00', 'Pendiente', 'CLS-0010', 'INS-0010');

INSERT INTO Pago (id_Pago, monto, fecha_Pago, concepto, id_Cliente, id_Recepcionista) VALUES
-- CLI-0001 (Membresía: MEB-01 - Costo: 100.00 - Vencimiento: 2026-11-06)
('PAG-0001', 100.00, '2026-11-05 08:30:00', 'Pago de Pase Diario', 'CLI-0001', 'REP-01'),

-- CLI-0002 (Membresía: MEB-02 - Costo: 350.00 - Vencimiento: 2026-11-12)
('PAG-0002', 350.00, '2026-11-05 09:00:00', 'Pago de Plan Semanal', 'CLI-0002', 'REP-02'),

-- CLI-0003 (Membresía: MEB-03 - Costo: 650.00 - Vencimiento: 2026-12-05)
('PAG-0003', 650.00, '2026-11-05 10:15:00', 'Pago de Mensualidad Básica', 'CLI-0003', 'REP-03'),

-- CLI-0004 (Membresía: MEB-04 - Costo: 900.00 - Vencimiento: 2026-12-05)
('PAG-0004', 900.00, '2026-11-05 11:20:00', 'Pago de Mensualidad Total', 'CLI-0004', 'REP-01'),

-- CLI-0005 (Membresía: MEB-05 - Costo: 1800.00 - Vencimiento: 2027-02-05)
('PAG-0005', 1800.00, '2026-11-05 12:00:00', 'Pago de Plan Trimestral', 'CLI-0005', 'REP-02'),

-- CLI-0006 (Membresía: MEB-06 - Costo: 500.00 - Vencimiento: 2026-12-05)
('PAG-0006', 500.00, '2026-11-05 12:45:00', 'Pago de Plan Estudiante Mensual', 'CLI-0006', 'REP-03'),

-- CLI-0007 (Membresía: MEB-07 - Costo: 550.00 - Vencimiento: 2026-12-05)
('PAG-0007', 550.00, '2026-11-05 13:10:00', 'Pago de Plan Matutino', 'CLI-0007', 'REP-01'),

-- CLI-0008 (Membresía: MEB-08 - Costo: 1600.00 - Vencimiento: 2026-12-05)
('PAG-0008', 1600.00, '2026-11-05 14:00:00', 'Pago de Plan Pareja Mensual', 'CLI-0008', 'REP-02'),

-- CLI-0009 (Membresía: MEB-09 - Costo: 1950.00 - Vencimiento: 2027-05-05)
('PAG-0009', 1950.00, '2026-11-05 15:30:00', 'Pago de Plan Semestral', 'CLI-0009', 'REP-03'),

-- CLI-0010 (Membresía: MEB-10 - Costo: 400.00 - Vencimiento: 2026-12-05)
('PAG-0010', 400.00, '2026-11-05 16:15:00', 'Pago de Plan Fines de Semana', 'CLI-0010', 'REP-01'),

-- CLI-0011 (Membresía: MEB-03 - Costo: 650.00 - Vencimiento: 2026-12-10)
('PAG-0011', 650.00, '2026-11-10 09:00:00', 'Pago de Mensualidad Básica', 'CLI-0011', 'REP-02'),

-- CLI-0012 (Membresía: MEB-04 - Costo: 900.00 - Vencimiento: 2026-12-10)
('PAG-0012', 900.00, '2026-11-10 10:30:00', 'Pago de Mensualidad Total', 'CLI-0012', 'REP-03'),

-- CLI-0013 (Membresía: MEB-01 - Costo: 100.00 - Vencimiento: 2026-11-07)
('PAG-0013', 100.00, '2026-11-06 11:00:00', 'Pago de Pase Diario', 'CLI-0013', 'REP-01'),

-- CLI-0014 (Membresía: MEB-05 - Costo: 1800.00 - Vencimiento: 2027-02-10)
('PAG-0014', 1800.00, '2026-11-10 12:15:00', 'Pago de Plan Trimestral', 'CLI-0014', 'REP-02'),

-- CLI-0015 (Membresía: MEB-06 - Costo: 500.00 - Vencimiento: 2026-12-10)
('PAG-0015', 500.00, '2026-11-10 13:40:00', 'Pago de Plan Estudiante Mensual', 'CLI-0015', 'REP-03'),

-- CLI-0016 (Membresía: MEB-02 - Costo: 350.00 - Vencimiento: 2026-11-15)
('PAG-0016', 350.00, '2026-11-08 14:20:00', 'Pago de Plan Semanal', 'CLI-0016', 'REP-01'),

-- CLI-0017 (Membresía: MEB-09 - Costo: 1950.00 - Vencimiento: 2027-05-10)
('PAG-0017', 1950.00, '2026-11-10 15:00:00', 'Pago de Plan Semestral', 'CLI-0017', 'REP-02'),

-- CLI-0018 (Membresía: MEB-04 - Costo: 900.00 - Vencimiento: 2026-12-15)
('PAG-0018', 900.00, '2026-11-15 10:00:00', 'Pago de Mensualidad Total', 'CLI-0018', 'REP-03'),

-- CLI-0019 (Membresía: MEB-07 - Costo: 550.00 - Vencimiento: 2026-12-15)
('PAG-0019', 550.00, '2026-11-15 11:30:00', 'Pago de Plan Matutino', 'CLI-0019', 'REP-01'),

-- CLI-0020 (Membresía: MEB-08 - Costo: 1600.00 - Vencimiento: 2026-12-15)
('PAG-0020', 1600.00, '2026-11-15 12:45:00', 'Pago de Plan Pareja Mensual', 'CLI-0020', 'REP-02');