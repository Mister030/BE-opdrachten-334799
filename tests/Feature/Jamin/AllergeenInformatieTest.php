<?php

namespace Tests\Feature\Jamin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Controle op user story 02: Inzien allergeneninformatie van product.
 */
class AllergeenInformatieTest extends TestCase
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

    public function test_scenario_01_toont_allergenen_gesorteerd_op_naam(): void
    {
        $response = $this->actingAs($this->gebruiker())
            ->get('/allergeen/' . $this->productId('Zoute Ruitjes'));

        $response->assertOk();
        $response->assertSee('Overzicht Allergenen');

        // Velden boven de tabel.
        $response->assertSee('Zoute Ruitjes');
        $response->assertSee('8719587323256');

        $response->assertSee('Dit product bevat gluten');
        $response->assertSee('Dit product bevat lactose');
        $response->assertSee('Dit product bevat soja');

        // Rijen gesorteerd op Naam oplopend: Gluten, Lactose, Soja.
        $inhoud = $response->getContent();
        $this->assertLessThan(strpos($inhoud, 'Lactose'), strpos($inhoud, 'Gluten'));
        $this->assertLessThan(strpos($inhoud, 'Soja'), strpos($inhoud, 'Lactose'));
    }

    public function test_scenario_02_toont_melding_als_het_product_geen_allergenen_bevat(): void
    {
        $response = $this->actingAs($this->gebruiker())
            ->get('/allergeen/' . $this->productId('Cola Flesjes'));

        $response->assertOk();
        $response->assertSee('In dit product zitten geen stoffen die een allergische reactie kunnen veroorzaken');

        // Na 4 seconden doorverwijzen naar Overzicht Magazijn Jamin.
        $response->assertSee('content="4;url=' . route('magazijn.index') . '"', false);
    }

    public function test_allergeneninformatie_is_afgeschermd_voor_gasten(): void
    {
        $this->get('/allergeen/1')->assertRedirect('/login');
    }
}
