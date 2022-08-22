
function setup_playback(description) {
    console.log(description);
    player = document.getElementById("player");

    filteredOptions = filterOptions(description.entries);
    if(filterOptions.length == 0){
        alert("Sorry, no available codecs are playable on your device/web browser.");
        return;
    }

    player.src = filteredOptions[0].relativePath;

    qualitySelector = document.getElementById("quality-selector");
    filteredOptions.forEach(option => {
        opt = document.createElement("option")
        opt.value = option.relativePath;
        opt.innerHTML = option.label;
        qualitySelector.appendChild(opt);
    });
}

function qualitySelected() {
    qualitySelector = document.getElementById("quality-selector");
    player = document.getElementById("player");

    time = player.currentTime;
    paused = player.paused;
    player.src = qualitySelector.value;
    player.currentTime = time;
    if(!paused) {
        player.play();
    }
}

function filterOptions(options) {
    player = document.getElementById("player");
    return options.filter(o => player.canPlayType(o.mimetypeWithCodec));
}