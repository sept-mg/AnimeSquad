<?php
    $notfund = false;

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
        $poster = explode('|', $posterList);
        

        $request_video = "SELECT video_path, sub_path FROM episodes WHERE episode_number= ? AND id= ? ";
        
        $stmt_video = $mysqli->prepare($request_video);
        $stmt_video->bind_param('si', $_GET['ep'], $_GET['id']);
        $stmt_video->execute();

        $stmt_video->bind_result($video, $sub);
        $stmt_video->fetch();
        $stmt_video->close();
        
        $mysqli->close();

        $subs = explode('|', $sub);
        if ($video == "not found") {
            notFound();
        }

?>
<meta charset="UTF-8">
<title>Anime Squad Player</title>
<link rel="shortcut icon" href="../icon/favicon.ico" type="image/x-icon">
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
</script>
<?php
echo '<video id="player" poster="'. $poster[0] .'" class="video-js" controls preload="auto" data-setup="{}">';
echo '<source src="' . $url . htmlspecialchars($video, ENT_QUOTES) . '" type="video/mp4">';
    for($i=0; $i < count($subs); $i++) {
        $lang = explode(':', $subs[$i]);
        if ($i == 0) {
            echo '<track src="" srclang="'. $lang[0] .'" kind="subtitles" type="application/x-ass" default>';
        } else {
            echo '<track src="" srclang="'. $lang[0] .'" kind="subtitles" type="application/x-ass">';
        }
    }
echo '<p class="vjs-no-js"> To view this video please enable JavaScript, and consider upgrading to a web browser that<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
</video>';
}
?>

<link href="assets/css/video-js.css" rel="stylesheet" />
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/libass-wasm/4.1.0/js/subtitles-octopus.min.js" integrity="sha512-t1nSzh2GY4msBWoSPva0GNgcEB4aw0pUFxgXs71iL9tqFJ9QmA0e7Dj3FxWOt/wPQDseAPVo1fitvtAXDQTDYA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/js/subtitles-octopus.js"></script>
        <script src="assets/js/video.js"></script>

        <script>
            var video = null;
            var dictionnaire = {};
            <?php
                for($i=0; $i < count($subs); $i++) {
                    $lang = explode(':', $subs[$i]);
                    echo 'dictionnaire["' . $lang[0] . '"] = "' . $url . $lang[1] . '";'. PHP_EOL;
                }
            ?>
            var player = videojs('#player', {});
            player.ready(function () {
                // This would look more nice as a plugin but is's just as showcase of using with custom players
                video = this.tech_.el_;
            });

                player.on('texttrackchange', function(e) {
                    var activeTextTrack = null;
                    for (var i = 0; i < player.textTracks().length; i++) {
                        var track = player.textTracks()[i];
                        if (track.mode === 'showing') {0
                        activeTextTrack = track;
                        break;
                        }
                    }
                    if (activeTextTrack !== null) {
                        var url = dictionnaire[activeTextTrack.language];
                        if (window.octopusInstance) {
                            window.octopusInstance.setTrackByUrl(url);
                        } else if (SubtitlesOctopus) {
                            var options = {
                                video: video,
                                subUrl: url,
                                fonts: fontsList,
                                //onReady: onReadyFunction,
                                debug: false,
                                workerUrl: '/JavascriptSubtitlesOctopus/assets/js/subtitles-octopus-worker.js'
                            };
                            window.octopusInstance = new SubtitlesOctopus(options);
                        }
                    } else {
                        if (SubtitlesOctopus || window.octopusInstance) {
                            if (window.octopusInstance) {
                                window.octopusInstance.freeTrack();
                            }

                        }
                    }
                });

            player.on('fullscreenchange', function() {
                    window.octopusInstance.render(window.octopusInstance.lastRenderTime)
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === "ArrowRight") { //foward key
                    player.currentTime(player.currentTime() + 10)
                }
                else if (event.key === "ArrowLeft") { // rewied key
                    player.currentTime(player.currentTime() - 10)
                }
                
                else if (event.key === " ") { //play pause key
                    if(player.paused()) {
                        player.play()
                    } else {
                        player.pause()
                    }
                }
                
                else if (event.key === "ArrowUp") { //volume increase
                    player.volume(player.volume() + 0.1)
                }
                
                else if (event.key === "ArrowDown") { //volum downcrease
                    player.volume(player.volume() - 0.1)
                } 

            });
        </script>
      </div>