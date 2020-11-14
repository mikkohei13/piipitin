
# Todo

- SHOULD:
  - Automated testing
  - Oma git-repositorio
- NICE:
  - Logging (time, number of rows)
  - Refactor filenames, function organization, varibale names

# Käyttö

Palvelu on tarkoitettu ainoastaan *omien havaintojen* muuntamiseen. 

## Havaintojen haku Tiirasta

Tiiran omissa asetuksissa kannattaa valita koordinaatit näytettäväksi wgs84-muodossa.

Tiira-exportissa pitää valita että "tyhjät kentät täytetään", eli kannattaa käyttää oletusasetusta. (Jos tyhjiä ei täytetä, systeemi ohittaa kaikki alihavainnot, koska niissä ei ole koordinaatteja.)

## Havaintojen tallennus Vihkoon

Tallennus tapahtuu osoitteessa https://laji.fi/vihko/tools/import

Jotta havainnot kirjautuisivat omiksi havainnoiksesi, henkilönimien linkitysvaiheessa valitse pudotusvalikosta oma nimesi, eikä "Arvo sellaisenaan" (joka kirjaisi havainnoijaksi vain nimesi, ei käyttäjätunnustasi).

Käyttäjän vastuulla on että muiden havainnoijien nimien tallennukseen on saatu heiltä lupa. Henkilönimiä ei poisteta tai salata.

Tiirassa käytetään lajia ylemmille taksoneille nimiä, joita Laji.fi ei vielä tunne. Nämä pitää linkittää tallennusvaiheessa tunnettuihin nimiin (esim. "harmaahanhilaji" > "harmaahanhet" tai "rastaslaji (Turdus)" -> "Turdus"). Vaihtoehtoisesti voi jättää linkityksen tekemättä, jolloin havainnot kyllä tallennetaan, mutta niitä on vaikeanmpi hakea ennen kuin ko. nimet lisätään Laji.fi:n nimistöön.

## Huomioita muunnoksesta

Tiiran havainto-id lisätään havainnon muihin tunnisteisiin. Tämän avulla duplikaatteja voi myöhemmin liittää toisiinsa, mikäli Tiiran havainnot tuodaan Laji.fi:hin myös muuta kautta.

Muunetusta tiedostosta jätetään pois:
- Summahavainnot (rivityyppi "SUMMA").
- Koordinaatittomat havainnot. (Nämä saattavat olla lintuyhdistysten vanhoista arkistoista digitoituja havaintoja.)
- Sarakkeet "Päivitetty" ja "Epäsuora havainto", koska näiden merkitys epäselvä ja vaikuttavat sisältävän aina saman arvon.

Havainnot, jotka on salattu Tiirassa, karkeistetaan 10km tarkkuuteen. Jos tämä ei käyttäjälle riitä, hänen täytyy jättää salaisiksi halutut havainnot pois tallennustiedostosta.

Paikkaan liittyvät tiedot kirjataan keruutapahtumaan (gathering). Havaintoon ja alihavaintoon (eli kaikkeen paitsi aikaan ja paikkaan) liittyvät tiedot kirjataan havaintoon (unitiin). Näin saman päivän havainnot menevät samaan havaintoerään (dokumenttiin) ja sen alla samasta paikasta kirjatut samaan keruutapahtumaan. Näin havaintoeriä muodostuu mahdollisimman vähän.

Jos linnun paikka on annettu, käytetään sitä. Jos ei, käytetään havainnoijan paikkaa. Kumpaa käytetty kirjataan lisätietoihin ja avainsanoihin.

Koordinaattien tarkkuus mapattu metreiksi seuraavasti:
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

Epätarkoja päivämääriä ei hyväksytä. Jos päivä tai kuukausi on nolla, on käyttäjän muutettava tämä päivämääräväliksi. (Tiiran ja Laji.fi:n päivämäärälogiikka eroaa tässä: Tiirassa päivämääräväli tarkoittaa että lintu havaittu *koko* merkittynä aikana, Laji.fi:ssa että *joskus* merkittynä aikana.)

