

Tämä sovellus (tiira2vihko) muuntaa Tiira-lintutietopalvelusta (tiira.fi) saatavan havaintotaulukon muotoon, jonka voi tallentaa Vihko-havaintojärjestelmään (laji.fi/vihko). Se siis auttaa omien lintuhavaintojen kopioinnissa Tiirasta Vihkoon.

Sovellus on täysin epävirallinen ja omaan henkilökohtaiseen käyttööni tekemä, mutta muutkin sitä saavat käyttää. 
Sovellus on tarkoitettu ainoastaan **omien havaintojen** muuntamiseen.

Lue tämä ohje läpi ennen havaintojesi kopioimista, niin ymmärrät miten siirto tapahtuu. Olet itse vastuussa että siirrät havaintosi oikein. Ohje on ajantasainen 11/2021.

Huolehdi itse, että haet havainnot Tiirasta oikeassa muodossa (ohjeita alla), ja että havainnoissa mahdollisesti olevien muiden havainnoijien nimien tallennukseen on saatu heiltä lupa.

Huomioi, että kun olet vienyt havainnot kerran Vihkoon, kaikki korjaukset niihin pitää tehdä Vihkossa. Samoja havaintoja ei pidä viedä uudelleen Tiirasta Vihkoon, tällöin muodostuu kaksoiskappaleita. Pidä itse kirjaa mitä olet jo vienyt ja mitä et.

## Havaintojen haku Tiirasta

Valitse Tiiran omissa asetuksissa että koordinaattien tyyppi on **astekoordinaattit**.

Hae haluamasi havainnot ja lataa havainnot csv:na.

Valitse että "tyhjät kentät täytetään", eli kannattaa käyttää oletusasetusta. (Jos tyhjiä ei täytetä, tämä sovellus ohittaa kaikki alihavainnot, koska niissä ei ole koordinaatteja.)

## Havaintotiedoston muuntaminen

Sovellusta voi käyttää jommalla kummalla tavalla:

1. Asentamalla sen omalle palvelimelle
2. Käyttämällä sitä osoitteessa https://www.biomi.org/tools/tiira2vihko/

Sovellus tuottaa tiedoston nimeltään *vihko-import-(JX.519).csv*, jossa havainnot ovat Vihkoon sopivassa taulukkomuodossa.

Jos haluat tarkastella tätä Excelissä, avaa tiedosto teksieditorilla ja kopioi sisältö Exceliin.

## Havaintojen tallennus Vihkoon

Em. vaiheessa saamasi tiedoston (csv tai Excel) tallennus tapahtuu osoitteessa https://laji.fi/vihko/tools/import 

Vihko pyytää linkittämään lajinimet Vihkon tuntemiin nimiin. Useimmat nimet tunnistuvat automaattisesti, mutta Tiirassa käytetään lajia ylemmille taksoneille nimiä, joista kaikkia Vihko ei vielä tunne. Näille voi tehdä jommalla kummalla tavalla:

1. Linkittää tallennusvaiheessa tunnettuihin nimiin, esim. "harmaahanhilaji" -> "harmaahanhet" tai "rastaslaji (Turdus)" -> "Turdus".
2. Linkityksen voi jättää tekemättä, jolloin havainnot kyllä tallennetaan Vihkoon, mutta niitä on vaikeampi hakea ennen kuin ko. nimet lisätään sen käyttämään nimistöön.

Vihko pyytää myös linkittämään nimet henkilöihin. Jotta havainnot kirjautuisivat omiksi havainnoiksesi, valitse oman nimesi kohdalla pudotusvalikosta itsesi, eikä "Arvo sellaisenaan" (joka kirjaisi havainnoijaksi vain nimesi, ei käyttäjätunnustasi).

Jos et halua tai saa viedä jonkin henkilön nimeä Vihkoon, valitse nimen kohdalla "Jätä huomioimatta". Kaikilla havainnoilla pitää kuitenkin olla ainakin yksi havainnoijan nimi.

Lopuksi valitse miten haluat ryhmitellä havainnot havaintoeriksi, ja tarkista että havainnot näkyvät kartalla oikein, ja tallenna havainnot.

Voit etsiä havaintoja Laji.fi:ssa valitsemalla omat havaintosi ja käyttämällä esim. avainsanaa tiir2vihko, jonka tämä sovellus liittää kaikkiin havaintoihin: https://laji.fi/observation/list?keyword=tiira2vihko&observerPersonToken=true 

## Huomioita muunnoksesta

Jokaisesta alihavainnosta tehdään oma havaintonsa.

Tiiran tunniste (id) lisätään havainnon muihin tunnisteisiin. Tämän avulla duplikaatteja voi myöhemmin yrittää liittää toisiinsa, mikäli Tiiran havainnot tuodaan Vihkoon tai Laji.fi:hin myös muuta kautta. (Joskin tämä voi olla hankalaa, sillä Tiiran havaintotiedosto ei sisällä alihavainnon tunnistetta.)

Muunnetusta tiedostosta jätetään pois:
- Summahavainnot (rivityyppi "SUMMA").
- Koordinaatittomat havainnot. (Nämä saattavat olla lintuyhdistysten vanhoista arkistoista digitoituja havaintoja.)
- Sarakkeet "Päivitetty" ja "Epäsuora havainto", koska näiden merkitys epäselvä ja vaikuttavat sisältävän aina saman arvon.

Havainnot, jotka on salattu Tiirassa, karkeistetaan 10km tarkkuuteen. Jos tämä ei riitä, salaisiksi halutut havainnot täytyy jättää pois tallennustiedostosta tai karkeistaa niitä ennen tallennusta haluamallaan tavalla.

Paikkaan liittyvät tiedot kirjataan keruutapahtumaan (gathering). Havaintoon ja alihavaintoon (eli kaikkeen paitsi aikaan ja paikkaan) liittyvät tiedot kirjataan havaintoon (unitiin). Näin saman päivän havainnot menevät samaan havaintoerään (dokumenttiin) ja sen alla samasta paikasta kirjatut samaan keruutapahtumaan. Näin havaintoeriä muodostuu mahdollisimman vähän.

Jos linnun paikka on annettu, käytetään sitä. Jos ei, käytetään havainnoijan paikkaa. Tieto siitä kumpaa on käytetty kirjataan lisätietoihin ja avainsanoihin.

Koordinaattien tarkkuus muunnetaan metreiksi seuraavasti:

- Havainnoijan sijainnin tarkkuus
  - 10m 10
  - 50m  50
  - 200m  200
  - 250m  250
  - 500m  500
  - yli 500m 2000
- Havainnon sijainnin tarkkuus
  - 1km 1000
  - alle 5km  5000
  - yli 5km  10000
  - tyhjä   2000

Alkuperäinen tarkkuus kirjataan keruutapahtuman lisätietoihin. Jos tarkkuus on tyhjä, kirjataan tämä avainsanaksi,

Epätarkkoja päivämääriä ei hyväksytä. Jos päivä tai kuukausi on nolla, on tämä muutettava päivämääräväliksi. (Tiiran ja Laji.fi:n päivämäärälogiikka eroaa tässä: Tiirassa päivämääräväli tarkoittaa että lintu havaittu *koko* merkittynä aikana, Laji.fi:ssa että *joskus* merkittynä aikana.)

# Todo

- SHOULD:
  - Automated testing
  - Oma git-repositorio
- NICE:
  - Logging (time, number of rows)
  - Refactor filenames, function organization, varibale names

