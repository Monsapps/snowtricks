/**
 * Resize hero height div to fill screen
 * @param {*} height 
 */
function resizeHero(height) {
    let heroDiv = document.querySelector("#main");
    let newHeight = height * 0.90;
    heroDiv.style.height = newHeight + "px";
}

function responsiveHeight(percent, height) {
    let newHeight = height * percent;
    return newHeight + "px";
}

/*window.onload = () => {
    var windowHeight = window.innerHeight;
    resizeHero(windowHeight);
    window.addEventListener('resize', function() {
        windowHeight = window.innerHeight;
        resizeHero(windowHeight);
    });

}*/
