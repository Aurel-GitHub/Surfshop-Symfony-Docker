//transition page - Swup.JS
// comment temporally -> pb refresh cart add/remove quantity
// const swup = new Swup();
// in style.css
///** Effet Swup */
 /* .transition-fade {
    transition: 0.6s;
    opacity: 1;
  }
  
  html.is-animating .transition-fade {
    opacity: 0;
  } */
 

//scroll animation - ScrollReveal.JS
const sr = ScrollReveal()
sr.reveal('.reveal-1', {
    origin: 'left',
    distance: '50px',
    duration: 2000,
    delay: 0.5,
    reset: true,
})
sr.reveal('.reveal-2', {
    origin: 'left',
    distance: '50px',
    duration: 2000,
    delay: 1,
    reset: true
    
})
sr.reveal('.reveal-3', {
    origin: 'left',
    distance: '50px',
    duration: 2000,
    delay: 1.5,
    reset: true
    
})
sr.reveal('.reveal-4', {
    origin: 'right',
    distance: '50px',
    duration: 2000,
    delay: 0.5,
    reset: true
    
})
sr.reveal('.reveal-5', {
    origin: 'right',
    distance: '50px',
    duration: 2000,
    delay: 1,
    reset: true
    
})
sr.reveal('.reveal-6', {
    origin: 'right',
    distance: '50px',
    duration: 3000,
    delay: 1.5,
    reset: true
    
})
sr.reveal('.reveal-7', {
    origin: 'top',
    distance: '50px',
    duration: 2000,
    delay: 0.5,
    reset: true
    
})
sr.reveal('.reveal-8', {
    origin: 'top',
    distance: '50px',
    duration: 2000,
    delay: 1,
    reset: true
    
})
sr.reveal('.reveal-9', {
    origin: 'top',
    distance: '50px',
    duration: 2000,
    delay: 1.5,
    reset: true
    
})
