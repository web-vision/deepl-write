import {Plugin} from '@ckeditor/ckeditor5-core';
import {ButtonView} from '@ckeditor/ckeditor5-ui';
import AjaxRequest from '@typo3/core/ajax/ajax-request.js';
import Modal from '@typo3/backend/modal.js';

export class Deeplwrite extends Plugin {
  static pluginName = 'Deeplwrite';

  init() {
    const editor = this.editor;

    editor.ui.componentFactory.add(Deeplwrite.pluginName, () => {
      const button = new ButtonView();

      button.set({
        label: TYPO3.lang['cke.button.title'],
        withText: true,
        icon: '',
        isEnabled: false
      });

      button.on('execute', () => {
        new AjaxRequest(TYPO3.settings.ajaxUrls.deeplwrite_ckeditor_edit)
          .get()
          .then(async function (response) {
            const deeplConfiguration = await response.resolve();
            console.log(deeplConfiguration);
            const content = document.createElement('div');
            content.innerHTML = deeplConfiguration;
            const originalContent = editor.getData();
            const originalReadability = content.querySelector('#original-readability');
            Deeplwrite.calculateReadability(originalContent, editor.locale.contentLanguage, originalReadability);

            content.querySelector('#original').value = originalContent;
            const optimizeModal = Modal.advanced({
              content: content,
              size: Modal.sizes.large,
              title: TYPO3.lang['cke.modal.title'],
              staticBackdrop: true,
              buttons: [
                {
                  text: TYPO3.lang['cke.modal.button.optimize'],
                  name: 'optimize',
                  icon: 'actions-lightbulb-on',
                  active: false,
                  btnClass: 'btn-primary',
                  trigger: function() {
                    const format = content.querySelector('input[name="format"]:checked');
                    let style = '';
                    let tone = '';
                    if (format !== null) {
                      if (format.classList.contains('style')) {
                        style = format.value;
                      } else {
                        tone = format.value;
                      }
                    }
                    new AjaxRequest(TYPO3.settings.ajaxUrls.deeplwrite_ckeditor_optimize)
                      .post({
                        text: editor.getData(),
                        style: style,
                        tone: tone
                      })
                      .then(async function (response){
                        const value = await response.resolve();
                        content.querySelector('#optimized').value = value.result;
                        const optimizedReadability = content.querySelector('#optimized-readability');
                        Deeplwrite.calculateReadability(value.result, editor.locale.contentLanguage, optimizedReadability);
                      })
                  }
                },
                {
                  text: TYPO3.lang['cke.modal.button.save'],
                  name: 'save',
                  icon: 'actions-document-save',
                  active: false,
                  btnClass: 'btn-secondary',
                  trigger: function(event, modal) {
                    const optimizedText = content.querySelector('#optimized').value;
                    if (optimizedText.length > 0) {
                      editor.setData(optimizedText);
                    }
                    modal.hideModal();
                  }
                }
              ]
            })
          });
      });

      new AjaxRequest(TYPO3.settings.ajaxUrls.deeplwrite_ckeditor_configuration)
        .get()
        .then(async function (response) {
          const deeplConfiguration = await response.resolve();
          if (deeplConfiguration.configured === true) {
            button.set('isEnabled', true);
          }
        });

      return button;
    });
  }

  /**
   * @param {string} text
   * @param {string} language
   * @param {Element} element
   */
  static calculateReadability(text, language, element) {

    new AjaxRequest(TYPO3.settings.ajaxUrls.deeplwrite_readability)
      .post({
        text: text,
        language: language
      })
      .then(async (response) => {
        const readability = await response.resolve();
        const value = Math.max(0, Math.min(100, Number(readability.score) || 0)).toFixed(2);
        element.style.setProperty('--value', value);
        element.setAttribute('aria-valuenow', String(value));
        const label = element.querySelector('.label');
        if (label) label.textContent = `${value}%`;
        return await response.resolve();
      })
  }
}
