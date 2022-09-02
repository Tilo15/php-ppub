// @license magnet:?xt=urn:btih:1f739d935676111cfff4b4693e3816e664797050&dn=gpl-3.0.txt GPL-v3-or-Later


function setup_playback(description) {
    document.getElementById("no-script").classList.remove("noscript");
    document.getElementById("controls").classList.add("javascript");

    video_manifest = description;
    console.log(description);
    player = document.getElementById("player");

    player.addEventListener("pause", (event) => playStateChanged(false));
    player.addEventListener("playing", (event) => playStateChanged(true));

    filteredOptions = filterOptions(description.entries);
    if(filteredOptions.length == 0){
        player.src = "invalid://invalid";
        document.getElementById("unplayable-modal").showModal();
        return;
    }

    player.src = filteredOptions[0].path;

    qualitySelector = document.getElementById("quality-selector");
    filteredOptions.forEach(option => {
        opt = document.createElement("option")
        opt.value = option.path;
        opt.innerHTML = option.label;
        qualitySelector.appendChild(opt);
    });

    startRes = findOptimalOptionForScreen(filteredOptions);
    qualitySelector.value = startRes.path
    qualitySelected();
}

function qualitySelected() {
    qualitySelector = document.getElementById("quality-selector");
    player = document.getElementById("player");
    
    paused = player.paused;
    time = player.currentTime;
    player.src = qualitySelector.value;
    player.load();

    player.currentTime = time;
    if(!paused) {
        player.play();
    }
}

function filterOptions(options) {
    player = document.getElementById("player");
    return options.filter(o => o.type === "video" && player.canPlayType(`${o.mimetype}; codecs="${o.metadata.codecs}"`));
}

function findOptimalOptionForScreen(options) {
    screenSize = Math.max(window.screen.width, window.screen.height);
    screenSize = screenSize * window.devicePixelRatio
    filtered = options.filter(o => o.type === "video" && Math.max(o.metadata.size.split("x")[0], o.metadata.size.split("x")[1]) <= screenSize);
    console.log(filtered);
    if(filtered.length == 0){
        return options[options.length - 1];
    }
    return filtered[0];
}

function downloadVideo() {
    document.getElementById("download-modal").showModal();
}

function showInfo() {
    document.getElementById("info-modal").showModal();
}

function shareVideo() {
    document.getElementById("share-modal").showModal();
}

function playStateChanged(playing) {
    controls = document.getElementById("controls")
    if(playing) {
        controls.classList.remove("paused")
    }
    else {
        controls.classList.add("paused")
    }
}


var speedTestStart;
var speed = 0;

function startSpeedTest() {
    speedTestStart = Date.now();
}

function speedTestProgress(transferred) {
    loadTime = Date.now() - speedTestStart;
    if(loadTime < 1) {
        loadTime = 1;
    }
    speed = transferred / loadTime;
}

// @license-end