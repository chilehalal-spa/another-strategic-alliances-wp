document.addEventListener('DOMContentLoaded', function () {
    // Selector actualizado a la nueva nomenclatura
    const sliderEl = document.querySelector('.another-slider-container');
    
    if (sliderEl) {
        new Swiper('.another-slider-container', {
            loop: true,
            slidesPerView: 2,
            spaceBetween: 30,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 40 },
                768: { slidesPerView: 4, spaceBetween: 50 },
                1024: { slidesPerView: 5, spaceBetween: 60 },
            },
        });
    }
});