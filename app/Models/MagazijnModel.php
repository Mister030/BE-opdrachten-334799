<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * Model voor het scherm Overzicht Magazijn Jamin.
 *
 * De queries worden met PDO uitgevoerd (DB::select gebruikt onder water PDO).
 * Parameters worden meegegeven met named binding, zodat SQL-injectie
 * niet mogelijk is.
 */
class MagazijnModel
{
    /**
     * Haal alle in het magazijn aanwezige producten op.
     *
     * De rijen worden gesorteerd op Barcode oplopend, zoals beschreven
     * in de scenario's van user story 01 en 02.
     *
     * @return array<int, \stdClass>
     */
    public function getMagazijnOverzicht(): array
    {
        $sql = 'SELECT     m.Id                 AS MagazijnId
                          ,p.Id                 AS ProductId
                          ,p.Barcode            AS Barcode
                          ,p.Naam               AS Naam
                          ,m.VerpakkingsEenheid AS VerpakkingsEenheid
                          ,m.AantalAanwezig     AS AantalAanwezig
                FROM       Magazijn m
                INNER JOIN Product  p ON p.Id = m.ProductId
                WHERE      m.IsActief = 1
                AND        p.IsActief = 1
                ORDER BY   p.Barcode ASC';

        return DB::select($sql);
    }
}
