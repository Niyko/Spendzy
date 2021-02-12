$(function() {
    onChildTreeChange();
});

function onChildTreeChange(){
    let treeTarget = document.querySelector("body");
    let treeConfig = { subtree: true, childList: true};
    let treeObserver = new MutationObserver(function() {
        treeObserver.disconnect();
        initDatepicker();
        if(getCookie("isolated-value")=="true") isolateValues(true);
        if(typeof onPageChildTreeChange!=="undefined") onPageChildTreeChange();
        treeObserver.observe(treeTarget, treeConfig);
    });
    treeObserver.observe(treeTarget, treeConfig);
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

function isolateValues(status){
    if(status){
        $(".isolated-value").each(function() {
            $(this).addClass("isolated-hidden-value");
            $(this).click(function() {
                $(this).removeClass("isolated-hidden-value");
            });
        });
        setCookie("isolated-value", "true");
        $(".isolated-value-btn").find("span").html("remove_moderator");
    }
    else {
        $(".isolated-value").each(function() {
            $(this).removeClass("isolated-hidden-value");
        });
        setCookie("isolated-value", "false");
        $(".isolated-value-btn").find("span").html("shield");
    }
}

function toggleSearchBox(status){
    if(status){
        $(".nav-bar-search-box").addClass("active");
        $(".nav-bar-search-input").focus();
    }
    else {
        $(".nav-bar-search-box").removeClass("active");
        $(".nav-bar-search-input").val("");
        searchOnList();
    }
}

function searchOnList(){
    let searchTerm = $(".nav-bar-search-input").val().toLowerCase();
    $(".checklist-item").each(function() {
        let searchData = $(this).find(".checklist-title").val();
        if(searchData.toLowerCase().indexOf(searchTerm)<0) $(this).addClass("checklist-item-search-hide");
        else $(this).removeClass("checklist-item-search-hide");
    });
}