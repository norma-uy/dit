/**
 * The callback function executed
 * once all the Google dependencies have loaded
 */
window.onGoogleReCaptchaApiLoad = () => {
    const widgets = document.querySelectorAll('[data-toggle="recaptcha"]')
    for (let i = 0; i < widgets.length; i++) {
        renderReCaptcha(widgets[i])
    }
}

/**
 * Render the given widget as a reCAPTCHA
 * from the data-type attribute
 */
const renderReCaptcha = (widget) => {
    const form = widget.closest('form')
    const widgetType = widget.getAttribute('data-type')

    const recaptchaSiteKeyElement = document.getElementById('recaptcha-site-key')
    const recaptchaSiteKey = recaptchaSiteKeyElement ? recaptchaSiteKeyElement.value : null
    const widgetParameters = {
        sitekey: recaptchaSiteKey
    }

    if (widgetType == 'invisible') {
        widgetParameters['callback'] = function () {
            form.submit()
        }
        widgetParameters['size'] = 'invisible'
    }

    const widgetId = grecaptcha.render(widget, widgetParameters)

    if (widgetType == 'invisible') {
        bindChallengeToSubmitButtons(form, widgetId)
    }
}

/**
 * Prevent the submit buttons from submitting a form
 * and invoke the challenge for the given captcha id
 */
const bindChallengeToSubmitButtons = (form, reCaptchaId) => {
    getSubmitButtons(form).forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault()
            grecaptcha.execute(reCaptchaId)
        })
    })
}

/**
 * Get the submit buttons from the given form
 */
const getSubmitButtons = (form) => {
    const buttons = form.querySelectorAll('button, input')
    const submitButtons = []

    for (let i = 0; i < buttons.length; i++) {
        const button = buttons[i]
        if (button.getAttribute('type') == 'submit') {
            submitButtons.push(button)
        }
    }

    return submitButtons
}
