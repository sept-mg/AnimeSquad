<meta charset="UTF-8">
<title>Anime Squad Player</title>
<link rel="shortcut icon" href="../icon/favicon.ico" type="image/x-icon">

<?php
    function errorCSS() {
        echo "<style>
        @font-face {
            font-family: 'coe';
            src: url('assets/fonts/corbel.otf');
        }

        html, body {
            overflow: hidden;
            background-color: #262626;
            color: #ffff;
            margin: 0;
            padding: 0;
        }
        #notFound {
            font-family: 'coe';
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
            text-align: center;
        }
        </style>";
    }
    function notFound() {
      echo "<div id='notFound'>";
      echo "<h1>L' &eacute;pisode est indisponible ou n'est pas encore sortie<h1>";
      errorCSS();
      exit();
    }
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($userAgent, 'Firefox')) {
        echo "<div id='notFound'>";
        echo "<h1>Firefox n'arrive pas a charg&eacute; la vid&eacute;o<h1>";
        errorCSS();
        exit();
    }

    if(isset($_GET['id']) && isset($_GET['ep'])) {
        $foundsub = true;
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
        

        $request_video = "SELECT video_path, sub_path, audio_path FROM episodes WHERE episode_number= ? AND id= ? ";
        
        $stmt_video = $mysqli->prepare($request_video);
        $stmt_video->bind_param('si', $_GET['ep'], $_GET['id']);
        $stmt_video->execute();

        $stmt_video->bind_result($video, $sub, $audio);
        $stmt_video->fetch();
        $stmt_video->close();
        
        $mysqli->close();

        
        if ($video == "not found") {
            notFound();
        }
        if (str_contains($sub, ':')) {
            $subs = explode('|', $sub);
            $sublink = explode(':', $subs[0]);
            if (array_key_exists(1, $sublink)) {
                $sublink = $sublink[1];
            } else {
                $foundsub = false;
            }
        } else {
            $foundsub = false;
        }

        if(str_contains($audio, ':')) {
            $audioArray = explode('|', $audio);
        } else {
            notFound();
        }
?>


<script>
var fontsList = [
    "/JavascriptSubtitlesOctopus/assets/js/default.woff2", //trebuchet MS
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
<div id="video">
<?php
    echo '<video id="player" poster="'. $poster[0] .'" class="plyr__video-embed" playsinline controls preload="auto" data-setup="{}">';
    echo '<source src="' . $url . htmlspecialchars($video, ENT_QUOTES) . '" type="video/mp4">';
        if($foundsub) {
            for($i=0; $i < count($subs); $i++) {
                $lang = explode(':', $subs[$i]);
                if ($i == 0) {
                    echo '<track src="" srclang="'. $lang[0] .'" label="'. $lang[0] .'" kind="captions" type="application/x-ass" default>'. PHP_EOL;
                } else {
                    echo '<track src="" srclang="'. $lang[0] .'" label="'. $lang[0] .'" kind="captions" type="application/x-ass">'. PHP_EOL;
                }
            }
            echo '<p class="vjs-no-js"> To view this video please enable JavaScript, and consider upgrading to a web browser that<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
            </video>';
        }
?>
</div>

<link rel="stylesheet" href="assets/css/plyr.css" />
<script src="assets/js/plyr.js"></script>
<script src="assets/js/subtitles-octopus.js"></script>
<script>
    function sleep(ms, callback) {
        setTimeout(callback, ms);
    }

    var dictionnaire = {};
    var audioDict = {};
    <?php
        if ($foundsub) {
            echo '//' . PHP_EOL;
            for($i=0; $i < count($subs); $i++) {
                $lang = explode(':', $subs[$i]);
                echo 'dictionnaire["' . $lang[0] . '"] = "' . $url . $lang[1] . '";' . PHP_EOL;
            }

            for($j=0; $j < count($audioArray); $j++) {
                $current = explode(':', $audioArray[$j]);
                echo 'audioDict["' . $current[0] . '"] = "' . $url . $current[1] . '";' . PHP_EOL;
            }

        } else {
            echo '// no subtitles found'. PHP_EOL;
        }
    
    ?>
    localStorageKey = "AnimeSquad-Plyr-Video-Player";
    localStorageAudioKey = "AnimeSquad-Audio-Player";

    var currentAudio = 'vo';
    var currentSubtitle = 'fr';
    
    if(audioDict[currentAudio] === undefined) {
        currentAudio = 'vo';
        currentSubtitle = 'fr';
    }

    if (dictionnaire[currentSubtitle] === undefined) {
        currentSubtitle = 'fr';
    }

    if (localStorage.getItem(localStorageKey) === null) {
        if (currentSubtitle == 'false') {
            localStorage.setItem(localStorageKey, JSON.stringify({"captions" : false, "language" : 'fr'}));
        }
        else {
            localStorage.setItem(localStorageKey, JSON.stringify({"captions" : true, "language" : currentSubtitle}));
        }

    } else if (currentSubtitle == 'false' && JSON.parse(localStorage.getItem(localStorageKey))["captions"]) {
        localStorage.setItem(localStorageKey, JSON.stringify({"captions" : false, "language" : "fr"}));

    } else if (JSON.parse(localStorage.getItem(localStorageKey))["language"] != currentSubtitle || (!JSON.parse(localStorage.getItem(localStorageKey))["captions"])) {
        localStorage.setItem(localStorageKey, JSON.stringify({"captions" : true, "language" : currentSubtitle}));     
    }

    if(localStorage.getItem(localStorageAudioKey) === null) {
        localStorage.setItem(localStorageAudioKey, JSON.stringify({"volume" : 1}));
    }

    const plyrOptions = {
        controls: ['rewind', 'play', 'fast-forward', 'progress', 'current-time', /*'mute', 'volume',*/ 'captions', 'settings', /*'pip',*/ 'airplay', 'download','fullscreen'],
        invertTime: false,
        settings: [ 'captions'/*, 'speed'*/],
        storage: { enabled: true, key: localStorageKey},
        speed: { selected: 1, options: [0.5, 1, 1.25, 1.5, 2] },
        urls: {download : '<?php echo $url . htmlspecialchars($video, ENT_QUOTES);}?>'},/*<?php  #echo "'download.php?animelink=" . urlencode($url . $video); if ($foundsub) { echo '&sublink=' . urlencode($url . $sublink); } echo '}, }?>*/
    };

    const player = new Plyr('.plyr__video-embed', plyrOptions);
    player.on('ready', function () {
        // sync check 
        // const intervalId = setInterval(syncCheck, 1500);

        // fetch(player.source).then(response => {
        //     if (response.ok) {
        //     console.log('Le document existe.');
        //     } else {
        //     console.log('Le document n\'existe pas ou est inaccessible.');
        //     }
        // })
        // .catch(error => {
        //     console.error('Une erreur s\'est produite lors de la requête.', error);
        // });

    });



    player.on('captionsenabled', (event) => {
        if (!window.octopusInstance) {
            var video = document.getElementById('player');
            
            try {
                var url = dictionnaire[player.media.textTracks[player.currentTrack]['language']];
            }
            catch(e) {
                var url = "not found";
            }
            var options = {
                video: video,
                subUrl: url,
                fonts: fontsList,
                //onReady: onReadyFunction,
                debug: false,
                workerUrl: '/JavascriptSubtitlesOctopus/assets/js/subtitles-octopus-worker.js'
            };
            window.octopusInstance = new SubtitlesOctopus(options);
        } else {
            var url = dictionnaire[player.media.textTracks[player.currentTrack]['language']];
            window.octopusInstance.setTrackByUrl(url);
        }

    });

    const captionsElements = document.querySelectorAll('[data-plyr="captions"]');
    captionsElements[0].style.display = "none";
    
    var audio = audio = new Audio(audioDict[currentAudio]);
    player.volume = 0;
    player.muted = true;
    audio.volume = JSON.parse(localStorage.getItem(localStorageAudioKey))["volume"];

    function changeAudioTrack(lang) {
        var paused = player.paused;
        if(!paused) {
            audio.pause();
            player.pause();
        }
        currentAudio = lang;
        audio.src = audioDict[currentAudio];
        audio.load();
        audio.currentTime = player.currentTime;
    }

    function genVolumeControls() {
        var divElement = document.createElement("div");
        divElement.setAttribute("class", "plyr__controls__item plyr__volume");

        var divContent = `
            <button id="buttonMute" type="button" class="plyr__control plyr__control">
                <svg id="buttonMuteMuted" class="icon--pressed"><use xlink:href="#plyr-muted"></use></svg>
                <svg id="buttonMuteUnMuted" class="icon--not-pressed"><use xlink:href="#plyr-volume"></use></svg>
                <span class="label--pressed plyr__sr-only">Unmute</span>
                <span class="label--not-pressed plyr__sr-only">Mute</span>
            </button>
            <input id="volumeInput" data-plyr="volume" type="range" min="0" max="1" step="0.05" value="1" autocomplete="off" role="slider" style="--value: 100%;">
        `;
        divElement.innerHTML = divContent;

        var flexContainer = document.querySelector(".plyr__controls");
        flexContainer.insertBefore(divElement, flexContainer.children[5]);
    }

    function genAudioTrackSettings(buttonElement) {
        buttonElement.setAttribute('type', 'button');
        buttonElement.setAttribute('class', 'plyr__control plyr__control--forward');
        // buttonElement.setAttribute('role', 'menuitem');
        buttonElement.setAttribute('aria-haspopup', 'true');

        var innerSpan = document.createElement('span');
        innerSpan.textContent = 'Audio';

        var nestedSpan = document.createElement('span');
        nestedSpan.setAttribute('class', 'plyr__menu__value');
        nestedSpan.textContent = currentAudio;

        innerSpan.appendChild(nestedSpan);

        buttonElement.appendChild(innerSpan);

        var flexContainer = document.querySelector('[role="menu"]');
        flexContainer.insertBefore(buttonElement, flexContainer.children[0]);

    }

    function genAudioTrackPanel(outerDiv, flexContainer) {
        
        outerDiv.setAttribute('id', 'plyr-settings-3000-audio');
        outerDiv.setAttribute('hidden', 'true');

        // Create the inner div element with role "menu"
        const innerDiv = document.createElement('div');
        innerDiv.setAttribute('role', 'menu');

        var languages = [];
        for(var key in audioDict) {
            languages.push({value: key, lang: key, badge: key.toUpperCase()});
        }

        // Create buttons for each language
        languages.forEach((language) => {
            const button = document.createElement('button');
            button.setAttribute('class', 'plyr__control audio__track');
            button.setAttribute('value', language.value);

            const span1 = document.createElement('span');
            span1.textContent = language.lang + ' ';

            const nestedSpan = document.createElement('span');
            nestedSpan.setAttribute('class', 'plyr__menu__value');

            const span2 = document.createElement('span');
            span2.setAttribute('class', 'plyr__badge');
            span2.textContent = language.badge;

            nestedSpan.appendChild(span2);
            span1.appendChild(nestedSpan);
            button.appendChild(span1);

            innerDiv.appendChild(button);
    });

    outerDiv.appendChild(innerDiv);

    // document.body.appendChild(outerDiv);
    flexContainer.appendChild(outerDiv);
    }

    genVolumeControls();
    var audioButtonElement = document.createElement('button');
    genAudioTrackSettings(audioButtonElement);
    var audioTrackPanel = document.createElement('div');
    var settingsPanel = document.querySelector('.plyr__menu__container').firstElementChild;
    genAudioTrackPanel(audioTrackPanel, settingsPanel);

    audioButtonElement.addEventListener("click", function() {
        audioTrackPanel.hidden = !audioTrackPanel.hidden;
        for(var current of settingsPanel.childNodes) {
            if(current.id.includes("home")) {
                if(current.getAttribute("hidden") == "true") {
                    current.setAttribute("hidden", "false");
                } else {
                    current.setAttribute("hidden", "true");
                }
                break;
            }
        }
    })

    var audioTrackPanelButtons = document.querySelectorAll('[class="plyr__control audio__track"]');
    audioTrackPanelButtons.forEach((button) => {
        button.addEventListener("click", function() {
            if(currentAudio != button.value) {
                changeAudioTrack(button.value);
            }

            audioTrackPanel.hidden = !audioTrackPanel.hidden;
            audioButtonElement.firstElementChild.firstElementChild.textContent = button.value;
            for(var current of settingsPanel.childNodes) {
                if(current.id.includes("home")) {
                    current.removeAttribute("hidden");
                    break;
                }
            }
        })
    })

    var muted = false;
    if(audio.volume == 0) {
        muted = true;
    }
    var inputVolume = document.getElementById("volumeInput");
    var svgMuted = document.getElementById("buttonMuteMuted");
    var svgUnMuted = document.getElementById("buttonMuteUnMuted");

    function changeAudioVolume(value) {
        if(muted) {
            svgMuted.classList.value = 'icon--pressed';
            svgUnMuted.classList.value = 'icon--not-pressed';
            muted = false;
        }
        inputVolume.style.setProperty("--value", value*100 + "%");
        audio.volume = value;
        inputVolume.value = value.toString();
        if(value == 0) {
            svgMuted.classList.value = 'icon--not-pressed';
            svgUnMuted.classList.value = 'icon--pressed';
            muted = true;
        } else {
            localStorage.setItem(localStorageAudioKey, JSON.stringify({"volume" : value}));
        }
    }
    changeAudioVolume(audio.volume);

    inputVolume.addEventListener("input", function(event) {
        changeAudioVolume(event.target.value);
    });

    var buttonMute = document.getElementById("buttonMute");
    buttonMute.addEventListener("click", function() {
        if(muted) {
            changeAudioVolume(JSON.parse(localStorage.getItem(localStorageAudioKey))["volume"]);
        } else {
            changeAudioVolume(0);
        }
    });

    player.on('languagechange', (event) => {
        if(window.octopusInstance) {
            var url = dictionnaire[player.media.textTracks[player.currentTrack]['language']];
            window.octopusInstance.setTrackByUrl(url);   
        }
    });

    player.on('captionsdisabled', (event) => {
        if (window.octopusInstance) {
            window.octopusInstance.freeTrack();
        }
    });

    player.on('enterfullscreen', (event) => {
        console.log('enterfullscreen');
        window.octopusInstance.render(window.octopusInstance.lastRenderTime)
    });

    player.on('exitfullscreen', function (event) {
        console.log('exitfullscreen');
        window.octopusInstance.render(window.octopusInstance.lastRenderTime)
    });

    player.on('pause', function (event) {
        if (document.hidden) {
            player.play();
        }
        else {
            console.log('pause');
            audio.pause();
        }
    });

    player.on('playing', function (event) {
        console.log('playing');
        audio.play();
        audio.currentTime = player.currentTime;
    });

    player.on('waiting', function () {
        console.log('waiting');
        audio.pause();
    });

    player.on('seeked', function () {
        console.log('seeked');
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
            if(audio.volume <= 0.9) {
                changeAudioVolume(Math.round((audio.volume + 0.1)*10)/10);
            }
        }
        
        else if (event.key === "ArrowDown") { //volum downcrease
            if(audio.volume >= 0.1) {
                changeAudioVolume(Math.round((audio.volume - 0.1)*10)/10);
            }
        }    

    });


</script>