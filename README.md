# Piipitin
Tools for sending data from FinBIF systems to mobile devices

## Setup

- `git clone https://github.com/mikkohei13/piipitin.git`
- Set up credentials to env/.env
- `docker-compose up --build`

## Usage examples

For production, ping localhost once every 10 minutes:

  watch -n600 curl localhost:90?mode=documents
  watch -n600 curl localhost:90/?mode=rarities

For debug:

  http://localhost:90/?mode=documents&debug=http://tun.fi/JX.987888
  http://localhost:90/?mode=rarities&debug=http://tun.fi/JX.988066

##

Top havainnoijat ja määrittäjät

Havainnoija-aggregointi
- havaittu tänä vuonna Suomessa lajitasolla, Vihkossa?
- aggregoi havainnoijan mukaan

Det-aggregointi
- havaittu tänä vuonna Suomessa lajitasolla, Vihkossa?
- aggregoi havainnoijan mukaan

Oma lajilista
- havaittu tänä vuonna Suomessa lajitasolla, Vihkossa?
  - itse havainnoijana, id
- aggregoi lajin mukaan


## Upgrade (UNTESTED)

- `docker-compose down`, `git pull` & `docker-compose up` if db has not changed

## Todo

- top-tilastoja
  - uudet lajit, tunnetutlla ja tuntemattomalla nimellä
  - eniten havaintoja suomesta vaikka ei suomalainen laji
- Get also yesterday's data
- error throwing and cathing
- tarkista ettei mennä pienempään eränumeroon kuin datafilessa
- Lähin suomenkielinen nimi mukaan
- check connection to api.laji.fi first and log error to telegram, only then write datafile 
- api cache=true for list query ?
- TODO's from comments
- Filter AA's observations
- Prepare for api errors
- disable telegram 23:00-07:00?
- Docker: Do not run Composer as root/super user! See https://getcomposer.org/root for details

- prepare for network problems, e.g. Warning:  file_get_contents(): php_network_getaddresses: getaddrinfo failed: Temporary failure in name resolution in /var/www/lajifi.php on line 5




## Notes


rarityScores

Käsittelee vain suomalaisia havaintoja.

- Suomen ensimmäinen: 40 pistettä. Yksi piste vähemmän jokaisesta seuraavasta havainnosta, eli 40. havainnosta saa enää yhden pisteen.
- Eliömaakunnan ensimmäinen: 20 pistettä. Vähennys samoin kuin edellä.
- Vuosikymmenen ensimmäinen: 20 pistettä. Vähennys samoin. Lasketaan vain kuluvan vuosikymmenen havainnoille.
- Kauden ensimmäinen: 20 pistettä. Vähennys samoin. Kausi = +-30 päivää havaintopäivästä, huomioimatta vuosilukua. Lasketaan vain havaoinnoille, joilla on tarkka päivämäärä.
- Vuoden ensimmäinen: 10 pistettä. Lasketaan vain kuluvan vuoden havainnoille.

Mielenkiintoisen raja on 10 pistettä.




Example queries to api.laji.fi


HR.1747 Kokoelma Vihko
ML.206 Maa Suomi


https://api.laji.fi/v0/warehouse/query/list?selected=document.collectionId%2Cdocument.createdDate%2Cdocument.documentId%2Cdocument.editorUserIds%2Cdocument.firstLoadDate%2Cdocument.formId%2Cdocument.loadDate%2Cdocument.modifiedDate%2Cdocument.sourceId%2Cgathering.biogeographicalProvince%2Cgathering.conversions.wgs84CenterPoint.lat%2Cgathering.conversions.wgs84CenterPoint.lon%2Cgathering.country%2Cgathering.displayDateTime%2Cgathering.eventDate.begin%2Cgathering.gatheringId%2Cgathering.interpretations.coordinateAccuracy%2Cgathering.linkings.observers.fullName%2Cgathering.locality%2Cgathering.municipality%2Cgathering.team%2Cunit.linkings.taxon.finnish%2Cunit.linkings.taxon.scientificName%2Cunit.reportedTaxonId%2Cunit.taxonVerbatim%2Cunit.unitId&orderBy=document.firstLoadDate&pageSize=20&page=1&cache=false&useIdentificationAnnotations=true&includeSubTaxa=true&includeNonValidTaxa=true&countryId=ML.206&collectionId=HR.1747&individualCountMin=1&firstLoadedSameOrAfter=2019-02-28%20DESC&qualityIssues=NO_ISSUES&access_token=

/v0/warehouse/query/list?selected=document.createdDate%2Cdocument.documentId%2Cdocument.loadDate%2Cgathering.biogeographicalProvince%2Cgathering.conversions.wgs84CenterPoint.lat%2Cgathering.conversions.wgs84CenterPoint.lon%2Cgathering.country%2Cgathering.eventDate.begin%2Cgathering.eventDate.end%2Cgathering.gatheringId%2Cgathering.interpretations.biogeographicalProvince%2Cgathering.interpretations.country%2Cgathering.interpretations.finnishMunicipality%2Cgathering.locality%2Cgathering.municipality%2Cgathering.notes%2Cgathering.province%2Cgathering.team%2Cunit.linkings.taxon.qname%2Cunit.linkings.taxon.scientificName%2Cunit.linkings.taxon.vernacularName%2Cunit.unitId&orderBy=document.documentId%20DESC&pageSize=100&page=1&collectionId=HR.1747&access_token=



/v0/warehouse/query/aggregate?aggregateBy=document.collectionId&geoJSON=false&pageSize=100&page=1&loadedLaterThan=" + parameters.sinceDate + "&access_token=


/v0/collections?lang=fi&langFallback=true&pageSize=1000&access_token=



Telegram api response:


SUCCESS

{
  "ok": true,
  "result": {
    "message_id": 627,
    "chat": {
      "id": -CHATID,
      "title": "Laji.fi info",
      "username": "lajifi",
      "type": "channel"
    },
    "date": 1551380850,
    "text": "Test 123"
  }
}


FAILURE:

{
  "ok": false,
  "error_code": 401,
  "description": "Unauthorized"
}

