# 🏋️‍♂️ ActivaFit - Sistema Integrador de Gestión para Gimnasios

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**ActivaFit** es una solución web integral diseñada para optimizar la administración operativa, el control de accesos por roles, la gestión de suscripciones/membresías y el agendamiento inteligente de clases grupales para centros deportivos y gimnasios.

---

## 📌 Tabla de Contenidos
- [Características Principales](#-características-principales)
- [Arquitectura y Tecnologías](#-arquitectura-y-tecnologías)
- [Estructura de la Base de Datos](#-estructura-de-la-base-de-datos)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación y Configuración Local](#-instalación-y-configuración-local)
- [Roles del Sistema](#-roles-del-sistema)
- [Casos de Uso e Historial de Desarrollo](#-casos-de-uso-e-historial-de-desarrollo)

---

## ⚡ Características Principales

- 🔐 **Autenticación Multi-Rol:** Acceso unificado y parametrizado por códigos institucionales para Clientes (`CLI-XXXX`), Recepcionistas (`REP-XX`), Instructores (`INS-XXXX`) y Administradores (`ADM-XX`).
- 👥 **Módulo Recepción:** 
  - Alta automatizada de clientes con generación dinámica de IDs no colisionables.
  - Asignación de membresías con cálculo automático de costo y fecha de vencimiento (`Carbon`).
  - Gestión integral (modificación y eliminación con confirmación) mediante interfaz en tarjeta deslizante (Slider horizontal CSS).
- 📅 **Módulo Clientes (CU-01):**
  - Catálogo interactivo de clases con filtrado por especialidad/aptitud del instructor y día real de la semana.
  - Validación dinámica de cupo máximo (`count()`) con indicador dinámico visual (Disponible / Lleno en rojo).
  - Bloqueo automatizado contra reservaciones duplicadas del mismo cliente en una sola sesión de clase.
  - Gestión de cancelaciones / baja voluntaria de reservaciones en tiempo real.

---

## 🛠️ Arquitectura y Tecnologías

| Componente | Tecnología Seleccionada | Justificación Técnica |
| :--- | :--- | :--- |
| **Plataforma / Tipo** | Aplicación Web MVC | Acceso distribuido multiplataforma sin instalación en clientes. |
| **Lenguaje Backend** | PHP 8.4+ | Entorno maduro con soporte para tipado estricto y alto rendimiento. |
| **Framework Web** | Laravel 11 | Facilita la separación MVC, enrutamiento limpio y manipulación fluida de fechas. |
| **SGBD** | MySQL 8.0 | Motor relacional robusto con soporte de restricciones de integridad referencial (`FK`, `CHECK`). |
| **Acceso a Datos** | Laravel Query Builder / DB Direct Connection | Consultas optimizadas con sintaxis fluida y bajo overhead de memoria. |
| **Frontend UI** | HTML5, CSS3, Blade, Tailwind CSS | Interfaz responsiva institucional (Navy `#1a2b4c` & Orange `#ff7f00`). |

---

## 🗄️ Estructura de la Base de Datos

La base de datos `gym_activafit` está modelada relacionalmente con validaciones mediante `CHECK` constraints e índices de clave primaria:

```
+-------------------+       +-----------------------+       +---------------------+
|     Membresia     |       |        Cliente        |       |    Inscripcion_Clase|
+-------------------+       +-----------------------+       +---------------------+
| id_Membresia (PK) |<------| id_Cliente (PK)       |<------| id_Reserva (PK)     |
| nombre_plan       |       | nombre                |       | fecha_Reserva       |
| costo             |       | edad                  |       | estado              |
| duracion_dias     |       | numero                |       | id_Cliente (FK)     |
| descripcion       |       | id_Membresia (FK)     |       | id_Clase (FK)       |--+
+-------------------+       | fecha_vencimiento     |       +---------------------+  |
                            +-----------------------+                                |
                                                                                     |
+-------------------+       +-----------------------+                                |
|    Instructor     |       |         Clase         |                                |
+-------------------+       +-----------------------+                                |
| id_Instructor(PK) |<------| id_Clase (PK)         |<-------------------------------+
| nombre            |       | nombre                |
| especialidad      |       | cupo_maximo           |
| horarioLaboral    |       | horario               |
+-------------------+       | dias_semana           |
                            | salon                 |
                            | id_Instructor (FK)    |
                            +-----------------------+
```

---

## 💻 Instalación y Configuración Local

Sigue estos pasos para desplegar el entorno local de desarrollo:

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/ActivaFit-Gym.git
cd ActivaFit-Gym
```

### 2. Instalar dependencias del proyecto
```bash
composer install
npm install && npm run build
```

### 3. Configurar variables de entorno
Copia el archivo `.env.example` como `.env`:
```bash
cp .env.example .env
```
Asegúrate de configurar la conexión MySQL en tu `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_activafit
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Importar la Base de Datos
Ejecuta el script SQL suministrado en el repositorio (`database/gym_activafit.sql`) en tu gestor MySQL (phpMyAdmin, DBeaver o CLI):
```bash
mysql -u root -p gym_activafit < database/gym_activafit.sql
```

### 5. Iniciar la aplicación
```bash
php artisan serve
```
Accede en tu navegador a `http://127.0.0.1:8000`.

---

## 👥 Roles del Sistema

Para realizar pruebas funcionales de acceso en el Login unificado:

| Rol | Código de Ejemplo | Módulo de Destino |
| :--- | :--- | :--- |
| **Cliente** | `CLI-0001` | Portal de Inscripción y Catálogo de Clases (`/catalogo`) |
| **Recepcionista** | `REP-01` | Registro y Administración de Clientes (`/recepcion`) |
| **Instructor** | `INS-0001` | Panel de Control de Clases e Instructores |
| **Administrador** | `ADM-01` | Configuración Global del Sistema |

---

## 📜 Casos de Uso e Historial de Desarrollo

### Estado de Implementación (Unidad 3 - E1)
- [x] **CU-00:** Autenticación unificada basada en roles e IDs parametrizados.
- [x] **CU-01:** Portal de Cliente: Filtrado de clases por aptitud/día, inscripción con control de cupo y bajas.
- [x] **CU-02:** Módulo de Recepción: Alta de clientes con vencimiento automático, edición/eliminación de usuarios y consulta de clases.
- [ ] **CU-03:** Módulo de Instructor (Programado para U3-E2).
- [ ] **CU-04:** Módulo de Administración (Programado para U3-E2).

---
© 2026 **ActivaFit Team** - Todos los derechos reservados.