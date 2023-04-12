import 'bootstrap'
import 'lazysizes';

import { initializeSlider } from '.'

$(function () {

    $('.carousel-item.active').each((carouselItemIndex, carouselItem) => {
        initializeSlider(carouselItem, false)
    })

    $('.carousel.slide').on('slid.bs.carousel', (event) => {
        initializeSlider(event.relatedTarget, false)
    })
})
