<?php

function export_data($vihkoRows) {
    
    if (isset($_GET['DEBUG'])) {
        print_r ($vihkoRows);
        exit("Finished debug");
    }

    $fileString = "";

    $fileString .= exportHeaderRow();

    foreach ($vihkoRows as $rowNumber => $row) {
        $fileString .= @exportCell($row['Laji - Määritys']);
        $fileString .= @exportCell($row['Muut tunnisteet - Havainto']);
        $fileString .= @exportCell($row['Alku - Yleinen keruutapahtuma']);
        $fileString .= @exportCell($row['Loppu - Yleinen keruutapahtuma']);
        $fileString .= @exportCell($row['Kunta - Keruutapahtuma']);
        $fileString .= @exportCell($row['Paikannimet - Keruutapahtuma']);
        $fileString .= @exportCell($row['Koordinaatit@N']);
        $fileString .= @exportCell($row['Koordinaatit@E']);
        $fileString .= @exportCell($row['Koordinaattien tarkkuus metreinä']);
        $fileString .= @exportCell($row['Koordinaatit@sys - Keruutapahtuma']);
        $fileString .= @exportCell($row['Pesimisvarmuusindeksi - Havainto']);
        $fileString .= @exportCell($row['Havainnoijat - Yleinen keruutapahtuma']);
        $fileString .= @exportCell($row['Havainnoijien nimet ovat julkisia - Yleinen keruutapahtuma']);
        $fileString .= @exportCell($row['Havainnon tarkat paikkatiedot ovat julkisia - Havaintoerä']);
        $fileString .= @exportCell($row['Määrä - Havainto']);
        $fileString .= @exportCell($row['Sukupuoli - Havainto']);
        $fileString .= @exportCell($row['Linnun puku - Havainto']);
        $fileString .= @exportCell($row['Linnun ikä - Havainto']);
        $fileString .= @exportCell($row['Linnun tila - Havainto']);
        $fileString .= @exportCell($row['Bongattu - Havainto']);
        $fileString .= @exportCell($row['Pesintä - Havainto']);
        $fileString .= @exportCell($row['Avainsanat - Havaintoerä']);
        $fileString .= @exportCell($row['Lisätiedot - Keruutapahtuma']);
        $fileString .= @exportCell($row['Kokoelma/Avainsanat - Havainto']);
        $fileString .= @exportCell($row['Lisätiedot - Havainto']);

        $fileString .= "\n";
    }

    $exportFilename = "data/tiira-export-" . date("His") . "-(JX.519).csv";
    file_put_contents($exportFilename, ("\xEF\xBB\xBF".$fileString)); // Add BOM

    echo $exportFilename;
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

