<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.slim.js" integrity="sha512-JC/KiiKXoc40I1lqZUnoRQr96y5/q4Wxrq5w+WKqbg/6Aq0ivpS2oZ24x/aEtTRwxahZ/KOApxy8BSZOeLXMiA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/libass-wasm/4.1.0/js/subtitles-octopus.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/libass-wasm/4.1.0/js/subtitles-octopus.min.js" integrity="sha512-t1nSzh2GY4msBWoSPva0GNgcEB4aw0pUFxgXs71iL9tqFJ9QmA0e7Dj3FxWOt/wPQDseAPVo1fitvtAXDQTDYA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
var fontsList = ["/JavascriptSubtitlesOctopus/assets/js/default.woff2", 
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
  "/JavascriptSubtitlesOctopus/assets/fonts/corbel.ttf",
];
</script>

<video poster="https://cdn.myanimelist.net/images/anime/1048/100481l.jpg" id="player" playsinline="" controls="">
    <source src="http://127.0.0.1:8080/anime-dl/Download/Gantz.S01E01.MULTI.1440x960p.UPSCALE.x264.AC-3.MULTISUBS-MG.mkv" type="video/mp4">
    <source src="http://127.0.0.1:8080/anime-dl/Download/fr.mp3" type="audio/mp3">
    <source src="http://127.0.0.1:8080/anime-dl/Download/en.mp3" type="audio/mp3">
    <track src="http://127.0.0.1:8080/anime-dl/Download/gantz-01-fr.ass" srclang="fr" label="French" kind="subtitles" type="application/x-ass" default>
    <track src="http://127.0.0.1:8080/anime-dl/Download/gantz-01-en.ass" srclang="en" label="English" kind="subtitles" type="application/x-ass">
</video>

        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/5.1.1/mediaelement-and-player.min.js" integrity="sha512-1ag/f08peZxb8DqRH3XzUD/PlaWTER8ROlC/K1dxSovpg5m5rgxjNxor0xDTdxXvzpx1eo0Y6qynL7BocQ2R7w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
        <script src="assets/js/mediaelement-and-player.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/6.0.3/mediaelementplayer.min.css" integrity="sha512-9dMFiFyikcX8OM6ZRtmk5DlIuaNEgtZv/abBnUMbs/rdOlYGeS/1qqCiaycA6nUFV8mW93CTUmMOvOH2Tur14Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
            mejs.i18n.language('fr');

            $('video').mediaelementplayer({
                startLanguage: 'fr',
                success: function (player, node) {
                    var video = node;
                    player.addEventListener('captionschange', function(e) {
                        console.log('Charging Track ' + e.detail.caption);
                        if (e.detail.caption !== null) {
                            if (window.octopusInstance) {
                                window.octopusInstance.setTrackByUrl(e.detail.caption.src);
                            } else if (SubtitlesOctopus) {
                                var options = {
                                    video: video,
                                    subUrl: e.detail.caption.src,
                                    fonts: fontsList,
                                    //onReady: onReadyFunction,
                                    debug: true,
                                    workerUrl: '/JavascriptSubtitlesOctopus/assets/js/subtitles-octopus-worker.js'
                                };
                                window.octopusInstance = new SubtitlesOctopus(options);
                            }
                        } else {
                            if (SubtitlesOctopus || window.octopusInstance) {
                                console.log('Disable Track ' + e.detail.caption);
                                window.octopusInstance.freeTrack();
                            }
                        }
                    });

                    $(player).closest('.mejs__container').attr('lang', mejs.i18n.language());
                    $('html').attr('lang', mejs.i18n.language());
                }
            });


        </script>

        <style>
            html, body {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: hidden;
                background-color: black;
            }
            video {
                width: 100vw;
                height: 100vh;
            }
        </style>