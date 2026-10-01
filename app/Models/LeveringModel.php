<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Model voor user story 01: Inzien leveringsinformatie product.
 *
 * Alle queries draaien via PDO (DB::select) en gebruiken named binding,
 * zodat SQL-injectie niet mogelijk is.
 */
class LeveringModel
{
    /**
     * Haal het product met zijn voorraad op uit het magazijn.
     */
    public function getProductMetVoorraad(int $productId): ?stdClass
    {
        $sql = 'SELECT     p.Id             AS ProductId
                          ,p.Naam           AS Naam
                          ,p.Barcode        AS Barcode
                          ,m.AantalAanwezig AS AantalAanwezig
                FROM       Product  p
                INNER JOIN Magazijn m ON m.ProductId = p.Id
                WHERE      p.Id = :productId
                AND        p.IsActief = 1
                AND        m.IsActief = 1';

        return DB::selectOne($sql, ['productId' => $productId]);
    }

    /**
     * Haal de leverancier op die het product aan het magazijn levert.
     */
    public function getLeverancierByProductId(int $productId): ?stdClass
    {
        $sql = 'SELECT     l.Naam              AS Naam
                          ,l.ContactPersoon    AS ContactPersoon
                          ,l.LeverancierNummer AS LeverancierNummer
                          ,l.Mobiel            AS Mobiel
                FROM       Leverancier           l
                INNER JOIN ProductPerLeverancier ppl ON ppl.LeverancierId = l.Id
                WHERE      ppl.ProductId = :productId
                AND        ppl.IsActief  = 1
                AND        l.IsActief    = 1
                GROUP BY   l.Id
                         , l.Naam
                         , l.ContactPersoon
                         , l.LeverancierNummer
                         , l.Mobiel
                ORDER BY   MAX(ppl.DatumLevering) DESC';

        return DB::selectOne($sql, ['productId' => $productId]);
    }

    /**
     * Haal alle leveringen van het product op.
     *
     * De rijen worden gesorteerd op het veld Datum laatste levering oplopend.
     *
     * @return array<int, stdClass>
     */
    public function getLeveringenByProductId(int $productId): array
    {
        $sql = 'SELECT     p.Naam                          AS Naam
                          ,ppl.DatumLevering               AS DatumLevering
                          ,ppl.Aantal                      AS Aantal
                          ,ppl.DatumEerstVolgendeLevering  AS DatumEerstVolgendeLevering
                FROM       ProductPerLeverancier ppl
                INNER JOIN Product               p ON p.Id = ppl.ProductId
                WHERE      ppl.ProductId = :productId
                AND        ppl.IsActief  = 1
                ORDER BY   ppl.DatumLevering ASC';

        return DB::select($sql, ['productId' => $productId]);
    }
}
