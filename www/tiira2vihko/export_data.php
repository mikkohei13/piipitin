<?php

function export_data($vihkoRows) {
    
    if (isset($_GET['DEBUG'])) {
        print_r ($vihkoRows);
        exit("Finished debug");
    }

    $fileString = "";

// todo: check if this contains all columns

    $fileString .= exportHeaderRow();

    foreach ($vihkoRows as $rowNumber => $row) {
        $fileString .= @exportCell($row['Laji - Määritys']); //  => sepelkyyhky
        $fileString .= @exportCell($row['Muut tunnisteet - Havainto']); //  => tiira.fi:19988183
        $fileString .= @exportCell($row['Alku - Yleinen keruutapahtuma']); //  => 08.08.2019, 17:30:00
        $fileString .= @exportCell($row['Loppu - Yleinen keruutapahtuma']); //  => 09.08.2019, 17:40:00
        $fileString .= @exportCell($row['Kunta - Keruutapahtuma']); //  => Espoo
        $fileString .= @exportCell($row['Paikannimet - Keruutapahtuma']); //  => Latokaski
        $fileString .= @exportCell($row['Koordinaatit@N']); //  => 60.1795
        $fileString .= @exportCell($row['Koordinaatit@E']); //  => 24.6649
        $fileString .= @exportCell($row['Koordinaattien tarkkuus metreinä']); //  => 500
        $fileString .= @exportCell($row['Koordinaatit@sys - Keruutapahtuma']); //  => wgs84
        $fileString .= @exportCell($row['Pesimisvarmuusindeksi - Havainto']); //  => 2
        $fileString .= @exportCell($row['Havainnoijat - Yleinen keruutapahtuma']); //  => Mikko Heikkinen; Inka Plit
        $fileString .= @exportCell($row['Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma']); //  => Kyllä
        $fileString .= @exportCell($row['Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä']); //  => 10 km
        $fileString .= @exportCell($row['Määrä - Havainto']); //  => 50
        $fileString .= @exportCell($row['Sukupuoli - Havainto']);
        $fileString .= @exportCell($row['Linnun puku - Havainto']); //  => ad (aikuinen)
        $fileString .= @exportCell($row['Linnun ikä - Havainto']); //  => +1kv (vanhempi kuin 1. kalenterivuosi)
        $fileString .= @exportCell($row['Linnun tila - Havainto']); //  => p, kiert
        $fileString .= @exportCell($row['Bongattu - Havainto']);
        $fileString .= @exportCell($row['Pesintä - Havainto']);
        $fileString .= @exportCell($row['Avainsanat - Havaintoerä']); //  => tiira.fi,import,tiira2vihko
        $fileString .= @exportCell($row['Lisätiedot - Keruutapahtuma']); //  => linnun koordinaatit / koordinaattien tarkkuus <500 m / tallentanut Tiiraan: Mikko Heikkinen / tallennettu Tiiraan: 2019-08-13 22:29:16
        $fileString .= @exportCell($row['Kokoelma/Avainsanat - Havainto']); //  => koordinaatit-linnun
        $fileString .= @exportCell($row['Lisätiedot - Havainto']); //  => https://www.tiira.fi/selain/naytahavis.php?id=19988183 / 1a / tuli peltojen suunnasta / parvi 17123

        $fileString .= "\n";
    }

    $exportFilename = "data/tiira-export-" . date("Hmi") . "-(JX.519).csv";
    file_put_contents($exportFilename, ("\xEF\xBB\xBF".$fileString)); // Add BOM

    echo "exported " . $exportFilename;
}

function exportCell($cell) {
    $cellString = "";
    if (isset($cell)) {
        $cellString .= $cell;
    }
    $cellString .= "\t";
    return $cellString;
}

function exportHeaderRow() {
    return "Laji - Määritys	Muut tunnisteet - Havainto	Alku - Yleinen keruutapahtuma	Loppu - Yleinen keruutapahtuma	Kunta - Keruutapahtuma	Paikannimet - Keruutapahtuma	Koordinaatit@N - Keruutapahtuma	Koordinaatit@E - Keruutapahtuma	Koordinaattien tarkkuus metreinä	Koordinaatit@sys - Keruutapahtuma	Pesimisvarmuusindeksi - Havainto	Havainnoijat - Yleinen keruutapahtuma	Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma	Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä	Määrä - Havainto	Sukupuoli - Havainto	Linnun puku - Havainto	Linnun ikä - Havainto	Linnun tila - Havainto	Bongattu - Havainto	Pesintä - Havainto	Avainsanat - Havaintoerä	Lisätiedot - Keruutapahtuma	Kokoelma/Avainsanat - Havainto	Lisätiedot - Havainto\n";
}

