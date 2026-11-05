# Taller PHP — 7 aplicaciones matemáticas y lógicas

Proyecto del taller de PHP Orientado a Objetos. Implementa 7 aplicaciones independientes
sobre una arquitectura **MVC construida desde cero** (sin frameworks de terceros) y
**sin JavaScript**: toda la lógica se ejecuta en el servidor.

| # | Aplicación | Estado | Descripción |
|---|---|---|---|
| 1 | Acrónimo | **Disponible** | Convierte una frase en su acrónimo (los guiones separan palabras; el resto de la puntuación se elimina). |
| 2 | Fibonacci / Factorial | **Disponible** | Formulario con selección de operación que genera la serie de Fibonacci o el factorial. |
| 3 | Estadísticas | Pendiente | Promedio, media (mediana) y moda de una serie de números reales de cantidad variable. |
| 4 | Conjuntos | **Disponible** | Unión, intersección y diferencias `A-B` y `B-A` sobre conjuntos de enteros. |
| 5 | Binario | **Disponible** | Conversión de un número entero a su representación binaria. |
| 6 | Árbol binario | Pendiente | Construcción del árbol a partir de dos o tres recorridos (preorden, inorden, postorden). |
| 7 | Calculadora | Pendiente | Suma, resta, multiplicación, división y porcentaje, con historial persistido en MySQL. |

La **página principal** contiene el menú que redirige a cada aplicación.

---

## Requisitos

- **XAMPP 8.x** (Apache + MySQL/MariaDB) con las extensiones `pdo_mysql`, `mbstring`, `bcmath` y `ctype`.
- **PHP 8.1+** (probado en PHP 8.2.12).
- Navegador web.

## Instalación

1. Ubicar el proyecto dentro del `DocumentRoot` de Apache:

   ```
   C:\xampp\htdocs\proyecto-php1\
   ```

2. Iniciar **Apache** y **MySQL** desde el panel de control de XAMPP.

3. Crear la base de datos e importar el esquema:

   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -P 3306 < C:\xampp\htdocs\proyecto-php1\database.sql
   ```

   El script `database.sql` crea la base `app_taller_php` y la tabla `historial_calculadora`.

4. Verificar el acceso:

   ```
   http://localhost:8080/proyecto-php1/
   ```

### Configuración de la base de datos

La conexión se define en `src/Config/Database.php` (patrón Singleton con PDO) y usa los
valores por defecto de XAMPP:

| Parámetro | Valor |
|---|---|
| Host | `127.0.0.1` |
| Puerto | `3306` |
| Base de datos | `app_taller_php` |
| Usuario | `root` |
| Contraseña | *(vacía)* |
| Charset | `utf8mb4` |

> En XAMPP el puerto por defecto de MySQL es `3306` y el de Apache es `80`. Si Apache
> escucha en otro puerto (por ejemplo `8080`), ajuste la URL de acceso; el proyecto no
> necesita cambios de código porque la raíz pública se detecta en tiempo de ejecución.

## Estructura del proyecto

```
proyecto-php1/
├── .htaccess                  # Reescritura de URLs hacia el front controller
├── database.sql               # Esquema de la base de datos
├── public/                    # Raíz pública (document root lógico)
│   ├── index.php              # Front Controller
│   └── css/
│       ├── base.css           # Estilos base (Grid + Flexbox)
│       └── modules.css        # Estilos de formularios y resultados
└── src/
    ├── Autoloader.php         # Autoloader PSR-4 propio (App\ → src/)
    ├── Config/
    │   ├── Container.php      # Contenedor de dependencias
    │   ├── Database.php       # Singleton PDO
    │   ├── routes.php         # Mapa de rutas
    │   └── services.php       # Punto único de composición (inyección de dependencias)
    ├── Controllers/
    │   ├── AcronymController.php
    │   ├── BinaryController.php
    │   ├── HomeController.php # Menú principal
    │   ├── Request.php        # Envoltura de la petición HTTP
    │   └── Router.php         # Resolución de rutas y despacho
    ├── Models/
    │   ├── AcronymGenerator.php
    │   ├── BinaryConverter.php
    │   └── Validator.php      # Validación de entrada reutilizable
    └── Views/                 # Plantillas HTML5 + motor de renderizado
        ├── View.php           # Renderizado de plantillas y escapado
        ├── acronym.php
        ├── binary.php
        ├── error-404.php
        ├── home.php
        └── layout.php
