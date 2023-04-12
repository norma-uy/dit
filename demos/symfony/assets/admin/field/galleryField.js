import '../css/field/galleryField.scss'
import { FetchAPI } from '../utils'
import sortable from 'html5sortable/dist/html5sortable.amd'

export class GalleryField {
    constructor(enableTextFields = false) {
        this.enableTextFields = enableTextFields
        this.APIbaseUrl = '/admin/api/gallery-media'

        this.generalState = new Map()

        this.galleryState = new Map([
            ['full-width', new Map([['1', new Map()]])],
            [
                '1-2_1-2',
                new Map([
                    ['1', new Map()],
                    ['2', new Map()]
                ])
            ],
            [
                '2-3_1-3',
                new Map([
                    ['1', new Map()],
                    ['2', new Map()]
                ])
            ],
            [
                '1-3_2-3',
                new Map([
                    ['1', new Map()],
                    ['2', new Map()]
                ])
            ],
            [
                '1-3_1-3_1-3',
                new Map([
                    ['1', new Map()],
                    ['2', new Map()],
                    ['3', new Map()]
                ])
            ]
        ])

        this.selectedGalleryState = new Map()

        $('body')
            .append(`<div class="modal fade gallery-collection-modal" id="gallery-collection-modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Galería</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ...
                                </div>
                                <div class="modal-footer">
                                    <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>-->
                                    <button type="button" class="btn btn-primary" id="gallery-collection-save-btn">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>`)

        this.fieldCollectionModal = document.getElementById('gallery-collection-modal')
        this.bootstrapFieldCollectionModal = new bootstrap.Modal(this.fieldCollectionModal, {
            backdrop: true,
            focus: true,
            keyboard: true
        })

        this.galleryEntityProperty = null
        this.galleryRowIndex = null
    }

    deleteItemFromGalleryState = (templateType, galleryColumnKey, mediaItemId) => {
        const galleryColumns = this.galleryState.get(templateType)
        if (galleryColumns) {
            const galleryColumn = galleryColumns.get(galleryColumnKey)

            if (galleryColumn) {
                const mediaItem = galleryColumn.get(mediaItemId)

                if (mediaItem) {
                    galleryColumn.delete(mediaItemId)
                }
            }
        }
    }

    resetState = () => {
        if (this.galleryState && this.galleryState.size > 0) {
            this.galleryState.forEach((galleryColumns) => {
                if (galleryColumns && galleryColumns.size > 0) {
                    galleryColumns.forEach((galleryColumn) => {
                        galleryColumn.clear()
                    })
                }
            })
        }

        this.generalState.clear()
        this.selectedGalleryState.clear()
        this.galleryEntityProperty = null
        this.galleryRowIndex = null

        $(`#gallery-collection-modal #media-list`).empty()
    }

    makeNewMediaid = (mediaItemId) => {
        let newMediaItemId = mediaItemId
        newMediaItemId = `${newMediaItemId}-${Date.now()}`

        return newMediaItemId
    }

