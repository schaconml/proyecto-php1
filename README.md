# Taller PHP — 7 aplicaciones matemáticas y lógicas

Proyecto del taller de PHP Orientado a Objetos. Implementa 7 aplicaciones independientes
sobre una arquitectura **MVC construida desde cero** (sin frameworks de terceros) y
**sin JavaScript**: toda la lógica se ejecuta en el servidor.

| # | Aplicación | Estado | Descripción |
|---|---|---|---|
| 1 | Acrónimo | **Disponible** | Convierte una frase en su acrónimo (los guiones separan palabras; el resto de la puntuación se elimina). |
| 2 | Fibonacci / Factorial | **Disponible** | Formulario con selección de operación que genera la serie de Fibonacci o el factorial. |
| 3 | Estadísticas | **Disponible** | Promedio, media (mediana) y moda de una serie de números reales de cantidad variable. |
| 4 | Conjuntos | **Disponible** | Unión, intersección y diferencias `A-B` y `B-A` sobre conjuntos de enteros. |
| 5 | Binario | **Disponible** | Conversión de un número entero a su representación binaria. |
| 6 | Árbol binario | **Disponible** | Construcción del árbol a partir de dos o tres recorridos (preorden, inorden, postorden). |
| 7 | Calculadora | **Disponible** | Suma, resta, multiplicación, división y porcentaje, con historial persistido en MySQL. |

La **página principal** contiene el menú que redirige a cada aplicación.

---

## Requisitos

| Componente | Versión / detalle |
|---|---|
| **PHP** | **8.1 o superior** (probado en 8.2.12). El mínimo lo exigen las propiedades `readonly` y la sintaxis de invocable de primera clase |
| **Extensiones de PHP** | `pdo_mysql`, `mbstring`, `bcmath` (exactitud del factorial ≥ 21!), `ctype` |
| **Apache** | 2.4 con `mod_rewrite` **habilitado** y `AllowOverride All` (valores por defecto de XAMPP) |
| **MySQL / MariaDB** | Puerto **3306** (valor por defecto). El esquema es estándar: funciona en MySQL 8 y en MariaDB 10.4 |
| **Navegador** | Cualquier navegador actual |

> Las extensiones se comprueban con `php -m` y el módulo de Apache con `php -m` en la salida de
> Apache o en `http://localhost/phpmyadmin`.

## Instalación

1. Descargar el repositorio y **colocarlo dentro del `DocumentRoot` de Apache** (la carpeta debe
   terminar con el nombre del repositorio):

   ```bash
   git clone https://github.com/schaconml/proyecto-php1.git C:\xampp\htdocs\proyecto-php1
   ```

   También funciona descargando el ZIP de GitHub y descomprimiéndolo en `C:\xampp\htdocs\`.

2. Iniciar **Apache** y **MySQL** desde el panel de control de XAMPP.

3. **Crear la base de datos e importar el esquema.** Este paso es imprescindible: el repositorio
   contiene el *código* y el *esquema* (`database.sql`), pero **no los datos**. El script crea la
   base `app_taller_php` y la tabla `historial_calculadora` desde cero:

   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -P 3306 < C:\xampp\htdocs\proyecto-php1\database.sql
   ```

   Equivalente con phpMyAdmin: crear/importar el archivo `database.sql` desde la pestaña **Importar**.

4. Verificar el acceso en el navegador:

   ```
   http://localhost/proyecto-php1/          # Apache en el puerto 80 (por defecto)
   http://localhost:8080/proyecto-php1/      # Apache en otro puerto
   ```

   La URL final depende del **puerto de Apache**, no del código: la raíz pública de la aplicación
   se detecta en tiempo de ejecución, así que el proyecto funciona en cualquier puerto y en
   cualquier carpeta dentro de `htdocs`.

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
> Si su MySQL usa **otra contraseña** o **otro puerto**, edite las propiedades
> `username`, `password` y `port` de ese mismo archivo.

### Verificación antes de presentar

| # | Comprobación | Cómo se hace | Resultado esperado |
|---|---|---|---|
| 1 | MySQL está encendido | Panel de XAMPP → MySQL en verde | — |
| 2 | La base existe | `mysql -u root -P 3306 -e "SHOW DATABASES LIKE 'app_taller_php';"` | `app_taller_php` |
| 3 | La tabla existe | `mysql -u root -P 3306 app_taller_php -e "SHOW TABLES;"` | `historial_calculadora` |
| 4 | El sitio carga | Navegar a `http://localhost/proyecto-php1/` | Menú con las 7 aplicaciones |
| 5 | Las 7 apps responden | Navegar a `/acronimo`, `/binario`, `/fibonacci-factorial`, `/estadisticas`, `/conjuntos`, `/arbol`, `/calculadora` | Todas con su formulario |
| 6 | La calculadora persiste | `2 + 3` y recargar | El historial muestra la operación |
| 7 | Se puede borrar el historial | Botón *Borrar todo el historial* | La tabla queda vacía |

