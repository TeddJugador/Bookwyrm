/*  Munashe Madziwanyika - g23M8754
    Theodore Masi - g23M7028
    Kyle Nkomo - g23N8653
    Keith Dube - g23D5910 
*/

// slider animation
const slides = document.querySelectorAll(".slides img");
let i = 0;
let intervalId = null;

document.addEventListener("DOMContentLoaded",initializeSlider);

//begin slideshow
function initializeSlider(){
    if(slides.length>0){
        slides[i].classList.add("displaySlide");
        intervalId = setInterval(nextSlide,5000);
    }
}


//show a slide at the current index in the node list of slides
function showSlide(index){
    if(index>=slides.length){
        i = 0;
    }
    else if(index<0){
        i = slides.length-1;
    }

    //move from old slide to new slide
    slides.forEach(slide =>{
        slide.classList.remove("displaySlide");
    });
    slides[i].classList.add("displaySlide");


}

// move to previous slide
function prevSlide(){
    i--;
    showSlide(i);
    clearInterval(intervalId);
}


//move to next slide
function nextSlide(){
    i++;
    showSlide(i);
}

//makes scrolling up the screen smooth
function toTop(){
const topButton = document.getElementById("toTop");

topButton.addEventListener("click", () =>{   //creates an event for when the button is clicked.
    window.scrollTo({
        top: 0, //scrolls to the top of the screen.
        behavior: "smooth"
    });
});
}
    document.addEventListener("DOMContentLoaded",toTgit);