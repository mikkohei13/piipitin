<?php
$action = "test.php";
if (isset($_GET['DEBUG'])) {
  $action = $action . "?DEBUG";
}
?>
<html>
  <head>
    <title>Muunna Tiira-havaintotaulukko Vihko-muotoon</title>
  </head>
  <body>
  Tällä työkalulla voi muuntaa Tiira-lintutietojärjestelmästä haetun omien havaintojen havaintotiedoston muotoon, jossa sen voi tallentaa Lajitietokeskuksen Vihko-havaintopalveluun. Käyttö omalla vastuulla. Muunnettaavia tietoja ei tallenneta. Lue ohjeet: <a href="https://github.com/mikkohei13/piipitin/tree/master/www/tiira2vihko">https://github.com/mikkohei13/piipitin/tree/master/www/tiira2vihko</a> 
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
      Muunnettava Tiira-tiedosto:
      <input type="file" value="Valitse tiedosto" name="file" id="file">
      <p>
      <input type="submit" value="Muunna tiedosto" name="submit" id="submit">
  </form>
  </body>
</html>