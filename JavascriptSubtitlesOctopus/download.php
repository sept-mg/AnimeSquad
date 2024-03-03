<?php
  die();
    if(isset($_GET['animelink'])) {
        $animelink = $_GET['animelink'];
    } else {
        $animelink = "";
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Anime Squad Downloader</title>
    <link rel="shortcut icon" href="../icon/favicon.ico" type="image/x-icon">
    <link href="../styles.css" rel="stylesheet">
</head>
<body>
    <h1>Anime Squad Downloader</h1>
    <h2></h2>
    <button id='raw'></button>
    <progress id='progress-bar' max='100'></progress>
    <p id="size-downloaded">Taille téléchargée :</p>
    <p id="elapsed-time">Temps écoulé :</p>
    <p id="remaining-time">Temps restant :</p>
    <p id="download-speed">Vitesse de téléchargement :</p>
    <p id="size"></p>
    <!-- <h3>Sous-titre uniquement :</h3>
    <button id='subtitles'></button> -->
<script>
const animelink = "<?php echo $animelink; ?>";
myString = animelink;
lastSlashIndex = myString.lastIndexOf("/");
const rawfilename = myString.substring(lastSlashIndex + 1);

const rawDownload = document.getElementById('raw');
rawDownload.innerHTML = rawfilename;

document.getElementsByTagName("h2")[0].innerHTML = rawfilename.replace('.mkv', '').replace('.mp4', '');

function formatSize(size) {
  var units = ['octets', 'ko', 'Mo', 'Go', 'To'];
  var i = 0;
  while (size >= 1024 && i < units.length - 1) {
    size /= 1024;
    i++;
  }
  return size.toFixed(2) + ' ' + units[i];
}

var xhr = new XMLHttpRequest();
var size = 0;
// var raw_url = 'https://' + encodeURIComponent(animelink.replace("https://", '')).replace(/%2F/g,'/');
var raw_url = 'http://' + encodeURIComponent(animelink.replace("http://", '')).replace(/%2F/g,'/').replace("%3A", "1:", 1);
xhr.open('HEAD', raw_url, true);
xhr.onreadystatechange = function() {
  if (xhr.readyState === xhr.DONE) {
    if (xhr.status === 200) {
      size = xhr.getResponseHeader('content-length');
      document.getElementById('size').innerHTML = formatSize(size);
    } else {
    //   console.log('Erreur lors de la récupération de la taille du fichier');
    }
  }
};
// xhr.send(null);


var downloaded = false;


rawDownload.addEventListener('click', () => {
    if(downloaded === true) {
        return;
    }
    downloaded = true;
    const url = raw_url;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'blob';

    const startTime = performance.now(); // Initialisez startTime ici
    var previousLoaded = 0;
    xhr.onprogress = function(event) {
      const progressBar = document.getElementById('progress-bar');
      progressBar.value = Math.round((event.loaded / event.total) * 100);

      const elapsedTime = (performance.now() - startTime) / 1000; // Convertir en secondes
      const minutes = Math.floor(elapsedTime / 60);
      const seconds = Math.floor(elapsedTime % 60);
      document.getElementById('elapsed-time').innerHTML = `Temps écoulé : ${minutes}m ${seconds}s`;

      const speed = (event.loaded - previousLoaded) / elapsedTime; // Calculer la vitesse en unité par seconde
      const speedInBytes = formatSize(speed) + '/s'; // Convertir la vitesse en une représentation lisible
      document.getElementById('download-speed').innerHTML = `Vitesse de téléchargement : ${speedInBytes}`;

      const remainingBytes = event.total - event.loaded;
      const remainingTime = remainingBytes / speed; // Calculer le temps restant en fonction de la vitesse
      const remainingMinutes = Math.floor(remainingTime / 60);
      const remainingSeconds = Math.floor(remainingTime % 60);
      document.getElementById('remaining-time').innerHTML = `Temps restant : ${remainingMinutes}m ${remainingSeconds}s`;

      const sizeInBytes = event.loaded;
      const sizeAsString = formatSize(sizeInBytes);
      document.getElementById('size-downloaded').innerHTML = `Taille téléchargée : ${sizeAsString}`;

      previousLoaded = event.loaded; // Mettre à jour la quantité de données précédente pour le calcul de la vitesse
    };


    xhr.onload = function() {
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(xhr.response);
        a.download = rawfilename;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };

    xhr.send();
});

</script>
</body>
</html>