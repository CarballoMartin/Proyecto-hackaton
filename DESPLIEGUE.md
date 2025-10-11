# Guion de Despliegue Definitivo

Esta guía contiene todos los pasos para desplegar la aplicación en un servidor Ubuntu limpio.

---

### **1. Instalar Prerrequisitos (en servidor Ubuntu limpio):**
```bash
sudo apt update
sudo apt install -y git
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### **2. Clonar tu Repositorio:**
```bash
# Reemplaza la URL por la de tu repositorio
git clone https://github.com/tu-usuario/tu-repo.git /var/www/html 

# Navegar a la carpeta
cd /var/www/html
```

### **3. Corregir Permisos:**
```bash
# Añadir el directorio como seguro para Git
git config --global --add safe.directory /var/www/html

# Cambiar el propietario de los archivos para que Laravel y Composer puedan escribir
sudo chown -R 1000:1000 /var/www/html
```

### **4. Crear y Configurar el Archivo `.env`:**
```bash
# Copiar el de ejemplo
cp .env.example .env

# Corregir automáticamente el host de la BD
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=laravel_db/g' .env

# Activar y poner la contraseña correcta a la BD
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=password/g' .env
```

### **5. Levantar los Contenedores de Docker:**
```bash
sudo docker-compose up -d --build
```

### **6. Instalar Dependencias y Finalizar Configuración:**
```bash
# Instalar dependencias de PHP
sudo docker-compose exec app composer install --no-dev --optimize-autoloader

# Generar la clave de la aplicación
sudo docker-compose exec app php artisan key:generate

# Ejecutar migraciones y seeders de la base de datos
sudo docker-compose exec app php artisan migrate --seed --force
```

### **7. Configurar el Firewall:**
- Entra a tu panel de control de DonWeb.
- Busca la sección "Firewall".
- Crea una nueva regla para permitir tráfico **TCP** en el puerto **8080**.

---

Con esto, la aplicación debería estar funcionando en `http://<IP_DE_TU_SERVIDOR>:8080`.
