// --- SLIDER LOGIC (Refactored for Auto-Scroll and Hover) ---

let slideIndices = {
    'trending-slider': 0,
    'recommended-slider-container': 0
};
let trendingInterval;
let recommendedInterval;


// Get the number of cards visible based on the current window size
function getCardsPerView(sliderId) {
    if (sliderId === 'trending-slider') {
        const width = window.innerWidth;
        if (width <= 480) return 1; 
        if (width <= 768) return 2; 
        if (width <= 1024) return 3; 
        return 4; 
    }
    return 1; // Recommended is always 1
}

// Core function to move the sliders
function moveSlider(sliderId, direction) {
    const container = document.getElementById(sliderId);
    if (!container) return;

    let currentIndex = slideIndices[sliderId];

    if (sliderId === 'trending-slider') {
        const cards = container.querySelectorAll('.manga-card');
        const cardsPerView = getCardsPerView(sliderId);
        const totalCards = cards.length;

        // Calculate the maximum number of moves (or slides) possible
        // e.g., 8 cards, 4 per view -> maxMoves = 2. Indices 0, 1.
        // Math.ceil(8/4) = 2. Max index is 1. (0, 1)
        const maxMoves = Math.ceil(totalCards / cardsPerView);

        currentIndex += direction;

        // Handle looping logic
        if (currentIndex >= maxMoves) {
            currentIndex = 0; // Loop to start
        } else if (currentIndex < 0) {
            currentIndex = maxMoves - 1; // Loop to end
        }

        slideIndices[sliderId] = currentIndex;

        // Calculate the percentage to shift. It's always 100% per slide step.
        const translateX = -currentIndex * 100;
        container.style.transform = `translateX(${translateX}%)`;

    } else if (sliderId === 'recommended-slider-container') {
        const slides = container.querySelectorAll('.recommended-slide');
        if (slides.length === 0) return;
        
        const totalSlides = slides.length;

        // Remove active class from current slide
        slides[currentIndex].classList.remove('active');

        // Calculate new index and handle looping
        currentIndex += direction;
        if (currentIndex >= totalSlides) {
            currentIndex = 0;
        } else if (currentIndex < 0) {
            currentIndex = totalSlides - 1;
        }
        
        slideIndices[sliderId] = currentIndex;
        // Add active class to new slide
        slides[currentIndex].classList.add('active');
    }
}

// --- Auto Scroll Functions ---

// 1. Trending Slider Auto-Scroll & Hover
const trendingSliderContainer = document.querySelector('.trending-slider');

function startTrendingAutoScroll() {
    clearInterval(trendingInterval); 
    trendingInterval = setInterval(() => {
        moveSlider('trending-slider', 1);
    }, 5000); // Auto-scroll every 5 seconds
}

if (trendingSliderContainer) {
    startTrendingAutoScroll(); // Start immediately
    
    // Pause on hover
    trendingSliderContainer.addEventListener('mouseenter', () => clearInterval(trendingInterval));
    trendingSliderContainer.addEventListener('mouseleave', startTrendingAutoScroll);
}


// 2. Recommended Slider Auto-Scroll & Hover
const recommendedSliderContainer = document.getElementById('recommended-slider-container');

function startRecommendedAutoScroll() {
    clearInterval(recommendedInterval);
    recommendedInterval = setInterval(() => {
        moveSlider('recommended-slider-container', 1);
    }, 7000); // Auto-scroll every 7 seconds
}

if (recommendedSliderContainer) {
    startRecommendedAutoScroll(); // Start immediately
    
    // Pause on hover
    recommendedSliderContainer.addEventListener('mouseenter', () => clearInterval(recommendedInterval));
    recommendedSliderContainer.addEventListener('mouseleave', startRecommendedAutoScroll);
}


// --- Event Listeners for Manual Buttons ---
document.querySelectorAll('.slider-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        const targetId = e.target.getAttribute('data-target');
        const direction = e.target.classList.contains('next-btn') || e.target.classList.contains('recommended-next') ? 1 : -1;
        moveSlider(targetId, direction);
        
        // When manually clicked, restart the auto-scroll timer to prevent immediate change
        if (targetId === 'trending-slider') {
            startTrendingAutoScroll(); 
        } else if (targetId === 'recommended-slider-container') {
            startRecommendedAutoScroll(); 
        }
    });
});

// Initialize trending slider on window resize to fix position and restart interval
window.addEventListener('resize', () => {
        // Reset the index and apply the transform to fit the new card count
    slideIndices['trending-slider'] = 0; 
    const trendingInner = document.getElementById('trending-slider');
    if (trendingInner) {
        trendingInner.style.transform = `translateX(0)`;
    }
    // Restart trending interval to ensure timing is fresh after resize
    if (trendingSliderContainer) {
        startTrendingAutoScroll(); 
    }
});
