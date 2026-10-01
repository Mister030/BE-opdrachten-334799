<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Het createscript is MySQL-specifiek. Bij het testen draait Laravel op
        // sqlite in-memory, daar wordt deze migratie overgeslagen.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        // Het createscript werkt in database `mvc`, dezelfde database als in
        // DB_DATABASE. Het maakt alleen de zes Jamin-tabellen (opnieuw) aan, dus
        // de tabellen van Laravel zelf blijven staan.
        DB::unprepared(file_get_contents(database_path('migrations/create_script_jamin_1.sql')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement('DROP TABLE IF EXISTS ProductPerAllergeen');
        DB::statement('DROP TABLE IF EXISTS ProductPerLeverancier');
        DB::statement('DROP TABLE IF EXISTS Magazijn');
        DB::statement('DROP TABLE IF EXISTS Leverancier');
        DB::statement('DROP TABLE IF EXISTS Product');
        DB::statement('DROP TABLE IF EXISTS Allergeen');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