```

La estructura sigue exactamente el plan maestro: `/src/Models` (lógica de negocio),
`/src/Views` (plantillas), `/src/Controllers` (HTTP) y `/src/Config` (configuración y
cableado), con `/public` como raíz pública.

## Pruebas y verificación

El desarrollo local incluye un arnés de pruebas propio (sin PHPUnit ni dependencias
externas) que **se mantiene solo en el entorno local y no se versiona**:

```bash
# Pruebas unitarias del modelo de dominio
C:\xampp\php\php.exe tests\run.php

# Verificación automática de las reglas del proyecto
# (sin <div>, <span>, <b>, <i>, estilos inline, <style>, JavaScript ni archivos .js)
C:\xampp\php\php.exe tests\rules-check.php
```

Ambos comandos devuelven `0` en caso de éxito y `1` si detectan un fallo.

## Arquitectura y reglas del proyecto

- **MVC estricto**: los controladores solo gestionan HTTP y renderizado; la lógica de
  negocio vive en `/src/Models`; las vistas no contienen lógica.
- **Sin JavaScript**: prohibido en todo el proyecto (las soluciones interactivas se
  resuelven en el servidor).
- **HTML5 semántico**: sin `<div>`, `<span>`, `<b>` ni `<i>`; se usan `header`, `nav`,
  `main`, `section`, `article`, `aside`, `footer`, `figure`, `dl`, `ul`/`ol`, `table`, etc.
- **CSS externo**: todo el estilo vive en `/public/css/*.css`; sin `style="..."` ni `<style>`.
- **CSS Grid y Flexbox** para maquetación; sin frameworks (Bootstrap, Tailwind, etc.).
- **SOLID**: responsabilidad única, dependencia por inyección de constructores y prepared statements.

## Ramas

| Rama | Contenido |
|---|---|
| `main` | Estado estable y publicado. |
| `feature/setup-base-datos` | Esquema de base de datos, Singleton PDO y esqueleto de la arquitectura MVC. |
| `feature/tarea1` | Infraestructura de inyección de dependencias, validador y **Aplicación 1: Acrónimo**. |
| `feature/tarea5` | **Aplicación 5: Conversor a binario**. |
| `feature/tarea2` | **Aplicación 2: Fibonacci y Factorial**. |
| `feature/tarea4` | **Aplicación 4: Operaciones entre conjuntos**. |

> El archivo `.gitignore` se mantiene **solo en el entorno local** y no se versiona en
> ninguna rama, por lo que los archivos de desarrollo (`SKILLS/`, `DOCSDEV/`,
> `Taller php.md`) y el arnés de pruebas (`tests/`) no forman parte del repositorio publicado.

## Estado actual

En `feature/tarea4` se encuentra:

- Base de datos `app_taller_php` con la tabla `historial_calculadora` verificada en MySQL 10.4.
- `Database` (Singleton PDO) con conexión comprobada.
- Autoloader, `Request`, `Router`, `View`, `Container`, `Validator`, mapa de rutas y menú.
- Front controller operativo con URLs limpias (`/proyecto-php1/...`) y respuestas 404.
- `.htaccess` que protege `/src`, `/SKILLS`, `/DOCSDEV`, `/.git` y los archivos de desarrollo.
- **Aplicación 1 — Acrónimo** disponible en `/acronimo`.
- **Aplicación 5 — Binario** disponible en `/binario` (admite negativos con signo y magnitud).
- **Aplicación 2 — Fibonacci y Factorial** disponible en `/fibonacci-factorial` (patrón Strategy:
  `SequenceGenerator` + `FibonacciGenerator` + `FactorialGenerator` + `OperationSelector`;
  el factorial exacto por encima de 20! se calcula con `bcmath`).
- **Aplicación 4 — Conjuntos** disponible en `/conjuntos` (`IntegerSet` como objeto de valor
  normalizado, `SetOperations` + `ArraySetOperations` para unión, intersección y diferencias,
  y `SetParser` para validar la entrada).

Las aplicaciones 3, 6 y 7 se desarrollan en las ramas siguientes.