    initGallery = () => {
        const _this = this

        const handleHiddenBsModal = (event) => {
            _this.resetState()
        }

        this.fieldCollectionModal.removeEventListener('hidden.bs.modal', handleHiddenBsModal, false)
        this.fieldCollectionModal.addEventListener('hidden.bs.modal', handleHiddenBsModal)

        $('.edit-gallery-btn').off('click')
        $('.edit-gallery-btn').on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            _this.resetState()

            const btnName = e.target.name
            let galleryNameParts = btnName.match(/([a-zA-Z]+)?(\[[a-zA-Z0-9]+\]+)/g)

            _this.galleryEntityProperty =
                galleryNameParts[0] !== undefined ? galleryNameParts[0].replace('[', '_').replace(']', '') : null
            _this.galleryRowIndex =
                galleryNameParts[1] !== undefined && galleryNameParts[1] === '[rows]'
                    ? parseInt(galleryNameParts[2].replace('[', '').replace(']', ''))
                    : null

            const queryUrlString = window.location.search
            const urlParams = new URLSearchParams(queryUrlString)

            const entityId = urlParams.get('entityId')

            const searchParams = new URLSearchParams({
                enableTextFields: _this.enableTextFields,
                ...(entityId ? { entityId: entityId } : {}),
                ...(_this.galleryEntityProperty ? { galleryEntityProperty: _this.galleryEntityProperty } : {}),
                ...(_this.galleryRowIndex ? { galleryRowIndex: _this.galleryRowIndex } : {})
            })

            FetchAPI.get(`${_this.APIbaseUrl}?${searchParams.toString()}`).then((data) => {
                const { libraryHtmlTab } = data.forms || { libraryHtmlTab: '' }

                _this.bootstrapFieldCollectionModal.show()
                $('#gallery-collection-modal .modal-body').html(libraryHtmlTab)

                _this.initModal()
            })
        })

        $('.row-gallery-field .field-collection-add-button').off('click')
        $('.row-gallery-field .field-collection-add-button').on('click', () => {
            setTimeout(() => {
                _this.initGallery()
            }, 300)
        })
    }

    imagePickerModalEvents = () => {
        const _this = this

        /**
         * Actualizar estado de las imágenes seleccionadas
         */
        $(`#image-picker-list .media-item`).removeClass('selected')
        $(`#image-picker-list .media-item input[type="checkbox"]`).prop('checked', false)
        if (_this.selectedGalleryState && _this.selectedGalleryState.size > 0) {
            _this.selectedGalleryState.forEach((selectedMediaItem, mediaId) => {
                $(`#image-picker-list #media-item-${mediaId}`).addClass('selected')
                $(`#image-picker-list #media-checkbox-${mediaId}`).prop('checked', true)
            })
        }
        /* ----------------------- */

        /**
         * Evento para seleccionar imagen
         */
        const mediaItemImg = $(
            '#image-picker-modal .image-picker-list .media-item img, #image-picker-modal .image-picker-list .media-item video'
        )
        mediaItemImg.off('click')
        mediaItemImg.on('click', (e) => {
            e.stopPropagation()

            const mediaImgCheck = e.target
            const mediaImgCheckData = mediaImgCheck ? mediaImgCheck.dataset : null
            const mediaItemId = mediaImgCheckData ? mediaImgCheckData.id : null

            if (mediaItemId) {
                const mediaItem = $(`.image-picker-list #media-item-${mediaItemId}`)
                const mediaCheck = $(`.image-picker-list #media-checkbox-${mediaItemId}`)

                if (mediaCheck.is(':checked')) {
                    mediaItem.removeClass('selected')
                    mediaCheck.prop('checked', false)

                    _this.selectedGalleryState.delete(mediaItemId)
                } else {
                    mediaItem.addClass('selected')
                    mediaCheck.prop('checked', true)

                    _this.selectedGalleryState.set(mediaItemId, mediaItem)
                }
            }
        })
        /* ----------------------- */

        /**
         * Insertar listado de images seleccionadas en el galleryState
         */
        const imagePickerAddBtn = $('#image-picker-modal #image-picker-add-btn')
        imagePickerAddBtn.off('click')
        imagePickerAddBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            if (_this.selectedGalleryState && _this.selectedGalleryState.size > 0) {
                $('.gallery-form-container #media-list .media-empty').remove()

                const templateType = $('.gallery-form-container #template-type-selector').val()

                const galleryColumnBtnActivated = _this.generalState.get('galleryColumnBtnActivated')
                const galleryColumnBtnActivatedData = $(galleryColumnBtnActivated).data()
                const galleryColumnBtnActivatedKey = galleryColumnBtnActivatedData.key.toString()

                const galleryStateActive = _this.galleryState.get(templateType)
                const galleryColumnActivated = galleryStateActive.get(galleryColumnBtnActivatedKey)

                _this.selectedGalleryState.forEach((selectedMediaItem, mediaItemId) => {
                    $(selectedMediaItem).removeClass('selected')

                    let mediaItemClone = $(selectedMediaItem).clone()

                    mediaItemId = _this.makeNewMediaid(mediaItemId)

                    $(mediaItemClone).find('figure img, figure video').off('click')

                    $(mediaItemClone).attr('id', mediaItemId).attr('data-id', mediaItemId).data('id', mediaItemId)
                    $(mediaItemClone)
                        .find('figure img, figure video')
                        .attr('id', `thumbnail-${mediaItemId}`)
                        .attr('data-id', mediaItemId)
                        .data('id', mediaItemId)
                    $(mediaItemClone)
                        .find('input[type="checkbox"]')
                        .attr('id', `media-checkbox-${mediaItemId}`)
                        .val(mediaItemId)
                    $(mediaItemClone)
                        .find('input.order')
                        .attr('id', `media-order-${mediaItemId}`)
                        .attr('data-id', mediaItemId)
                        .data('id', mediaItemId)

                    $(mediaItemClone)
                        .find('.controls')
                        .prepend(
                            `<button type="button" class="btn btn-link order-btn">
                                <i class="fa-solid fa-up-down-left-right"></i>
                            </button>
                            <button 
                            type="button" 
                            class="btn btn-link-danger delete-btn" 
                            data-template-type="${templateType}"
                            data-gallery-column-key="${galleryColumnBtnActivatedKey}"
                            data-media-item-id="${mediaItemId}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>`
                        )
                    if (_this.enableTextFields) {
                        $(mediaItemClone).append(
                            `<input type="text"
                                value=""
                                id="media-txt-es-${mediaItemId}"
                                data-id="${mediaItemId}"
                                class="form-control"
                                placeholder="Texto en español" />
                            <hr />
                            <input type="text"
                                value=""
                                id="media-txt-en-${mediaItemId}"
                                data-id="${mediaItemId}"
                                class="form-control"
                                placeholder="Texto en ingles" />
                            <hr />
                            <div class="row">
                                <div class="col-6 d-flex">
                                    <input type="radio"
                                        class="btn-check m-auto"
                                        id="media-position-1-${mediaItemId}"
                                        name="media-position-${mediaItemId}"
                                        value="1"
                                        checked
                                        autocomplete="off" />
                                    <label class="btn btn-outline-primary m-auto"
                                        for="media-position-1-${mediaItemId}">
                                        <i class="fa-solid fa-border-top-left"></i>
                                    </label>
                                </div>
                                <div class="col-6 d-flex">
                                    <input type="radio"
                                        class="btn-check m-auto"
                                        id="media-position-2-${mediaItemId}"
                                        name="media-position-${mediaItemId}"
                                        value="2"
                                        autocomplete="off" />
                                    <label class="btn btn-outline-primary m-auto"
                                        for="media-position-2-${mediaItemId}">
                                        <i class="fa-solid fa-arrows-to-dot"></i>
                                    </label>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-6 d-flex">
                                    <input type="radio"
                                        class="btn-check m-auto"
                                        id="media-color-1-${mediaItemId}"
                                        name="media-color-${mediaItemId}"
                                        value="fff"
                                        checked
                                        autocomplete="off" />
                                    <label class="btn btn-outline-primary m-auto"
                                        for="media-color-1-${mediaItemId}">
                                        Blanco
                                    </label>
                                </div>
                                <div class="col-6 d-flex">
                                    <input type="radio"
                                        class="btn-check m-auto"
                                        id="media-color-2-${mediaItemId}"
                                        name="media-color-${mediaItemId}"
                                        value="000"
                                        autocomplete="off" />
                                    <label class="btn btn-outline-primary m-auto"
                                        for="media-color-2-${mediaItemId}">
                                        Negro
                                    </label>
                                </div>
                            </div>`
                        )

                        const mediaCheck = $(mediaItemClone).find(`#media-checkbox-${mediaItemId}`)
                        const mediaOrder = $(mediaItemClone).find(`#media-order-${mediaItemId}`)

                        const mediaTxtEs = $(mediaItemClone).find(`#media-txt-es-${mediaItemId}`)
                        const mediaTxtEn = $(mediaItemClone).find(`#media-txt-en-${mediaItemId}`)
                        const mediaPosition = $(mediaItemClone).find(
                            `input[name="media-position-${mediaItemId}"]:checked`
                        )
                        const mediaColor = $(mediaItemClone).find(`input[name="media-color-${mediaItemId}"]:checked`)

                        galleryColumnActivated.set(mediaItemId, {
                            jsonData: {
                                order: mediaOrder.val(),
                                txtEs: mediaTxtEs.val(),
                                txtEn: mediaTxtEn.val(),
                                position: mediaPosition.val(),
                                color: mediaColor.val(),
                                ...mediaCheck.data()
                            },
                            mediaItemHtml: mediaItemClone
                        })
                    } else {
                        const mediaCheck = $(mediaItemClone).find(`#media-checkbox-${mediaItemId}`)
                        const mediaOrder = $(mediaItemClone).find(`#media-order-${mediaItemId}`)

                        galleryColumnActivated.set(mediaItemId, {
                            jsonData: {
                                order: mediaOrder.val(),
                                ...mediaCheck.data()
                            },
                            mediaItemHtml: mediaItemClone
                        })
                    }

                    galleryStateActive.set(galleryColumnBtnActivatedKey, galleryColumnActivated)
                    _this.galleryState.set(templateType, galleryStateActive)
                })

                _this.selectedGalleryState.clear()
                $('#image-picker-modal').removeClass('active')

                _this.initModal()
            }
        })
        /* ----------------------- */

        /**
         * Filtrar por categoría
         */
        const categoryItemBtn = $('#image-picker-modal .category-list .category-item button')
        categoryItemBtn.off('click')
        categoryItemBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            const categoryId = _this.generalState.get('categoryId') != e.target.dataset.id ? e.target.dataset.id : null
            const searchParams = new URLSearchParams({
                page: 1,
                ...(categoryId ? { categoryId: categoryId } : {})
            })

            _this.generalState.set('categoryId', categoryId)

            $('.gallery-form-container .loader').addClass('active')

            FetchAPI.get(`/admin/api/gallery-image-picker?${searchParams.toString()}`).then((data) => {
                $('.gallery-form-container .loader').removeClass('active')

                const { libraryHtmlTab } = data.forms || { libraryHtmlTab: '' }
                $('#image-picker-modal #image-picker-list').html(libraryHtmlTab)

                _this.imagePickerModalEvents()
            })
        })
        /* ----------------------- */

        /**
         * Paginado
         */
        const paginationLinkBtn = $('#image-picker-modal .pagination .page-item a')
        paginationLinkBtn.off('click')
        paginationLinkBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            const page = e.currentTarget.dataset.page
            const categoryId = _this.generalState.get('categoryId') ? _this.generalState.get('categoryId') : null
            const searchParams = new URLSearchParams({
                page: page,
                ...(categoryId ? { categoryId: categoryId } : {})
            })

            _this.generalState.set('pageActive', page)

            $('.gallery-form-container .loader').addClass('active')

            FetchAPI.get(`/admin/api/gallery-image-picker?${searchParams.toString()}`).then((data) => {
                $('.gallery-form-container .loader').removeClass('active')

                const { libraryHtmlTab } = data.forms || { libraryHtmlTab: '' }
                $('#image-picker-modal #image-picker-list').html(libraryHtmlTab)

                _this.imagePickerModalEvents()
            })
        })
        /* ----------------------- */

        /**
         * Cerrar la modal de la galería de medios
         */
        const controlsCloseBtn = $('#image-picker-modal .controls .btn-close')
        controlsCloseBtn.off('click')
        controlsCloseBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            $('#image-picker-modal').removeClass('active')
            _this.selectedGalleryState.clear()
            _this.initModal()
        })
        /* ----------------------- */
    }

    galleryColumnBtnsEvents = () => {
        const _this = this

        let galleryColumnBtnActivated = $('.gallery-form-container .gallery-column-btns .active button.active')
        const galleryColumnBtns = $('.gallery-form-container .gallery-column-btns .active button')

        if (!galleryColumnBtnActivated || galleryColumnBtnActivated.length <= 0) {
            galleryColumnBtnActivated = galleryColumnBtns.first()
        }

        let galleryColumnBtnActivatedData = $(galleryColumnBtnActivated).data()
        let galleryColumnBtnActivatedKey = galleryColumnBtnActivatedData ? galleryColumnBtnActivatedData.key : null
        galleryColumnBtnActivatedKey = galleryColumnBtnActivatedKey ? galleryColumnBtnActivatedKey.toString() : null

        this.generalState.set('galleryColumnBtnActivated', galleryColumnBtnActivated)

        const galleryColumns = $(`#gallery-collection-modal #media-list .gallery-column`)

        galleryColumns.removeClass('active')
        galleryColumnBtns.removeClass('active')

        galleryColumnBtnActivated.addClass('active')
        $(`#gallery-collection-modal #media-list #gallery-column-${galleryColumnBtnActivatedKey}`).addClass('active')

        $(galleryColumnBtns).off('click')
        $(galleryColumnBtns).on('click', (e) => {
            const button = e.target

            if (!$(button).hasClass('active')) {
                let buttonData = $(button).data()
                let buttonDataKey = buttonData ? buttonData.key : null
                buttonDataKey = buttonDataKey ? buttonDataKey.toString() : null

                galleryColumns.removeClass('active')
                galleryColumnBtns.removeClass('active')

                $(button).addClass('active')
                $(`#gallery-collection-modal #media-list #gallery-column-${buttonDataKey}`).addClass('active')

                _this.generalState.set('galleryColumnBtnActivated', button)

                _this.mediaItemsEvents()
            }
        })
    }

    updateGalleryColumns = (updateTemplateType = true) => {
        const _this = this

        const galleryMediaList = $(`#gallery-collection-modal #media-list`)
        const galleryMediaListData = galleryMediaList && galleryMediaList.length > 0 ? galleryMediaList.data() : null
        const galleryMediaListDataTemplateType = galleryMediaListData ? galleryMediaListData.templateType : null

        const templateTypeSelector = $('.gallery-form-container #template-type-selector')

        if (updateTemplateType && galleryMediaListDataTemplateType) {
            templateTypeSelector.val(galleryMediaListDataTemplateType)
            $('.column-btns').removeClass('active')
            $(`#${galleryMediaListDataTemplateType}-column`).addClass('active')
        }

        const templateType = templateTypeSelector.val()
        const galleryStateActive = _this.galleryState.get(templateType)

        $(`#gallery-collection-modal #media-list .gallery-column`).each((galleryColumnIndex, galleryColumn) => {
            let galleryColumnData = $(galleryColumn).data()
            let galleryColumnKey = galleryColumnData ? galleryColumnData.key : null
            galleryColumnKey = galleryColumnKey ? galleryColumnKey.toString() : null

            if (galleryColumnKey) {
                const galleryColumnLayout = galleryStateActive.get(galleryColumnKey)

                if (galleryColumnLayout) {
                    $(galleryColumn)
                        .find('.media-item')
                        .each((mediaItemIndex, mediaItem) => {
                            let mediaItemData = $(mediaItem).data()
                            let mediaItemId = mediaItemData ? mediaItemData.id : null
                            mediaItemId = mediaItemId ? mediaItemId.toString() : null

                            if (mediaItemId) {
                                const mediaCheck = $(`#gallery-collection-modal #media-checkbox-${mediaItemId}`)
                                let mediaCheckData = $(mediaCheck).data()

                                const mediaOrder = $(`#gallery-collection-modal #media-order-${mediaItemId}`)

                                galleryColumnLayout.set(mediaItemId, {
                                    jsonData: { order: mediaOrder.val(), ...mediaCheckData },
                                    mediaItemHtml: mediaItem
                                })
                            }
                        })

                    galleryStateActive.set(galleryColumnKey, galleryColumnLayout)
                    _this.galleryState.set(templateType, galleryStateActive)
                }
            }
        })

        galleryStateActive.forEach((galleryColumnRow, galleryColumnKey) => {
            let galleryColumn = $(`#gallery-collection-modal #media-list #gallery-column-${galleryColumnKey}`)

            if (!galleryColumn || galleryColumn.length <= 0) {
                galleryColumn = $(
                    `<section class="gallery-column selected" id="gallery-column-${galleryColumnKey}" data-key="${galleryColumnKey}"></section>`
                )
                galleryMediaList.append(galleryColumn)
            }

            galleryColumnRow.forEach((mediaItem) => {
                if (mediaItem['mediaItemHtml'] && mediaItem['mediaItemHtml'].length > 0) {
                    galleryColumn.append(mediaItem['mediaItemHtml'])
                }
            })
        })
    }

    mediaItemsEvents = () => {
        const _this = this

        /**
         * Ordenar imágenes de la galería
         */
        $(
            sortable('#gallery-collection-modal #media-list .gallery-column', {
                handle: 'button.order-btn'
            })
        ).on('sortstop', (e) => {
            const templateType = $('.gallery-form-container #template-type-selector').val()
            const galleryColumnBtnActivated = _this.generalState.get('galleryColumnBtnActivated')
            const galleryColumnBtnActivatedData = $(galleryColumnBtnActivated).data()
            const galleryColumnBtnActivatedKey = galleryColumnBtnActivatedData.key.toString()

            $('#gallery-collection-modal #media-list .gallery-column').each((index) => {
                const galleryColumn = $(this)
                const galleryColumnId = galleryColumn.attr('id')

                $(`#gallery-collection-modal #media-list #${galleryColumnId} .media-item .order`).each((index) => {
                    const mediaOrder = $(this)
                    mediaOrder.val(index + 1)

                    const mediaOrderData = mediaOrder.data()
                    let mediaCheckId = mediaOrderData ? mediaOrderData.id : null
                    mediaCheckId = mediaCheckId ? mediaCheckId.toString() : null

                    if (mediaCheckId) {
                        let galleryStateActive = _this.galleryState.get(templateType)
                        let galleryColumnActivated = galleryStateActive.get(galleryColumnBtnActivatedKey)

                        let mediaItemData = galleryColumnActivated.get(mediaCheckId)

                        if (mediaItemData) {
                            let mediaOrder = $(`#gallery-collection-modal .media-list #media-order-${mediaCheckId}`)
                            mediaItemData['jsonData']['order'] = mediaOrder.val()

                            galleryColumnActivated.set(mediaCheckId, mediaItemData)
                            galleryStateActive.set(galleryColumnBtnActivatedKey, galleryColumnActivated)
                            _this.galleryState.set(templateType, galleryStateActive)
                        }
                    }
                })
            })
        })
        /* ----------------------- */

        /**
         * Borrar imagen seleccionada
         */
        const controlsDeleteBtn = $('#gallery-collection-modal .controls .delete-btn')
        controlsDeleteBtn.off('click')
        controlsDeleteBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            const mediaItemId = e.currentTarget.dataset.mediaItemId
            const galleryColumnKey = e.currentTarget.dataset.galleryColumnKey
            const templateType = e.currentTarget.dataset.templateType

            if (mediaItemId) {
                $(`#gallery-collection-modal #media-list #media-item-${mediaItemId}`).remove()
                _this.deleteItemFromGalleryState(templateType, galleryColumnKey, mediaItemId)
                _this.initModal()
            }
        })
        /* ----------------------- */
    }

    initModal = () => {
        const _this = this

        /**
         * Cambio de plantilla
         */
        const templateTypeSelector = $('.gallery-form-container #template-type-selector')
        templateTypeSelector.off('change')
        templateTypeSelector.on('change', (e) => {
            const templateTypeValue = e.target.value

            if (templateTypeValue) {
                $('.column-btns').removeClass('active')
                $(`#${templateTypeValue}-column`).addClass('active')

                $(`#gallery-collection-modal #media-list`).attr('data-template-type', templateTypeValue)
                $(`#gallery-collection-modal #media-list`).data('template-type', templateTypeValue)

                $(`.gallery-column`).removeClass('selected')

                $(`#gallery-column-1`).addClass('selected')

                if (templateTypeValue !== 'full-width') {
                    $(`#gallery-column-2`).addClass('selected')
                }

                if (templateTypeValue === '1-3_1-3_1-3') {
                    $(`#gallery-column-3`).addClass('selected')
                }

                _this.updateGalleryColumns(false)
                _this.galleryColumnBtnsEvents()
            }
        })
        /* ----------------------- */

        /**
         * Activar panel de búsqueda de en la galería de medios
         */
        const openImagePickerBtn = $('.gallery-form-container #open-image-picker-btn')
        openImagePickerBtn.off('click')
        openImagePickerBtn.on('click', (e) => {
            e.preventDefault()
            e.stopPropagation()

            $('#image-picker-modal').addClass('active')

            _this.imagePickerModalEvents()
        })
        /* ----------------------- */

        /**
         * Guardar selección de galerías
         */
        const galleryCollectionSaveBtn = $('#gallery-collection-modal #gallery-collection-save-btn')
        galleryCollectionSaveBtn.off('click')
        galleryCollectionSaveBtn.on('click', (e) => {
            const templateType = $('.gallery-form-container #template-type-selector').val()

            let galleryColumnsActivated = _this.galleryState.get(templateType)

            if (_this.enableTextFields) {
                $(`#gallery-collection-modal #media-list .gallery-column.selected`).each(
                    (galleryColumnIndex, galleryColumn) => {
                        const galleryColumnKey = $(galleryColumn).data().key.toString()
                        const galleryColumnData = galleryColumnsActivated.get(galleryColumnKey)

                        $(galleryColumn)
                            .find(`.media-item`)
                            .each((mediaItemIndex, mediaItem) => {
                                const mediaItemId = $(mediaItem).data().id.toString()

                                const mediaCheck = $(mediaItem).find(`#media-checkbox-${mediaItemId}`)
                                const mediaOrder = $(mediaItem).find(`#media-order-${mediaItemId}`)
                                const mediaTxtEs = $(mediaItem).find(`#media-txt-es-${mediaItemId}`)
                                const mediaTxtEn = $(mediaItem).find(`#media-txt-en-${mediaItemId}`)
                                const mediaPosition = $(mediaItem).find(
                                    `input[name="media-position-${mediaItemId}"]:checked`
                                )
                                const mediaColor = $(mediaItem).find(`input[name="media-color-${mediaItemId}"]:checked`)

                                const mediaItemData = galleryColumnData.get(mediaItemId)

                                mediaItemData.jsonData = {
                                    order: mediaOrder.val(),
                                    txtEs: mediaTxtEs.val(),
                                    txtEn: mediaTxtEn.val(),
                                    position: mediaPosition.val(),
                                    color: mediaColor.val(),
                                    ...mediaCheck.data()
                                }

                                galleryColumnData.set(mediaItemId, mediaItemData)
                            })

                        galleryColumnsActivated.set(galleryColumnKey, galleryColumnData)
                    }
                )
            }

            galleryColumnsActivated = Object.fromEntries(galleryColumnsActivated)

            for (let galleryIndex in galleryColumnsActivated) {
                galleryColumnsActivated[galleryIndex] = Object.fromEntries(galleryColumnsActivated[galleryIndex])
            }

            if (_this.galleryEntityProperty) {
                const dataQuery = `#${_this.galleryEntityProperty}${
                    _this.galleryRowIndex ? `_rows_${_this.galleryRowIndex}` : ''
                }_data`

                $(dataQuery).val(JSON.stringify(galleryColumnsActivated))

                const templateTypeQuery = `#${_this.galleryEntityProperty}${
                    _this.galleryRowIndex ? `_rows_${_this.galleryRowIndex}` : ''
                }_templateType`

                $(templateTypeQuery).val(templateType)

                const queryUrlString = window.location.search
                const urlParams = new URLSearchParams(queryUrlString)

                const entityId = urlParams.get('entityId')

                const formElement = document.querySelector('.ea-edit-form, .ea-new-form')
                const formData = new FormData(formElement)
                formData.append('entityId', entityId)
                formData.append('galleryEntityProperty', _this.galleryEntityProperty)

                FetchAPI.postForm(`/admin/api/save-gallery-data`, formData).then((data) => {
                    console.log(data)
                })
            }

            $('#image-picker-modal').removeClass('active')
            _this.bootstrapFieldCollectionModal.hide()

            _this.resetState()
        })
        /* ----------------------- */

        _this.updateGalleryColumns()

        _this.galleryColumnBtnsEvents()

        _this.mediaItemsEvents()
    }
}
