<?php

function export_data($vihkoRows) {
//    print_r ($vihkoRows); // debug

// todo: check if this contains all columns

    exportHeaderRow();

    foreach ($vihkoRows as $rowNumber => $row) {
        @exportCell($row['Laji - Määritys']); //  => sepelkyyhky
        @exportCell($row['Muut tunnisteet - Havainto']); //  => tiira.fi:19988183
        @exportCell($row['Alku - Yleinen keruutapahtuma']); //  => 08.08.2019, 17:30:00
        @exportCell($row['Loppu - Yleinen keruutapahtuma']); //  => 09.08.2019, 17:40:00
        @exportCell($row['Kunta - Keruutapahtuma']); //  => Espoo
        @exportCell($row['Paikannimet - Keruutapahtuma']); //  => Latokaski
        @exportCell($row['Koordinaatit@N']); //  => 60.1795
        @exportCell($row['Koordinaatit@E']); //  => 24.6649
        @exportCell($row['Koordinaattien tarkkuus metreinä']); //  => 500
        @exportCell($row['Koordinaatit@sys - Keruutapahtuma']); //  => wgs84
        @exportCell($row['Pesimisvarmuusindeksi - Havainto']); //  => 2
        @exportCell($row['Havainnoijat - Yleinen keruutapahtuma']); //  => Mikko Heikkinen; Inka Plit
        @exportCell($row['Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma']); //  => Kyllä
        @exportCell($row['Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä']); //  => 10 km
        @exportCell($row['Määrä - Havainto']); //  => 50
        @exportCell($row['Linnun puku - Havainto']); //  => ad (aikuinen)
        @exportCell($row['Linnun ikä - Havainto']); //  => +1kv (vanhempi kuin 1. kalenterivuosi)
        @exportCell($row['Linnun tila - Havainto']); //  => p, kiert
        @exportCell($row['Avainsanat - Havaintoerä']); //  => tiira.fi,import,tiira2vihko
        @exportCell($row['Lisätiedot - Keruutapahtuma']); //  => linnun koordinaatit / koordinaattien tarkkuus <500 m / tallentanut Tiiraan: Mikko Heikkinen / tallennettu Tiiraan: 2019-08-13 22:29:16
        @exportCell($row['Kokoelma/Avainsanat - Havainto']); //  => koordinaatit-linnun
        @exportCell($row['Lisätiedot - Havainto']); //  => https://www.tiira.fi/selain/naytahavis.php?id=19988183 / 1a / tuli peltojen suunnasta / parvi 17123

        echo "\n";
    }

}

function exportCell($cell) {
    if (isset($cell)) {
        echo $cell;
    }
    echo "\t";
    return;
}

function exportHeaderRow() {
    echo "Laji - Määritys	Muut tunnisteet - Havainto	Alku - Yleinen keruutapahtuma	Loppu - Yleinen keruutapahtuma	Kunta - Keruutapahtuma	Paikannimet - Keruutapahtuma	Koordinaatit@N - Keruutapahtuma	Koordinaatit@E - Keruutapahtuma	Koordinaattien tarkkuus metreinä	Koordinaatit@sys - Keruutapahtuma	Pesimisvarmuusindeksi - Havainto	Havainnoijat - Yleinen keruutapahtuma	Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma	Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä	Määrä - Havainto	Linnun puku - Havainto	Linnun ikä - Havainto	Linnun tila - Havainto	Avainsanat - Havaintoerä	Lisätiedot - Keruutapahtuma	Kokoelma/Avainsanat - Havainto	Lisätiedot - Havainto";
    echo "\n";
    return;
}

