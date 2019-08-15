


Todo
----

- Skippaa koordinaatittomat
- loggaa skipatut
- testaa että avainsanat ja tunnisteet tallentuvat eroteltuina
- havainnon päivä ja aika dokumenttiin (tarkista)
- Better error handling (ks. todo)
- Tee testihavainto kaikilla tiedoilla, erikoismerkeillä ($deg; etc) ja molemmilla lomakkeilla
- testaa jos tyhjiä kenttiä ei täytetty tiira-exportin asetuksissa
- päätä avainsanat engl. vs. suomi
- tarkista tavuviiva sallittu avainsanoissa
- Tarkista erotellaanko tila-kentän arvot puolipisteellä vai pilkulla (kuten tässä nyt)?
- Ryhmänimien (joutsenlaji yms.) mappaus, ml.
- pieni & iso päiväpetolintu
- vesilintu
- HDR
- pikkulintu


Laji.fi muutoksia vaativia:
- Siirrä linnun aika unit-päivään, kun sellaisen voi importoida
- PV-indeksin mappaus
- Iän mappaus
- Tila suuntakenttään


Tulkinta
--------

Käyttäjän vastuulla on että sekä itse havaintojen että muiden havainnoijien nimien tallennukseen on saatu lupa. Henkilönimiä ei poisteta tai salata.

Koordinaatittomat havainnot jätetään pois. (Nämä saattavat olla lintuyhdistysten vanhoista arkistoista digitoituja havaintoja.)

Tiirassa salatut havainnot karkeistetaan 10km tarkkuuteen. Jos tämä ei käyttäjälle riitä, hänen täytyy jättää salaisiksi halutut havainnot pois tallennustiedostosta.

Rivityyppi SUMMA jätetään pois

Sarakkeita "Päivitetty" ja "Epäsuora havainto" ei huomioida, koska näiden merkitys epäselvä ja vaikuttavat sisältävän aina saman arvon.

Jos linnun paikka on annettu, käytetään sitä. Jos ei, käytetään havainnoijan paikkaa. Kumpaa käytetty kirjataan lisätietoihin ja avainsanoihin.

Koordinaattien tarkkuus mapattu metreiksi seuraavasti:
- Havainnoijan sijainnin tarkkuus
  - 10m 10
  - 50m  50
  - 200m  200
  - 250m  250
  - 500m  500
  - >500m 2000
- Havainnon sijainnin tarkkuus
  - 1km 1000
  - <5km  5000
  - >5km  10000
  - tyhjä   2000

Alkuperäinen tarkkuus kirjataan lisätietoihin. Jos tarkkuus on tyhjä, kirjataan tämä avainsanaksi,

Epätarkoja päivämääriä ei hyväksitä. Jos päivä tai kuukausi on nolla, on käyttäjän muutettava tämä päivämääräväliksi. (Tiiran ja Laji.fi:n päivämäärälogiikka eroaa tässä: Tiirassa päivämääräväli tarkoittaa että lintu havaittu *koko* merkittynä aikana, Laji.fi:ssa että *joskus* merkittynä aikana.)


------

Laji.fi

Tiedoston datasta ei löytynyt havaintoja, jotka olisi voitu tallentaa. 
-> 
Tiedoston havainnoissa on virheitä, joiden vuoksi tietostoa ei voi tallentaa.

Taxonomy
ks tiiran käyttämät termit lajia ylemmille pseudotaksoneille. Mihin laitetaan - aka, alt, obs?


Schema changes


Ehdotus: jätetään selitetekstit labeleista pois, jos olemassa lyhenne, jonka käyttö vakiintunut. Ohjeet muualle.
Vaihtoehto: sallitaan importissa pelkkä arvo, ts. ennen sulkua oleva osa trimmattuna. Tällöin tämän pitää olla uniikki, mutta niihän se onkin, jos suluissa vain ohje/selitys.

esim. 
pesimisvarmuusindeksit
lintujen iät
lintujen puvut

Add to MY.birdPlumageEnum:
MY.birdPlumagePep = pep (peruspuku)
MY.birdPlumageSs = ss (sulkasatoinen)

Add to MY.birdAgeEnum:

MY.birdAgePlus3kv = +3kv
MY.birdAge3Kv = 3kv
...
MY.birdAgePlus8kv = +8kv
MY.birdAge8Kv = 8kv


Fix typos
olden -> older
calender -> calendar

