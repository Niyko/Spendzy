let lockscreenTimer;

$(function() {
    initLockscreen();
    
    $("body").on({"touchstart" : function(){ 
        if($(".lockscreen").is(":hidden")) initLockscreen();
    }});

    $("body").mousemove(function(event){
        if($(".lockscreen").is(":hidden")) initLockscreen();
    });
});

function initLockscreen(){
    $(".lockscreen").hide();
    clearTimeout(lockscreenTimer);
    lockscreenTimer = setTimeout(function (){
        $(".lockscreen").fadeIn();
    }, 20000);
}

function toggleNavProgress(isShow, isRequestSpecfic=false, requestCode){
    if(isShow) {
        progressRequestCode = requestCode;
        $("#nav-bar-spinner").fadeIn();
    }
    else if(isRequestSpecfic==true){
        if(progressRequestCode==requestCode) $("#nav-bar-spinner").fadeOut();
    }
    else {
        $("#nav-bar-spinner").fadeOut();
    } 
}

function nFormatter(num, digits) {
    var si = [
        { value: 1, symbol: "" },
        { value: 1E3, symbol: "k" },
        { value: 1E6, symbol: "M" },
        { value: 1E9, symbol: "G" },
        { value: 1E12, symbol: "T" },
        { value: 1E15, symbol: "P" },
        { value: 1E18, symbol: "E" }
    ];
    var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    var i;
    for (i = si.length - 1; i > 0; i--) {
        if (num >= si[i].value) {
        break;
        }
    }
    return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
}

function onDatabaseError(){
    toggleErrorAlert(true);
}

function toggleErrorAlert(isShow){
    if(isShow) $('.error-container').addClass("error-active");
    else $('.error-container').removeClass("error-active");
}

function toggleSideBar(){
    if ($(".uk-offcanvas.uk-open")[0]){
        UIkit.offcanvas("#side-bar").hide();
    } else {
        UIkit.offcanvas("#side-bar").show();
    }
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}