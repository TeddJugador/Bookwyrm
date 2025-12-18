/*  Munashe Madziwanyika - g23M8754
    Theodore Masi - g23M7028
    Kyle Nkomo - g23N8653
    Keith Dube - g23D5910 
*/

document.getElementById('review-form').onsubmit = setRatingValue;

//get clicked star
const stars = document.querySelectorAll('.ratings span');
//add event listener to each star
for(let star of stars){
  star.addEventListener('click', function(){
    clearReset();
    highlightStars(star);
  });
}


function highlightStars(upToStar){
  if(upToStar.getAttribute('data-clicked')=='true'&& upToStar.getAttribute('selected')=='true'){
    clearStars();
  }
  else{
    for(let mystar of stars){
      if(mystar.getAttribute('id')== upToStar.getAttribute('id')){
        mystar.setAttribute('selected', 'true');
      }
      else{
        mystar.removeAttribute('selected');
      }
      if(mystar.getAttribute('id')<= upToStar.getAttribute('id')){
        mystar.setAttribute('data-clicked', 'true');
      }
      else{
        mystar.setAttribute('data-clicked', 'false');    
      }
    }
  }
}

function clearStars(){
  for(let thestar of stars){
    thestar.setAttribute('data-clicked', 'false');
    thestar.setAttribute('data-reset', 'true');
    thestar.removeAttribute('selected');
  }
}

function clearReset(){
  for(let thestar of stars){
    thestar.removeAttribute('data-reset');
  }
}

//set rating input value
function setRatingValue(){
  //find the selected star
  const rating = document.getElementById('rating-value');
  let rated = false
  for(let s of stars){
    if(s.getAttribute('selected')=='true'){
      rating.value = Number(s.getAttribute('data-value'));
      rated= true;
      break;
    }
  }
  //if no star is selected, set rating to 0   
  if(!rated){
    rating.value = 0;
  }
  //window.alert("You rated this manga " + rating.value + " stars."); //DO THIS FROM PHP
  return true; //allow form submission
}
