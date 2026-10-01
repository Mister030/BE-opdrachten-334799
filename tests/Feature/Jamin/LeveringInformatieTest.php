<?php

namespace Tests\Feature\Jamin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Controle op user story 01: Inzien leveringsinformatie product.
 */
class LeveringInformatieTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml zet de database standaard op sqlite in-memory. De gebruikers
        // en de productgegevens staan in de MySQL-database mvc.
        config([
            'database.default'                    => 'mysql',
            'database.connections.mysql.database' => 'mvc',
        ]);
    }

    private function gebruiker(): User
    {
        return User::where('rolename', 'magazijnmedewerker')->firstOrFail();
    }

    private function productId(string $naam): int
    {
        return (int) DB::selectOne(
            'SELECT Id FROM Product WHERE Naam = :naam',
            ['naam' => $naam]
        )->Id;
    }

    public function test_scenario_01_toont_leveringen_van_een_voorradig_product(): void
    {
        $response = $this->actingAs($this->gebruiker())
            ->get('/levering/' . $this->productId('Mintnopjes'));

        $response->assertOk();
        $response->assertSee('Levering Informatie');

        // Velden boven de tabel.
        $response->assertSee('Venco');
        $response->assertSee('Bert van Linge');
        $response->assertSee('L1029384719');
        $response->assertSee('06-28493827');

        // Leveringen, gesorteerd op datum laatste levering oplopend.
        $inhoud = $response->getContent();
        $this->assertLessThan(
            strpos($inhoud, '18-10-2024'),
            strpos($inhoud, '09-10-2024'),
            'De leveringen staan niet oplopend gesorteerd op datum levering.'
        );
    }

    public function test_scenario_02_toont_melding_als_het_product_niet_voorradig_is(): void
    {
        $response = $this->actingAs($this->gebruiker())
            ->get('/levering/' . $this->productId('Winegums'));

        $response->assertOk();
        $response->assertSee('Er is van dit product op dit moment geen voorraad aanwezig, de verwachte eerstvolgende levering is: 30-04-2023');

        // Velden boven de tabel, ook als er geen voorraad is.
        $response->assertSee('Basset');
        $response->assertSee('Joyce Stelterberg');
        $response->assertSee('L1023845773');
        $response->assertSee('06-48293823');

        // Na 4 seconden doorverwijzen naar Overzicht Magazijn Jamin.
        $response->assertSee('content="4;url=' . route('magazijn.index') . '"', false);
    }

    public function test_leveringsinformatie_is_afgeschermd_voor_gasten(): void
    {
        $this->get('/levering/1')->assertRedirect('/login');
    }
}
