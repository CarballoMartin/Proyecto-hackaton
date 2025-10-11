<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // El orden es crucial para que las dependencias se resuelvan correctamente.

        // 1. Usuarios (requerido por casi todo lo demás)
        $this->call([
            UserSeeder::class, // Crea SuperAdmin, Institucional, Productor
        ]);

        // 2. Catálogos y Configuración principal
        $this->call([
            ConfiguracionActualizacionSeeder::class, // Depende de User
            CondicionTenenciaSeeder::class,
            FuenteAguaSeeder::class,
            TipoPastoSeeder::class,
            TipoSueloSeeder::class,
            TipoRegistroSeeder::class,
            MotivoMovimientosSeeder::class,
            EspecieSeeder::class,
            RazaSeeder::class,
            CategoriaAnimalSeeder::class,
            TipoIdentificadorSeeder::class,
            MunicipiosSeeder::class,
            ParajesSeeder::class,
            MunicipioCoordinatesSeeder::class, // Updates municipios with coordinates
        ]);

        // 3. Entidades que dependen de los catálogos y usuarios
        $this->call([
            ProductorSeederMejorado::class, // Depende de User - 10 productores
            InstitucionSeederMejorado::class, // 10 instituciones
            UsuarioInstitucionalSeeder::class, // Usuarios institucionales para cada institución
            UnidadProductivaSeederMejorado::class, // Depende de Productor - 2-3 por productor
        ]);

        // 4. Stock (depende de todo lo anterior)
        $this->call([
            StockAnimalSeederMejorado::class, // Stock real con declaraciones
        ]);

        // 5. Datos adicionales
        $this->call([
            ClimaSeeder::class, // Datos de clima para municipios
        ]);
    }
}
