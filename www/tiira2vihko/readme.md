
# Todo

- SHOULD:
  - Automated testing
  - Oma git-repositorio
- NICE:
  - Logging (time, number of rows)
  - Refactor filenames, function organization, varibale names

# Tulkinta

Tiira-exportissa pitää valita että "tyhjät kentät täytetään", eli kannattaa käyttää oletusasetusta. (Jos tyhjiä ei täytetä, systeemi ohittaa kaikki alihavainnot, koska niissä ei ole koordinaatteja.)

Käyttäjän vastuulla on että sekä itse havaintojen että muiden havainnoijien nimien tallennukseen on saatu lupa. Henkilönimiä ei poisteta tai salata.

Summahavainnot (rivityyppi "SUMMA") jätetään muunnetusta tiedostosta pois.

Koordinaatittomat havainnot jätetään pois. (Nämä saattavat olla lintuyhdistysten vanhoista arkistoista digitoituja havaintoja.)

Havainnot, jotka on salattu Tiirassa, karkeistetaan 10km tarkkuuteen. Jos tämä ei käyttäjälle riitä, hänen täytyy jättää salaisiksi halutut havainnot pois tallennustiedostosta.

Sarakkeita "Päivitetty" ja "Epäsuora havainto" ei huomioida, koska näiden merkitys epäselvä ja vaikuttavat sisältävän aina saman arvon.

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

