# FluviApp - Sistema de Gestión y Operaciones Fluviales

Sistema web integral para el control de operaciones fluviales, programación de viajes, emisión de boletería para pasajeros, registro de carga/encomiendas y generación de manifiestos oficiales de zarpe para transporte por ríos.

---

## 🚀 Requisitos del Sistema

- **PHP** >= 8.2 (con extensión PDO MySQL habilitada)
- **MySQL / MariaDB** >= 10.4
- **Servidor Web:** Apache (incluido en **XAMPP**)
- **Ubicación:** `C:\xampp\htdocs\fluviapp`

---

## 🛠️ Instalación y Puesta en Marcha

1. **Clonar o ubicar el proyecto:**
   Debe estar alojado en la carpeta `htdocs` de XAMPP:
   ```text
   C:\xampp\htdocs\fluviapp
   ```

2. **Iniciar Servicios en XAMPP:**
   Abre el Panel de Control de XAMPP e inicia **Apache** y **MySQL**.

3. **Crear y poblar la Base de Datos:**
   Importa los archivos SQL ubicados en `database/`:
   - `database/schema.sql` (Estructura de tablas)
   - `database/seeders.sql` (Datos de prueba iniciales)

   *Desde phpMyAdmin:*
   Crea la base de datos `fluviapp` e importa ambos archivos en orden.

4. **Acceso al Sistema:**
   Abre tu navegador e ingresa a:
   👉 **[http://localhost/fluviapp](http://localhost/fluviapp)**

---

## 🔑 Credenciales de Acceso por Defecto

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@fluviapp.com` | `admin123` |
| **Taquillero / Venta** | `taquilla@fluviapp.com` | `admin123` |
| **Operador de Muelle** | `operador@fluviapp.com` | `admin123` |
| **Capitán de Barco** | `capitan@fluviapp.com` | `admin123` |

---

## 📦 Módulos Incluidos

1. **Dashboard:** Métricas en tiempo real de ingresos por pasajes y fletes, ocupación de viajes y accesos directos.
2. **Itinerarios y Viajes:** Programación de salidas, control de estados (Programado, Embarque, En Navegación, Arribado).
3. **Manifiesto Oficial de Zarpe:** Formato estandarizado imprimible con lista de pasajeros y carga para autoridades portuarias.
4. **Boletería:** Venta de pasajes con asignación de asientos y generación de tiquetes térmicos imprimibles.
5. **Carga y Encomiendas:** Guías de flete fluvial con pesaje y validación de bodega.
6. **Flota Fluvial:** Registro de lanchas rápidas, ferries, botes y barcazas.
7. **Rutas y Muelles:** Administración de puertos fluviales, distancias y tarifas base.
8. **Reportes y Estadísticas:** Análisis de ingresos y ocupación por rutas y embarcaciones.

---

## 🚢 Repositorio GitHub

- **URL:** [https://github.com/Davinson27/fluviapp](https://github.com/Davinson27/fluviapp)
- **Rama principal:** `main`
