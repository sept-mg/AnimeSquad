<?php
    function notFound() {
      echo "<div id='notFound'>";
      echo "<h1>L'épisode est indisponible ou n'est pas encore sortie<h1>";
    }

    if(isset($_GET['id']) && isset($_GET['ep'])) {
        include '../db.php';

        $request_poster = "SELECT poster_online FROM info WHERE id = ? ";
        
        $stmt_poster = $mysqli->prepare($request_poster);
        $stmt_poster->bind_param('i', $_GET['id']);
        $stmt_poster->execute();

        $stmt_poster->bind_result($posterList);
        $stmt_poster->fetch();

        $stmt_poster->close();
        if ($posterList === null) {
          // not in the db
          notFound();
        }
        

        $request_video = "SELECT video_path, sub_path FROM episodes WHERE episode_number= ? AND id= ? ";
        
        $stmt_video = $mysqli->prepare($request_video);
        $stmt_video->bind_param('si', $_GET['ep'], $_GET['id']);
        $stmt_video->execute();

        $stmt_video->bind_result($video, $sub);
        $stmt_video->fetch();
        $stmt_video->close();
        
        $mysqli->close();
        $sublink = $url . $sub;
        $animelink = $url . $video;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Anime Squad Player</title>
  <link rel="shortcut icon" href="../icon/favicon.ico" type="image/x-icon">

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/libass-wasm/4.1.0/js/subtitles-octopus.js"></script>
  <!-- Styles -->
  <link rel="stylesheet" href="assets/css/plyr.css" />
  <script src="assets/js/plyr.js"></script>
</head>
<body>

  <div id="video">
    <?php
        if ($video == "not found") {
          notFound();
        }
        else {
            $poster = $poster = explode('|', $posterList);
            echo "<video id='player' poster='" . $poster[0] . "' class='plyr__video-embed' playsinline controls>";
            // the "../" because is local
            echo "<source src='". $url . htmlspecialchars($video, ENT_QUOTES) ."' type='video/mp4'/>";
            echo "</video>";
        }
        
    } else {
        notFound();
    }
    echo "</div>";
    ?>
    
      

  <script>
  var fontsList = ["/JavascriptSubtitlesOctopus/assets/js/default.woff2", //trebuchet MS
  "/JavascriptSubtitlesOctopus/assets/fonts/Aero_Matics_Display_Regular.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/georgiab.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/KozMinPro-Bold.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/PTSans-Bold.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/PTSans-BoldItalic.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/trebuc.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/trebuc-italic.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/OpenSans-ExtraBold.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/OpenSans-Italic.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/OpenSans-Light.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/OpenSans-Regular.ttf",
  "/JavascriptSubtitlesOctopus/assets/fonts/OpenSans-SemiBold.ttf",
];


  // Configuration des contrôles du player
  var controls = [
  //'play-large', // The large play button in the center
  //'restart', // Restart playback
  'rewind', // Rewind by the seek time (default 10 seconds)
  'play', // Play/pause playback
  'fast-forward', // Fast forward by the seek time (default 10 seconds)
  'progress', // The progress bar and scrubber for playback and buffering
  'current-time', // The current time of playback
  //'duration', // The full duration of the media
  'mute', // Toggle mute
  'volume', // Volume control
  //'captions', // Toggle captions
  'settings', // Settings menu
  'pip', // Picture-in-picture (currently Safari only)
  'airplay', // Airplay (currently Safari only)
  'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
  'fullscreen', // Toggle fullscreen
];

const plyrOptions = {
  controls: controls,
  invertTime: false,
  speed: { selected: 1, options: [0.5, 1, 1.25, 1.5, 2] },
  //quality: { selected: 1, options: ['1080p', '720p', '480p']}, // marche pas
  urls: {download : 'download.php?animelink=<?php echo urlencode($animelink) . '&sublink=' . urlencode($sublink);?>',
  },
};

// Initialisation du player
const player = new Plyr('.plyr__video-embed', plyrOptions);


player.on('ready', function () {
    var video = document.getElementById('player');
            window.SubtitlesOctopusOnLoad = function() {
                var options = {
                    video: video,
                    <?php 
                    // the "../" because is local 
                    echo "subUrl: \"" . $sublink ."\",\n"
                    ?>
                    fonts: fontsList,
                    //onReady: onReadyFunction,
                    //debug: true,
                    workerUrl: '/JavascriptSubtitlesOctopus/assets/js/subtitles-octopus-worker.js'
                };
                window.octopusInstance = new SubtitlesOctopus(options); // You can experiment in console
            };
            if (SubtitlesOctopus) {
                SubtitlesOctopusOnLoad();
            }
});

document.addEventListener('keydown', function(event) {
  if (event.key === "ArrowRight") { //foward key
    player.forward(10)
  }
  else if (event.key === "ArrowLeft") { // rewied key
    player.rewind(10)
  }
  
  else if (event.key === " ") { //play pause key
    if (player.playing) {
      player.pause();
    }
    else if (player.paused) {
      player.play();
    }
  }
  
  else if (event.key === "ArrowUp") { //volume increase
    if(player.volume < 1) {
      player.volume += 0.1;
    }
  }
  
  else if (event.key === "ArrowDown") { //volum downcrease
    if(player.volume > 0) {
      player.volume += -0.1;
    }
  }    

});
</script>

</body>
</html>
