(function( $ ){
    $.fn.animateCSS = function(animation) {
        let e = this;
        let prefix = "animate__";
        new Promise((resolve, reject) => {
            const animationName = `${prefix}${animation}`;
            const node = $(e).get(0);
            node.classList.add(`${prefix}animated`, animationName);
            function handleAnimationEnd(event) {
                event.stopPropagation();
                node.classList.remove(`${prefix}animated`, animationName);
                resolve('Animation ended');
            }
            node.addEventListener("animationend", handleAnimationEnd, {once: true});
        });
        return this;
    }; 
 })( jQuery );