### Si algo falla

| Síntoma | Causa probable | Solución |
|---|---|---|
| Error 500 en **todas** las páginas | Apache sin `mod_rewrite` o con `AllowOverride None` | Habilitar `mod_rewrite` en `httpd.conf` y `AllowOverride All` en el bloque `<Directory "C:/xampp/htdocs">` |
| Error 404 en `/proyecto-php1/calculadora` pero el menú sí carga | El `.htaccess` no se está aplicando | Verificar `AllowOverride All` y que el archivo `.htaccess` exista en la raíz del proyecto |
| La calculadora muestra *«No fue posible conectar con la base de datos…»* | Falta importar `database.sql`, o MySQL está apagado, o las credenciales no coinciden | Importar `database.sql`, encender MySQL y revisar `src/Config/Database.php` |
| La calculadora funciona pero las otras páginas dan error 500 | PHP < 8.1 (no soporta `readonly`) | Actualizar PHP a 8.1 o superior |
| El factorial de 21 o más sale mal | Falta la extensión `bcmath` | Activar `bcmath` en `php.ini` |
| La URL abre el **listado de archivos** en vez del menú | `mod_rewrite` desactivado | Habilitar `mod_rewrite` y recargar |

> **Las otras 6 aplicaciones no dependen de la base de datos**: aunque MySQL esté apagado o el
> esquema no se haya importado, siguen funcionando con normalidad. Solo la calculadora necesita
> la base de datos, porque es la única que guarda el historial.

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
| `feature/tarea6` | **Aplicación 6: Árbol binario a partir de sus recorridos**. |
| `feature/tarea3` | **Aplicación 3: Estadísticas (promedio, media y moda)**. |
| `feature/tarea7` | **Aplicación 7: Calculadora con historial en MySQL**. |

> El archivo `.gitignore` se mantiene **solo en el entorno local** y no se versiona en
> ninguna rama, por lo que los archivos de desarrollo (`SKILLS/`, `DOCSDEV/`,
> `Taller php.md`) y el arnés de pruebas (`tests/`) no forman parte del repositorio publicado.

## Estado actual

En `feature/tarea7` se encuentran las **7 aplicaciones del taller**:

- Base de datos `app_taller_php` con la tabla `historial_calculadora` verificada en MySQL 10.4.
- `Database` (Singleton PDO) con conexión comprobada.
- Autoloader, `Request`, `Router`, `View`, `Container`, `Validator`, mapa de rutas y menú.
- Front controller operativo con URLs limpias (`/proyecto-php1/...`) y respuestas 404 y 500.
- `.htaccess` que protege `/src`, `/SKILLS`, `/DOCSDEV`, `/.git` y los archivos de desarrollo.
- **Aplicación 1 — Acrónimo** en `/acronimo`.
- **Aplicación 2 — Fibonacci y Factorial** en `/fibonacci-factorial` (patrón Strategy:
  `SequenceGenerator` + `FibonacciGenerator` + `FactorialGenerator` + `OperationSelector`;
  el factorial exacto por encima de 20! se calcula con `bcmath`).
- **Aplicación 3 — Estadísticas** en `/estadisticas` (`StatisticsCalculator` con promedio, media
  y moda, y `NumberSeriesParser`). La cantidad de números la elige el usuario en un flujo de dos
  pasos resuelto en el servidor, sin JavaScript.
- **Aplicación 4 — Conjuntos** en `/conjuntos` (`IntegerSet` como objeto de valor normalizado,
  `SetOperations` + `ArraySetOperations`, y `SetParser`).
- **Aplicación 5 — Binario** en `/binario` (admite negativos con signo y magnitud).
- **Aplicación 6 — Árbol binario** en `/arbol` (patrón Strategy: `TreeBuilder` +
  `PreInorderTreeBuilder` / `PrePostorderTreeBuilder` / `InPostorderTreeBuilder`,
  `TreeBuilderFactory` que elige el par y valida la consistencia, `TreeTraverser`,
  `TraversalParser` y `TreeLayoutCalculator`; el dibujo se genera en SVG desde el servidor).
- **Aplicación 7 — Calculadora** en `/calculadora` (`Calculator` + `CalculationOperation` y
  `CalculatorHistoryRepository`, la única clase con SQL, con el `PDO` inyectado por constructor
  y prepared statements). El historial se borra con POST, entrada por entrada o por completo.
