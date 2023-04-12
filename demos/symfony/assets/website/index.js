import 'bootstrap'

/* resolución */
function resolution() {
    return $(window).width()
}

export const initializeSlider = (carouselItem, enableTextFields = false) => {
    const mediaVideo = $(carouselItem).find('video')

    if (mediaVideo && mediaVideo.length > 0) {
        $(mediaVideo).each((videoIndex, video) => {
            video.play()
        })
    }

    if (enableTextFields) {
        const mediaData = $(carouselItem).find('img, video').data()
        const mediaDataTxt = mediaData && mediaData.txt ? mediaData.txt : null
        const mediaDataPosition = mediaData && mediaData.position ? mediaData.position : null
        const mediaDataColor = mediaData && mediaData.color ? mediaData.color : null

        $(carouselItem).find('h4').html('')
        $(carouselItem).parents('.grid-content').find('h3').html('')

        if (mediaDataPosition && mediaDataTxt) {
            if (mediaDataPosition === 1) {
                $(carouselItem).parents('.grid-content').find('h3').html(mediaDataTxt)
            } else if (mediaDataPosition === 2) {
                $(carouselItem).find('h4').html(mediaDataTxt)
            }
        }

        if (mediaDataColor) {
            $(carouselItem).parents('.grid-content').removeClass('color-fff').removeClass('color-000')
            $(carouselItem).parents('.grid-content').addClass(mediaDataColor)
        }
    }
}
