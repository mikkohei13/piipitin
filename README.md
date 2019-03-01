# Piipitin
Tools for sending data from FinBIF systems to mobile devices

## Setup

- `git clone https://github.com/mikkohei13/piipitin.git`
- Set up credentials to env/.env
- `docker-compose up --build`

## Usage examples


## Upgrade (UNTESTED)

- `docker-compose down`, `git pull` & `docker-compose up` if db has not changed

## Todo

- Docker: Do not run Composer as root/super user! See https://getcomposer.org/root for details


## Notes

Example queries to api.laji.fi



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

