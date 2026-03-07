var afeb_elementor_players_data = [], afeb_players_timer_countdowns = [];

function afebSetCookie(name, value, exdays = 30) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function adebDeleteCookie(name, exdays = 35) {
    const d = new Date();
    d.setTime(d.getTime() + (-exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = name + "=;" + expires + ";path=/";
}

function afebGetCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function afebCheckCookie(name) {
    let data = afebGetCookie(name);
    if (data != "") {
        return true;
    }

    return false;
}

jQuery(document).ready(function ($) {
    let playerOpt = {
        src: [''],
        preload: false,
        autoplay: false,
        loop: false,
        html5: true,
    },
        audioPlayer = new Howl(playerOpt),
        countdown = null,
        intervals = [],
        playerElements = $('body').find('.afeb-sound-player'),
        playerBtnElements = playerElements.find('.afeb-sound-btn-playing'),
        playerInterval;

    if (audioPlayer) {
        audioPlayer.stop();
    }

    $(window).bind('beforeunload', function () {
        adebDeleteCookie('audio_src');
        adebDeleteCookie('audio_hash');
    });

    if ($('.afeb-sound-player.afeb-has-loader').length > 0) {
        setTimeout(function () {
            $('.afeb-sound-player.afeb-has-loader').removeClass('afeb-has-loader');
        }, 2000);
    }

    if (playerBtnElements.length > 0) {
        playerBtnElements.each(function (i) {
            let el = $(this),
                pid = el.data('player-id'),
                btnHash = el.data('hash'),
                saveAudio = afeb_elementor_players_data[`player_${pid}`] ? (afeb_elementor_players_data[`player_${pid}`].save_audio == true) : false;
            if (!saveAudio) {
                afebSetCookie(`player_${pid}_audio_` + btnHash, 0);
            }
        });
    }

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-play.afeb-in-playlist', function (e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.hasClass('afeb-in-playlist')) {
            $('body').find('.afeb-sound-btn-playing').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
            if (audioPlayer) {
                audioPlayer.stop();
                if (playerInterval) {
                    playerInterval.stop();
                }
                if (countdown) {
                    countdown.stop();
                }
            }
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-play', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            audioTitle = btn.data('title'),
            audioSubTitle = btn.data('subtitle'),
            audioCover = btn.data('cover'),
            audioLen = btn.data('length'),
            audioPriDur = btn.data('duration'),
            soundWrap = $('body').find(`.afeb-sound-player-${playerID}`),
            rangeInput = soundWrap.find('.afeb-sound-timer-line input[type="range"]'),
            saveAudio = true,
            audio = btn.data('audio'),
            audioHashName, audioSeek, audioCurrentSeek, audioCurrentDur, progressLine, audioDur;

        audioPlayer.off();

        if (playerInterval) {
            playerInterval.stop();
        }
        if (countdown) {
            countdown.stop();
        }

        if (audio && audio !== undefined) {
            audioPlayer.stop();

            $('body').find('.afeb-sound-player .afeb-sound-btn-pause').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
            $('body').find('.afeb-sound-player.afeb-is-playing').removeClass('afeb-is-playing');
            soundWrap.addClass('afeb-is-playing');
            soundWrap.find('.afeb-sound-timer-wrap').attr('id', `afeb-timer-progress-${playerID}-` + btn.data('hash'));

            if ($(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-down`).text() == '00:00' && countdown !== null) {
                countdown.init();
            }

            soundWrap.find('.afeb-sound-player-content .afeb-sound-title').text(audioTitle);
            soundWrap.find('.afeb-sound-player-content .afeb-sound-subtitle').text(audioSubTitle);
            soundWrap.find('.afeb-sound-player-content .afeb-sound-btn-download').attr('href', audio);

            soundWrap.find('.afeb-player-share-buttons-content a, .afeb-sound-player-content .afeb-sound-btn-playing, .afeb-sound-player-content .afeb-sound-btn-sec-prev, .afeb-sound-player-content .afeb-sound-btn-sec-next, .afeb-sound-player-content .afeb-sound-player-range, .afeb-sound-player-content .afeb-sound-timer-line .afeb-stl').data({
                'title': audioTitle,
                'subtitle': audioSubTitle,
                'cover': audioCover,
                'length': audioLen,
                'duration': audioPriDur,
                'audio': audio,
                'hash': btn.data('hash'),
            });

            if (btn.hasClass('afeb-in-playlist')) {
                let itemID = btn.data('item-id'),
                    playListItem = soundWrap.find(`.afeb-sound-player-playlist .afeb-sound-playlist-item`),
                    playListActiveItem = soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item.afeb-active');
                $('body').find('.afeb-sound-player .afeb-sound-player-playlist .afeb-sound-playlist-item').removeClass('afeb-active');
                $('body').find('.afeb-sound-player .afeb-sound-player-playlist .afeb-sound-playlist-item-' + itemID).addClass('afeb-active');
                if (playListActiveItem.length > 0) {
                    if (playListItem.last().hasClass('afeb-active')) {
                        soundWrap.find(`.afeb-sound-btn-next`).addClass('afeb-btn-disabled');
                    } else {
                        soundWrap.find(`.afeb-sound-btn-next`).removeClass('afeb-btn-disabled');
                    }

                    soundWrap.find(`.afeb-sound-btn-prev`).removeClass('afeb-btn-disabled');

                    if (playListItem.first().hasClass('afeb-active')) {
                        soundWrap.find(`.afeb-sound-btn-prev`).addClass('afeb-btn-disabled');
                    }
                }

                soundWrap.find('.afeb-sound-player-content .afeb-sound-btn-playing.afeb-in-content').removeClass('afeb-sound-btn-play').addClass('afeb-sound-btn-pause');
            } else {
                if (soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item').length > 0) {
                    if (soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item-' + btn.data('hash'))) {
                        soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item-' + btn.data('hash')).first().addClass('afeb-active');
                    } else if (!soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item.afeb-active').length) {
                        soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item').first().addClass('afeb-active');
                    }
                    $('body').find('.afeb-sound-player .afeb-sound-player-playlist .afeb-sound-btn-playing').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
                    soundWrap.find('.afeb-sound-player-content .afeb-sound-btn-playing').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
                    soundWrap.find(`.afeb-sound-player-playlist .afeb-sound-playlist-item .afeb-sound-btn-play[data-hash="${btn.data('hash')}"]`).first().addClass('afeb-sound-btn-pause').removeClass('afeb-sound-btn-play');
                }
            }

            if (playerInterval !== undefined) {
                playerInterval.init();
            }

            intervals = [];
            soundWrap.find('.afeb-sound-timer-wrap').trigger('change');
            audioHashName = `player_${playerID}_audio_` + btn.data('hash');
            progressLine = soundWrap.find(`#afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-progress`);

            let is_new = afebGetCookie('audio_end') == 'true' || afebGetCookie('player_id') != playerID || afebGetCookie('audio_src') != audio && afebGetCookie('audio_hash') != btn.data('hash');

            if (is_new) {
                audioCurrentSeek = audioPlayer.seek();
                audioPlayer.unload();
                audioPlayer._src = audio;
                audioPlayer.load();
            }

            audioPlayer.on();

            afebSetCookie('audio_end', false);
            afebSetCookie('player_id', playerID);
            afebSetCookie('audio_src', audio);
            afebSetCookie('audio_hash', btn.data('hash'));
            audioCurrentDur = audioPlayer.duration();
            audioSeek = afebGetCookie(audioHashName);
            audioSeek = !isNaN(audioSeek) && audioSeek !== undefined ? audioSeek : audioCurrentSeek;
            if (saveAudio && audioSeek) {
                audioPlayer.seek(audioSeek);
            } else if (audioSeek > 0) {
                audioPlayer.seek(audioSeek);
            }
            afebSetCookie(audioHashName, audioSeek);

            audioPlayer.play();

            let play_callback = function () {
                audioPriDur = audioPriDur && audioPriDur !== undefined ? audioPriDur : audioPlayer.duration();
                btn.removeClass('afeb-sound-btn-play').addClass('afeb-sound-btn-pause');

                let timeDownEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-down`),
                    timeDefEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-default`);

                playerInterval = eaSetInterval(function () {
                    audioCurrentSeek = audioPlayer.seek();
                    afebSetCookie(audioHashName, audioPlayer.seek());
                    audioSeek = afebGetCookie(audioHashName);
                    if (audioSeek <= 0) {
                        return false;
                    }
                    audioDur = afebGetCookie(`player_${playerID}_audio_duration_` + btn.data('hash'));
                    progressLineWidth = parseInt(progressLine.get(0).style.width);
                    progressLine.parent().parent().find('.afeb-sound-timer-line .afeb-stl').css('width', ((afebGetCookie(audioHashName) / audioDur) * 100) + '%');
                    progressLine.css('width', ((afebGetCookie(audioHashName) / audioDur) * 100) + '%');
                    rangeInput.val((afebGetCookie(audioHashName) / audioDur) * 100);
                    afebSetCookie(`player_${playerID}_audio_duration_` + btn.data('hash'), audioPriDur);

                    setTimeout(function () {
                        let player = $('body').find(`.afeb-sound-player-${playerID}`);
                        let playbtn = player.find('.afeb-sound-btn-play');

                        if (player.length == 0) audioPlayer.stop();
                        if (playbtn.length > 0 && player.find('.afeb-sound-timer-down').text().trim() == '00:00') playbtn.click();
                        audioDur = afebGetCookie(`player_${playerID}_audio_duration_` + btn.data('hash'));
                        if (afeb_players_timer_countdowns && afeb_players_timer_countdowns !== undefined) {
                            if (afeb_players_timer_countdowns['audio_' + playerID] !== undefined) {
                                clearInterval(afeb_players_timer_countdowns['audio_' + playerID]);
                            }
                        }
                        countdown = new eaMSecCountdown(timeDownEl, (audioDur - audioSeek), timeDefEl, audioDur, playerID);
                        if (countdown !== null) {
                            countdown.start();
                            afebSetCookie('player_countdown_id_' + playerID, countdown.timer['audio_' + playerID]);
                        }
                    }, 320);

                    if (progressLineWidth == 99 || progressLineWidth == 100) {
                        btn.removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
                        $('body').find('.afeb-sound-player .afeb-sound-player-playlist .afeb-sound-btn-playing, .afeb-sound-player .afeb-sound-player-content .afeb-sound-btn-playing').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
                        adebDeleteCookie(audioHashName);
                        adebDeleteCookie(`player_${playerID}_audio_duration_` + btn.data('hash'));
                        progressLine.parent().parent().find('.afeb-sound-timer-line .afeb-stl').css('width', '0%');
                        progressLine.css('width', '0%');
                        rangeInput.val(0);
                        audioPlayer.stop();
                        audioPlayer.off();
                        adebDeleteCookie('audio_src');
                        adebDeleteCookie('audio_hash');
                        afebSetCookie('audio_end', true);
                        if (countdown !== null) {
                            countdown.init();
                        }
                        if (playerInterval) {
                            playerInterval.init();
                        }
                        audioPlayer.unload();
                    }
                }, 300, playerID);

                playerInterval.start();

                if (audioCover && audioCover !== undefined) {
                    soundWrap.find('.afeb-sound-pri-cover').css('background-image', `url(${audioCover})`);
                }

                if (audioLen && audioLen !== undefined) {
                    soundWrap.find('.afeb-sound-timer-default').text(audioLen);
                }

                afebSetCookie('player_interval_id_' + playerID, playerInterval.timer['interval_' + playerID]);
            }

            if (is_new) {
                audioPlayer.on('play', play_callback);
            } else {
                audioPlayer.once('play', play_callback);
            }

            audioPlayer.on('playerror', function (e) {
                btn.addClass('afeb-shake-animation');
                setTimeout(() => {
                    btn.removeClass('afeb-shake-animation');
                }, 1000);
            });
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-pause', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            audioHash = btn.data('hash'),
            soundWrap = $('body').find(`.afeb-sound-player-${playerID}`);
        audioPlayer.pause();
        btn.removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
        $('body').find('.afeb-sound-player.afeb-is-playing').removeClass('afeb-is-playing');
        if (!btn.hasClass('afeb-in-content')) {
            soundWrap.find('.afeb-sound-player-content .afeb-sound-btn-playing').removeClass('afeb-sound-btn-pause').addClass('afeb-sound-btn-play');
        }
        if (btn.hasClass('afeb-in-content')) {
            if (!soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item').length) {
                soundWrap.find('.afeb-sound-player-playlist .afeb-sound-playlist-item').removeClass('afeb-active');
                // soundWrap.find('.sound-player-playlist .sound-playlist-item').first().addClass('active');
            }
        }
        if (btn.hasClass('afeb-in-content')) {
            $('body').find(`.afeb-sound-player-playlist .afeb-sound-playlist-item.afeb-active .afeb-sound-btn-pause[data-hash="${audioHash}"]`).addClass('afeb-sound-btn-play').removeClass('afeb-sound-btn-pause');
        }
        if (countdown !== null) {
            countdown.stop();
        }
        if (playerInterval) {
            playerInterval.init();
        }
    });

    $('body').on('input', '.afeb-sound-player .afeb-sound-timer-line input[type="range"]', function (e) {
        let input = $(this),
            playerID = input.data('player-id'),
            soundWrap = $('body').find(`.afeb-sound-player-${playerID}`),
            progressLine = soundWrap.find(`#afeb-timer-progress-${playerID}-${input.data('hash')} .afeb-sound-timer-progress`),
            duration = audioPlayer.duration(),
            audioHashName = `player_${playerID}_audio_` + input.data('hash'),
            currentTimeInSeconds = ((input.val() * duration) / 100).toFixed(2);
        if (((currentTimeInSeconds / duration) * 100) > 99) {
            return false;
        }
        if (audioPlayer.playing()) {
            playerInterval.stop();
            countdown.stop();
            audioPlayer.pause();
        }
        afebSetCookie(audioHashName, currentTimeInSeconds);
        audioPlayer.seek(currentTimeInSeconds);
        progressLine.parent().parent().find('.afeb-sound-timer-line .afeb-stl').css('width', ((currentTimeInSeconds / duration) * 100) + '%');
        progressLine.css('width', ((currentTimeInSeconds / duration) * 100) + '%');
    });

    $('body').on('change', '.afeb-sound-player .afeb-sound-timer-line input[type="range"]', function (e) {
        let input = $(this),
            duration = audioPlayer.duration(),
            currentTimeInSeconds = ((input.val() * duration) / 100).toFixed(2),
            playerID = input.data('player-id');
        if (((currentTimeInSeconds / duration) * 100) > 99) {
            return false;
        }
        if ($('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-content .afeb-sound-btn-pause`).length > 0) {
            audioPlayer.play();
            playerInterval.start();
            countdown.start();
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-sec-next', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            sound = audioPlayer.pause()._sounds[0],
            currentSeek = sound._seek,
            forwardTo = currentSeek + 30,
            timeDownEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-down`),
            timeDefEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-default`),
            audioHashName = `player_${playerID}_audio_` + btn.data('hash'),
            audioDurHashName = `player_${playerID}_audio_sec_next` + btn.data('hash');

        afebSetCookie(audioHashName, forwardTo);
        audioPlayerForwardTo(audioPlayer, forwardTo);
        if ($('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-content .afeb-sound-btn-pause`).length > 0) {
            audioPlayer.play();
            countdown.start();
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-sec-prev', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            sound = audioPlayer.pause()._sounds[0],
            currentSeek = sound._seek,
            forwardTo = currentSeek - 30,
            timeDownEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-down`),
            timeDefEl = document.querySelector(`.afeb-sound-player-${playerID} #afeb-timer-progress-${playerID}-${btn.data('hash')} .afeb-sound-timer-default`),
            audioHashName = `player_${playerID}_audio_` + btn.data('hash'),
            audioDurHashName = `player_${playerID}_audio_sec_prev` + btn.data('hash');
        afebSetCookie(audioHashName, forwardTo);
        audioPlayerForwardTo(audioPlayer, forwardTo);
        if ($('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-content .afeb-sound-btn-pause`).length > 0) {
            audioPlayer.play();
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-share', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            itemID = btn.data('item-id'), wrap;
        if (btn.hasClass('afeb-sound-btn-share-in-playlist')) {
            wrap = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-playlist .afeb-sound-player-share-buttons-` + itemID).first();
        } else {
            wrap = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-content .afeb-sound-player-share-buttons`);
            wrap.parent().addClass('afeb-opened-overlay');
        }
        wrap.slideToggle();
    });

    $('body').on('click', '.afeb-sound-player .afeb-share-close', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            itemID = btn.data('item-id'),
            wrap = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-content`);
        wrap.removeClass('afeb-opened-overlay');
        btn.parent().parent().slideUp();
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-player-content .afeb-player-share-buttons-content a', function (e) {
        let btn = $(this),
            pattern = btn.data('pattern'),
            title = btn.data('title'),
            subTitle = btn.data('subtitle'),
            audio = btn.data('audio'), url;
        if (pattern !== undefined && title !== undefined && subTitle !== undefined && audio !== undefined) {
            e.preventDefault();
            url = pattern.replace('{{url}}', audio);
            url = url.replace('{{title}}', title + ' / ' + subTitle);
            btn.attr('href', url);
            window.open(url, '_blank');
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-playlist', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            wrap = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-playlist`);
        wrap.slideToggle();
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-next', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            player = $('body').find(`.afeb-sound-player-${playerID}`),
            playlist = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-playlist`), playBtn;
        if (!playlist.find('.afeb-sound-playlist-item').last().hasClass('afeb-active')) {
            if (playlist.find('.afeb-sound-playlist-item.afeb-active').length > 0) {
                playlist.find('.afeb-sound-playlist-item.afeb-active').next().addClass('afeb-active');
                playlist.find('.afeb-sound-playlist-item.afeb-active').prev().removeClass('afeb-active');
            } else {
                playlist.find('.afeb-sound-playlist-item').first().addClass('afeb-active');
            }
        }
        if (playlist.find('.afeb-sound-playlist-item.afeb-active').length > 0) {
            player.find('.afeb-sound-btn-prev').removeClass('afeb-btn-disabled');
        }
        if (playlist.find('.afeb-sound-playlist-item').last().hasClass('afeb-active')) {
            btn.addClass('afeb-btn-disabled');
        }
        if (playlist.find('.afeb-sound-playlist-item.afeb-active .afeb-sound-btn-playing').length > 0) {
            playlist.find('.afeb-sound-playlist-item .afeb-sound-btn-pause').trigger('click');
            adebDeleteCookie('audio_src');
            adebDeleteCookie('audio_hash');
            playlist.find('.afeb-sound-playlist-item.afeb-active .afeb-sound-btn-playing').trigger('click');
        }
    });

    $('body').on('click', '.afeb-sound-player .afeb-sound-btn-prev', function (e) {
        e.preventDefault();
        let btn = $(this),
            playerID = btn.data('player-id'),
            player = $('body').find(`.afeb-sound-player-${playerID}`),
            playlist = $('body').find(`.afeb-sound-player-${playerID} .afeb-sound-player-playlist`);
        if (!playlist.find('.afeb-sound-playlist-item').first().hasClass('afeb-active')) {
            if (playlist.find('.afeb-sound-playlist-item.afeb-active').length > 0) {
                playlist.find('.afeb-sound-playlist-item.afeb-active').prev().addClass('afeb-active');
                playlist.find('.afeb-sound-playlist-item.afeb-active').next().removeClass('afeb-active');
            } else {
                //playlist.find('.sound-playlist-item').last().addClass('active');
                /*btn.addClass('btn-disabled');
                 player.find('.sound-btn-next').removeClass('btn-disabled');
                 player.find('.sound-player-content .sound-btn-playing').trigger('click');*/
            }
        }
        if (playlist.find('.afeb-sound-playlist-item').first().hasClass('afeb-active')) {
            player.find('.afeb-sound-btn-next').removeClass('afeb-btn-disabled');
            playlist.find('.afeb-sound-playlist-item .afeb-sound-btn-pause').trigger('click');
            playlist.find('.afeb-sound-playlist-item.afeb-active .afeb-sound-btn-playing').trigger('click');
            player.find('.afeb-sound-btn-prev').addClass('afeb-btn-disabled');
            adebDeleteCookie('audio_src');
            adebDeleteCookie('audio_hash');
        } else {
            playlist.find('.afeb-sound-playlist-item .afeb-sound-btn-pause').trigger('click');
            if (playlist.find('.afeb-sound-playlist-item.active .afeb-sound-btn-playing').length > 0) {
                playlist.find('.afeb-sound-playlist-item.afeb-active .afeb-sound-btn-playing').trigger('click');
                adebDeleteCookie('audio_src');
                adebDeleteCookie('audio_hash');
            }
        }
    });

    $('body').on('input', '.afeb-sound-player-1 .afeb-sound-volume input[type="range"]', function () {
        let input = $(this),
            icon = input.parent().parent().find('i'),
            volumeValue = input.val(),
            volume = volumeValue / 100;

        if (volumeValue > 50) {
            icon.attr('class', 'fas fa-volume-up');
        } else {
            if (volumeValue <= 0) {
                icon.attr('class', 'fas fa-volume-mute');
            } else if (volumeValue <= 50) {
                icon.attr('class', 'fas fa-volume-down');
            }
        }

        audioPlayer.volume(volume);
    });

    $('body').on('click', '.afeb-sound-player-1 .afeb-sound-speed .afeb-sound-speed-box .afeb-speed-item', function () {
        let btn = $(this),
            rateValue = btn.data('value');

        btn.parent().find('.afeb-active').removeClass('afeb-active');
        btn.addClass('afeb-active');

        audioPlayer.rate(rateValue);
    });
});

function audioPlayerForwardTo(audioPlayer, forwardTo, countdownTime = 0, timeDownEl = null, timeDefEl = null, playerID = null) {
    let duration = audioPlayer.duration();
    if (forwardTo >= duration) {
        return;
    }
    audioPlayer.seek(forwardTo);
    if (timeDownEl != null && timeDefEl != null && playerID != null) {
        if (afeb_players_timer_countdowns && afeb_players_timer_countdowns !== undefined) {
            if (afeb_players_timer_countdowns['audio_' + playerID] !== undefined) {
                clearInterval(afeb_players_timer_countdowns['audio_' + playerID]);
                afeb_players_timer_countdowns['audio_' + playerID] = null;
            }
        }

        countdown = new eaMSecCountdown(timeDownEl, countdownTime, timeDefEl, duration, playerID);

        if (countdown !== null) {
            countdown.start();
        }
    }
}

function eaMSecCountdown(elem, seconds, defElem = null, defSeconds = 0, player_id = 0) {
    var that = {};

    that.elem = elem;
    that.player_id = player_id;
    that.defelem = defElem;
    that.defseconds = defSeconds;
    that.seconds = seconds;
    that.totalTime = seconds * 100;
    that.usedTime = 0;
    that.startTime = +new Date();
    that.timer = [];

    that.count = function () {
        that.usedTime = Math.floor((+new Date() - that.startTime) / 10);

        var tt = that.totalTime - that.usedTime;

        if (tt <= 0 || !jQuery(`.afeb-sound-player-${that.player_id} .afeb-sound-btn-pause`).length) {
            clearInterval(that.timer['audio_' + that.player_id]);
            clearInterval(afeb_players_timer_countdowns['audio_' + that.player_id]);
            afeb_players_timer_countdowns['audio_' + that.player_id] = null;
        } else {
            var di = Math.floor(tt / (24 * 60 * 60 * 100));
            var mi = Math.floor(tt / (60 * 100));
            var ss = Math.floor((tt - mi * 60 * 100) / 100);
            var ms = tt - Math.floor(tt / 100) * 100;
            if (that.elem) {
                that.elem.innerHTML = that.fillZero(mi) + ":" + that.fillZero(ss);

                if (that.defelem.innerHTML === '00:00') {
                    that.defelem.innerHTML = that.fillZero(mi) + ":" + that.fillZero(ss);
                }
            }
        }
    };

    that.init = function () {
        if (that.timer['audio_' + that.player_id]) {
            clearInterval(that.timer['audio_' + that.player_id]);
            clearInterval(afeb_players_timer_countdowns['audio_' + that.player_id]);
            afeb_players_timer_countdowns['audio_' + that.player_id] = null;
            that.elem.innerHTML = '00:00';
            that.totalTime = seconds * 100;
            that.defelem.innerHTML = '00:00';
            that.usedTime = 0;
            that.startTime = +new Date();
            that.timer = [];
            afeb_players_timer_countdowns = [];
        }
    };

    that.start = function () {
        if (!that.timer['audio_' + that.player_id]) {
            clearInterval(that.timer['audio_' + that.player_id]);
            clearInterval(afeb_players_timer_countdowns['audio_' + that.player_id]);
            afeb_players_timer_countdowns['audio_' + that.player_id] = null;
            that.timer = [];
            that.timer['audio_' + that.player_id] = setInterval(that.count, 1);
            afeb_players_timer_countdowns['audio_' + that.player_id] = that.timer['audio_' + that.player_id];
        }
    };

    that.stop = function () {
        if (that.timer['audio_' + that.player_id]) {
            clearInterval(that.timer['audio_' + that.player_id]);
            clearInterval(afeb_players_timer_countdowns['audio_' + that.player_id]);
            afeb_players_timer_countdowns['audio_' + that.player_id] = null;
        }
    };

    that.fillZero = function (num) {
        return num < 10 ? '0' + num : num;
    };

    return that;
}

function eaSetInterval(callback, time, interval_id = 0) {
    var that = {};

    that.interval_id = interval_id ? interval_id : 1;
    that.time = time;
    that.timer = [];

    that.count = callback;

    that.init = function () {
        if (that.timer['interval_' + that.interval_id]) {
            clearInterval(that.timer['interval_' + that.interval_id]);
            that.timer = [];
        }
    };

    that.start = function () {
        if (!that.timer['interval_' + that.interval_id]) {
            clearInterval(that.timer['interval_' + that.interval_id]);
            that.timer = [];
            that.timer['interval_' + that.interval_id] = setInterval(that.count, that.time);
        }
    };

    that.stop = function () {
        if (that.timer['interval_' + that.interval_id]) {
            clearInterval(that.timer['interval_' + that.interval_id]);
            that.timer = [];
        }
    };

    return that;
}

function eaClearPlayersIntervals() {
    jQuery('body').find('.afeb-sound-player').each(function () {
        if (afebCheckCookie('player_interval_id_' + jQuery(this).data('player-id'))) {
            clearInterval(afebGetCookie('player_interval_id_' + jQuery(this).data('player-id')));
        }
        if (afebCheckCookie('player_countdown_id_' + jQuery(this).data('player-id'))) {
            clearInterval(afebGetCookie('player_countdown_id_' + jQuery(this).data('player-id')));
        }
    });
}