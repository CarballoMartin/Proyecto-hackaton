# Plan de Implementación: Roles Flexibles y Perfiles Múltiples

## 1. Problema Identificado

El sistema de roles actual (`superadmin`, `institucional`, `productor`) es rígido y se basa en una única columna en la tabla `users`. Esto no permite manejar casos de uso reales donde un usuario puede desempeñar múltiples funciones dentro del ecosistema (ej. un investigador que también es productor).

## 2. Solución Propuesta

Se migrará de un sistema de "rol único" a un sistema de "perfiles basados en relaciones". La columna `users.rol` puede permanecer como un rol "primario" o por defecto, pero la autorización se basará en la existencia de relaciones y un "perfil activo" guardado en la sesión del usuario. Un ejemplo seria que una cuenta tenga su rol primario como 'intitucional', pero tambien su id de usuario esta en la tabla de productores o viceversa

Esto permite que un usuario con una sola cuenta pueda tener los perfiles de `institucional` y `productor` simultáneamente, accediendo a cada panel de forma separada.

## 3. Flujo de Usuario con Múltiples Perfiles

1.  **Login:** El usuario inicia sesión con sus credenciales únicas.
2.  **Detección:** Un middleware (`DetectMultipleProfiles`) se ejecuta inmediatamente después del login. Este middleware revisa las relaciones del usuario para determinar cuántos perfiles posee (ej. `Productor`, `InstitucionalParticipante`).
3.  **Redirección (si aplica):** Si el usuario posee más de un perfil, es redirigido a una página intermedia de "Selección de Perfil" (`/seleccionar-perfil`). Los usuarios con un solo perfil omiten este paso y son redirigidos a su dashboard correspondiente como de costumbre.
4.  **Selección:** En esta página, el usuario elige con qué perfil desea operar en la sesión actual (ej. "Acceder como Productor").
5.  **Almacenamiento en Sesión:** La elección del usuario se guarda en la sesión, ej: `session(['active_profile' => 'productor'])`.
6.  **Acceso al Panel:** El usuario es redirigido al dashboard del perfil seleccionado.
7.  **Cambio de Perfil:** Dentro de la aplicación, el menú de usuario contendrá un dropdown que le permitirá cambiar de perfil en cualquier momento, repitiendo los pasos 5 y 6.

## 4. Regla de Negocio Crítica: Tipos de Cuenta Institucional

Para la detección de perfiles múltiples, es vital diferenciar entre dos tipos de usuarios institucionales:

*   **Cuentas de Administrador (Tipo Entidad):** Son cuentas que representan a la institución en sí (ej. `inta@gmail.com`). Estas cuentas **NO son elegibles** para tener múltiples perfiles. Su propósito es único y exclusivo a la gestión institucional. El middleware de detección debe excluirlas de la lógica de perfiles múltiples.
*   **Cuentas de Participante (Tipo Persona):** Son usuarios con credenciales personales (ej. `juan.perez@gmail.com`) que están vinculados a una institución como técnicos, investigadores, etc. Estos usuarios **SÍ son elegibles** para tener un perfil adicional de `productor`.

## 5. Componentes Técnicos Clave

*   **Middleware `DetectMultipleProfiles`:** Se adjuntará a la ruta de login o al grupo de rutas autenticadas. Su lógica será:
    1.  Verificar si el usuario es un Administrador Institucional (Tipo Entidad). Si lo es, no hacer nada.
    2.  Contar los perfiles/relaciones del usuario (ej. si existe en `productors` y en `institucional_participantes`).
    3.  Si el conteo > 1, redirigir a `route('profile.selector')`.
*   **Ruta y Vista `Selector de Perfil`:**
    *   `GET /seleccionar-perfil` -> `ProfileSelectorController@show`
    *   `POST /seleccionar-perfil` -> `ProfileSelectorController@store`
*   **Middleware `CheckProfile:{perfil}`:** Reemplazará al middleware `role:`. Verificará que `session('active_profile')` coincida con el `{perfil}` requerido por la ruta.
*   **UI en `navigation-menu.blade.php`:** Un componente de dropdown que se mostrará si el usuario tiene más de un perfil, permitiéndole cambiar el `active_profile`.

## 6. Adaptación del Dashboard Institucional

La refactorización del dashboard institucional se alineará con este plan:

1.  Las rutas (`/institucional/*`) serán protegidas por el nuevo middleware `CheckProfile:institucional`.
2.  Los controladores (`DashboardController`, `GestionarParticipantesController`, etc.) se mantendrán como se crearon, ya que su lógica es independiente del sistema de roles.
3.  Las vistas se adaptarán a la estructura de layouts del sistema ("onion layout") según sea necesario.
