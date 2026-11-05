# Taller PHP — 7 aplicaciones matemáticas y lógicas

Proyecto del taller de PHP Orientado a Objetos. Implementa 7 aplicaciones independientes
sobre una arquitectura **MVC construida desde cero** (sin frameworks de terceros) y
**sin JavaScript**: toda la lógica se ejecuta en el servidor.

| # | Aplicación | Descripción |
|---|---|---|
| 1 | Acrónimo | Convierte una frase en su acrónimo (los guiones separan palabras; el resto de la puntuación se elimina). |
| 2 | Fibonacci / Factorial | Formulario con selección de operación que genera la serie de Fibonacci o el factorial. |
| 3 | Estadísticas | Promedio, media (mediana) y moda de una serie de números reales de cantidad variable. |
| 4 | Conjuntos | Unión, intersección y diferencias `A-B` y `B-A` sobre conjuntos de enteros. |
| 5 | Binario | Conversión de un número entero a su representación binaria. |
| 6 | Árbol binario | Construcción del árbol a partir de dos o tres recorridos (preorden, inorden, postorden). |
| 7 | Calculadora | Suma, resta, multiplicación, división y porcentaje, con historial persistido en MySQL. |

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
│       └── base.css           # Estilos externos (Grid + Flexbox)
└── src/
    ├── Autoloader.php         # Autoloader PSR-4 propio (App\ → src/)
    ├── Config/
    │   ├── Database.php       # Singleton PDO
    │   └── routes.php         # Mapa de rutas
    ├── Core/
    │   ├── Request.php        # Envoltura de la petición HTTP
    │   ├── Router.php         # Resolución de rutas y despacho
    │   └── View.php           # Renderizado de plantillas y escapado
    ├── Controllers/
    │   └── HomeController.php # Menú principal
    ├── Models/                # Lógica de negocio y acceso a datos
    └── Views/                 # Plantillas HTML5
```

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

> El archivo `.gitignore` se mantiene **solo en el entorno local** y no se versiona en
> ninguna rama, por lo que los archivos de desarrollo (`SKILLS/`, `DOCSDEV/`,
> `Taller php.md`) no forman parte del repositorio publicado.

## Estado actual

En `feature/setup-base-datos` se encuentra:

- Base de datos `app_taller_php` con la tabla `historial_calculadora` verificada en MySQL 10.4.
- `Database` (Singleton PDO) con conexión comprobada.
- Autoloader, `Request`, `Router`, `View`, mapa de rutas, layout principal y menú.
- Front controller operativo con URLs limpias (`/proyecto-php1/...`) y respuestas 404.
- `.htaccess` que protege `/src`, `/SKILLS`, `/DOCSDEV`, `/.git` y los archivos de desarrollo.

El desarrollo de las 7 aplicaciones continúa en la rama siguiente.
