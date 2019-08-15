

Todo:
- päätä avainsanat engl. vs. suomi
- tarkista tavuviiva sallittu avainsanoissa
- tarkista että kellonajat oikein
- testaa jos tyhjiä kenttiä ei täytetty tiira-exportissa

  TODO:  
  Not handled:
  Paikannettu

Aikaformaatti
2019-08-15T17:32

PV-indeksit
Ryhmänimet

erotellaanko tila-kentän arvot puolipisteellä vai pilkulla (kuten tässä nyt)?

erotellaanko avainsanat puolipisteellä vai pilkulla?


-----

Tulkinta

rivityyppi SUMMA jätetään pois

unitin keywordsiin tiira.fi, tiira2laji

unitin id-kenttään id

Jos linnun paikka annettu, käytetään sitä
Jos ei, käytetään havainnoijan paikkaa
Gathering remarksiin importoitu tiirasta "havainnoijan paikka" tai "linnun paikka" + ", tarkkuus " tarkkuus originaalina

koordinaattien tarkkuus
10m 10
50m  50
200m  200
250m  250
500m  500
>500m 2000
1km 1000
<5km  5000
>5km  10000
tyhjä   2000


tallentaja on vastuussa käyttöehtojen noudattamisesta, eli pitää kysyä lupa muiden nimien tallennukseen
- muut jotka havainnoijina
- jos joku muu tallentanut havainnon (hae vain omat tallennukset, jos haluat välttää tämän)

Ei tuettu
epätarkat päivämäärät, jossa päivä tai kuukausi nolla

------

import-parannuksia
- nappi, jolla voi valita helposti "arvo sellaisenaan". Voi jo nyt: nuoli+enter

Tiedoston datasta ei löytynyt havaintoja, jotka olisi voitu tallentaa. 
-> 
Tiedoston havainnoissa on virheitä, jonka vuoksi tietostoa ei voi tallentaa.


-----

Taxonomy
ks tiiran käyttämät termit lajia ylemmille pseudotaksoneille. Mihin laitetaan - aka, alt, obs?

Schema changes


Ehdotus: jätetään selitetekstit labeleista poi, jos olemassa lyhenne, jonka käyttö vakiintunut. Ohjeet muualle.
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

