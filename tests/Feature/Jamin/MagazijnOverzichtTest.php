<?php

namespace Tests\Feature\Jamin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Controle op het scherm Overzicht Magazijn Jamin.
 *
 * Deze test draait tegen de echte MySQL-database mvc: daar staan zowel de
 * gebruikers als de productgegevens uit het createscript.
 */
class MagazijnOverzichtTest extends TestCase
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

    public function test_overzicht_toont_producten_gesorteerd_op_barcode(): void
    {
        $gebruiker = User::where('rolename', 'magazijnmedewerker')->firstOrFail();

        $response = $this->actingAs($gebruiker)->get('/magazijn');

        $response->assertOk();
        $response->assertSee('Overzicht Magazijn Jamin');
        $response->assertSee('Mintnopjes');
        $response->assertSee('Winegums');

        // Barcode oplopend: 8719587231278 (Mintnopjes) staat vooraan,
        // 8719587328256 (Witte Muizen) achteraan.
        $inhoud = $response->getContent();
        $this->assertLessThan(
            strpos($inhoud, '8719587328256'),
            strpos($inhoud, '8719587231278'),
            'De producten staan niet oplopend gesorteerd op Barcode.'
        );
    }

    public function test_de_iconen_linken_naar_het_juiste_product(): void
    {
        $gebruiker = User::where('rolename', 'magazijnmedewerker')->firstOrFail();

        $response = $this->actingAs($gebruiker)->get('/magazijn');
        $inhoud   = $response->getContent();

        foreach (['Mintnopjes', 'Winegums', 'Zoute Ruitjes', 'Cola Flesjes'] as $naam) {
            $productId = (int) DB::selectOne(
                'SELECT Id FROM Product WHERE Naam = :naam',
                ['naam' => $naam]
            )->Id;

            $this->assertStringContainsString(
                route('levering.show', $productId, absolute: false),
                $inhoud,
                "Het vraagteken-icoon van {$naam} linkt niet naar het juiste product."
            );
            $this->assertStringContainsString(
                route('allergeen.show', $productId, absolute: false),
                $inhoud,
                "Het kruis-icoon van {$naam} linkt niet naar het juiste product."
            );
        }
    }

    public function test_overzicht_is_afgeschermd_voor_gasten(): void
    {
        $this->get('/magazijn')->assertRedirect('/login');
    }
}
