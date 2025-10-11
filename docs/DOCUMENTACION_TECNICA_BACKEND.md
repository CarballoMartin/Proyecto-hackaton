# Documentación Técnica del Backend para Desarrollo Móvil

**Proyecto:** Sistema de Gestión Ovino-Caprino
**Fecha:** 10 de septiembre de 2025
**Versión:** 1.0

## 1. Resumen General y Stack Tecnológico

Este documento detalla la arquitectura, endpoints y lógica de negocio del backend del sistema, con el objetivo de facilitar el desarrollo de una aplicación móvil para el rol de **Productor**.

El backend está construido sobre el stack TALL (Tailwind, Alpine.js, Laravel, Livewire), pero expone una API RESTful para la interacción con clientes externos como la aplicación móvil.

- **Framework Principal:** Laravel 12
- **Versión de PHP:** ^8.2
- **Base de Datos:** MySQL (por defecto, configurable en `.env`)
- **Autenticación API:** Laravel Sanctum
- **Servidor de Aplicación:** `php artisan serve` para desarrollo.

## 2. Arquitectura y Conceptos Clave

El sistema gestiona la producción ganadera a través de varias entidades centrales. Es crucial entender cómo se relacionan para construir una experiencia de usuario coherente en la aplicación móvil.

### 2.1. Modelos de Datos Principales

- **`User`**: El modelo de usuario estándar de Laravel. Un usuario puede tener el rol de `productor`, `superadmin`, etc. La aplicación móvil solo interactuará con usuarios de rol `productor`.
- **`Productor`**: Contiene la información personal del productor (DNI, CUIL, teléfono, etc.) y está vinculado a un `User` a través de la relación `usuario()`.
- **`UnidadProductiva` (UP)**: Es la entidad más importante para el productor. Representa una explotación agropecuaria (un campo, un RNSPA). Un productor puede tener asociadas múltiples UPs. **Toda la gestión de stock se realiza en el contexto de una UP seleccionada.**
- **`DeclaracionStock`**: Representa una "Ficha de Declaración" para un período específico y una UP concreta. Funciona como un contenedor o un "borrador" que agrupa todos los movimientos de stock. Su estado puede ser `en_progreso` o `completada`.
- **`StockAnimal`**: Es un registro individual de un movimiento de stock (un alta, una baja, una declaración inicial). Cada uno de estos registros pertenece a una `DeclaracionStock`.

### 2.2. Lógica de Negocio: El Cuaderno de Campo

El "Cuaderno de Campo" es la funcionalidad principal para el productor. La aplicación móvil debe replicar este flujo:

1.  **Ciclo de Declaración:** El sistema funciona en ciclos (ej. semestrales) con una fecha de inicio y fin.
2.  **Declaración "Viva":** Mientras un ciclo está activo, el productor tiene una `DeclaracionStock` con estado `en_progreso` para cada una de sus Unidades Productivas.
3.  **Registro de Movimientos:** El productor puede añadir movimientos (altas/bajas) a su declaración en cualquier momento. Estos movimientos se guardan en la tabla `stock_animals` y se asocian a la declaración `en_progreso`.
4.  **Cierre Automático:** No hay un botón de "Finalizar" para el productor. Una tarea programada en el servidor (`cron job`) cambia automáticamente el estado de las declaraciones a `completada` cuando el ciclo termina.
5.  **Consulta Histórica:** Las declaraciones completadas son el registro oficial y pueden ser consultadas, pero no modificadas por el productor.

## 3. API para la Aplicación Móvil

La API está diseñada para ser simple y segura, utilizando tokens de Laravel Sanctum.

**URL Base:** `http://<tu_dominio>/api`

### 3.1. Autenticación (Login sin Contraseña)

El flujo de autenticación no utiliza contraseñas, sino un sistema de código de un solo uso (OTP) para mayor seguridad y facilidad de uso.

#### **Paso 1: Solicitar Código de Acceso**

La aplicación móvil envía el identificador del productor (email o número de teléfono) para recibir un código.

- **Endpoint:** `/solicitar-codigo`
- **Método:** `POST`
- **Headers:**
    - `Content-Type: application/json`
    - `Accept: application/json`
- **Cuerpo de la Petición (JSON):**
  ```json
  {
    "identificador": "productor@email.com"
  }
  ```
  *O con teléfono:*
  ```json
  {
    "identificador": "+5493764123456"
  }
  ```
- **Respuesta Exitosa (Código `200 OK`):**
  ```json
  {
    "message": "Si su cuenta de productor existe, se ha enviado un código de acceso."
  }
  ```
  *(Nota: Por seguridad, la respuesta es siempre la misma para evitar la enumeración de usuarios).*

#### **Paso 2: Iniciar Sesión y Obtener Token**

Con el código recibido, la app solicita el token de acceso.

- **Endpoint:** `/iniciar-sesion`
- **Método:** `POST`
- **Headers:**
    - `Content-Type: application/json`
    - `Accept: application/json`
- **Cuerpo de la Petición (JSON):**
  ```json
  {
    "identificador": "productor@email.com",
    "codigo": "123456"
  }
  ```
- **Respuesta Exitosa (Código `200 OK`):**
  ```json
  {
    "message": "Inicio de sesión exitoso",
    "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer",
    "user": {
      "id": 10,
      "name": "Nombre del Productor",
      "email": "productor@email.com",
      "rol": "productor"
    }
  }
  ```
  El `access_token` debe ser almacenado de forma segura en el dispositivo móvil.

- **Respuesta de Error (Código `401 Unauthorized`):**
  ```json
  {
    "error": "El código de acceso es incorrecto o ha expirado."
  }
  ```

### 3.2. Realizar Peticiones Autenticadas

Todas las peticiones a endpoints protegidos deben incluir el token en la cabecera de autorización.

- **Header Requerido:**
  `Authorization: Bearer <access_token>`

- **Ejemplo de Endpoint Protegido (Obtener datos del usuario):**
    - **Endpoint:** `/user`
    - **Método:** `GET`
    - **Respuesta:** Devuelve el objeto `User` completo del usuario autenticado.

### 3.3. Endpoints Futuros (A desarrollar)

Actualmente, la API solo contiene los endpoints de autenticación. Los próximos endpoints a desarrollar para la funcionalidad del "Cuaderno de Campo" deberían ser:

- **`GET /api/unidades-productivas`**: Para listar las UPs del productor autenticado.
- **`GET /api/unidades-productivas/{id}/stock`**: Para obtener el stock actual de una UP.
- **`POST /api/unidades-productivas/{id}/movimientos`**: Para registrar nuevos movimientos (altas/bajas) en el cuaderno de campo de la UP seleccionada.

## 4. Consideraciones Adicionales

- **Manejo de Colas (Queues):** El envío de emails con los códigos OTP se procesa a través del sistema de colas de Laravel. En un entorno de producción, es **indispensable** tener un worker de colas (`php artisan queue:work`) activo y monitorizado.
- **Envío de SMS:** La lógica para enviar SMS está presente (`SmsServiceInterface`) pero actualmente utiliza un servicio falso (`FakeSmsService`) que solo escribe en el log. Para producción, se debe implementar un `SmsServiceInterface` real (ej. `TwilioSmsService`) y configurar las credenciales correspondientes en el archivo `.env`.
- **Variables de Entorno:** El archivo `.env` debe estar correctamente configurado con los detalles de la base de datos y las credenciales del servicio de correo (ej. Mailtrap para desarrollo, un servicio SMTP real para producción).
