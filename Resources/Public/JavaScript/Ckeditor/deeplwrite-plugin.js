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
            const content = document.createElement('div');
            content.innerHTML = deeplConfiguration;
            content.querySelector('#original').value = editor.getData();
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
                    console.log(format);
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
}
