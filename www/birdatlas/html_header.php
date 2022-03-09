<!DOCTYPE html>
<html lang="fi">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lintuatlas-tilastoja</title>
    <link rel="stylesheet" href="style.css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GOOGLE_ANALYTICS_ID; ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
    </script>

  </head>
  <body>

<h1>Lintuatlaksen tilastoja</h1>
<p>Nämä tilastot tulevat Lajitietokeskuksen avoimesta ohjelmointirajapinnasta (<a href=\"https://api.laji.fi/\">api.laji.fi</a>). Mukana ovat lintuhavainnot Suomesta 1.1.2022 alkaen, joille on kirjattu mahdolliseen, todennäköiseen tai varmaan pesintään liittyvä pesimävarmuusindeksi. Arkaluontoiset tai käyttäjien karkeistamat havainnot tulevat mukaan, mikäli karkeistustapa ei rajaa niitä pois.
</p>
<p>
Vihkon havainnot päivittyvät mukaan 1-30 minuutin viiveellä, Tiiran havainnot kerran vuorokaudessa, ja iNaturalistin pari kertaa viikossa.
</p>

<p>Mikko Heikkinen/<a href="https://biomi.org/">biomi.org</a></p>