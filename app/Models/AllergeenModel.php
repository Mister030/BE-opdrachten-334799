<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Model voor user story 02: Inzien allergeneninformatie van product.
 *
 * Alle queries draaien via PDO (DB::select) en gebruiken named binding,
 * zodat SQL-injectie niet mogelijk is.
 */
class AllergeenModel
{
    /**
     * Haal naam en barcode van het product op.
     */
    public function getProductById(int $productId): ?stdClass
    {
        $sql = 'SELECT   p.Id      AS ProductId
                        ,p.Naam    AS Naam
                        ,p.Barcode AS Barcode
                FROM     Product p
                WHERE    p.Id = :productId
                AND      p.IsActief = 1';

        return DB::selectOne($sql, ['productId' => $productId]);
    }

    /**
     * Haal alle allergenen van het product op.
     *
     * De rijen worden gesorteerd op het veld Naam oplopend.
     *
     * @return array<int, stdClass>
     */
    public function getAllergenenByProductId(int $productId): array
    {
        $sql = 'SELECT     a.Naam         AS Naam
                          ,a.Omschrijving AS Omschrijving
                FROM       ProductPerAllergeen ppa
                INNER JOIN Allergeen           a ON a.Id = ppa.AllergeenId
                WHERE      ppa.ProductId = :productId
                AND        ppa.IsActief  = 1
                AND        a.IsActief    = 1
                ORDER BY   a.Naam ASC';

        return DB::select($sql, ['productId' => $productId]);
    }
}
