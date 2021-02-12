let lockscreenDuration = 180000;
let lockscreenTimeout;

initLockscreenTimer();

$(function() {
    $(window).on("wheel mousemove click keyup keypress scroll touch touchmove", function (event) {
        clearTimeout(lockscreenTimeout);
        initLockscreenTimer();
    });
});

function initLockscreenTimer(){
    lockscreenTimeout = setTimeout(function (){
        $(".lockscreen").fadeIn();
    }, lockscreenDuration);
}