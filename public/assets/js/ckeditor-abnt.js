/**
 * CKEditor 5 – Plugin ABNT
 * Numeração automática de títulos (1, 1.1, 1.1.1)
 * Compatível com ClassicEditor CDN
 */

class AbntNumberingPlugin {

    static init(editor) {

        editor.commands.add('abntNumbering', {
            execute: () => AbntNumberingPlugin.apply(editor)
        });

        editor.ui.componentFactory.add('abntNumbering', locale => {
            const button = new window.CKEDITOR5.ui.button.ButtonView(locale);

            button.set({
                label: 'Numerar Títulos (ABNT)',
                withText: true,
                tooltip: true
            });

            button.on('execute', () => {
                editor.execute('abntNumbering');
            });

            return button;
        });
    }

    static apply(editor) {
        const editable = editor.ui.getEditableElement();
        const headings = editable.querySelectorAll('h1, h2, h3');

        let h1 = 0, h2 = 0, h3 = 0;

        headings.forEach(el => {
            if (el.tagName === 'H1') {
                h1++; h2 = 0; h3 = 0;
                AbntNumberingPlugin.setText(el, `${h1}`);
            }

            if (el.tagName === 'H2') {
                h2++; h3 = 0;
                AbntNumberingPlugin.setText(el, `${h1}.${h2}`);
            }

            if (el.tagName === 'H3') {
                h3++;
                AbntNumberingPlugin.setText(el, `${h1}.${h2}.${h3}`);
            }
        });
    }

    static setText(element, number) {
        const cleanText = element.textContent.replace(/^\d+(\.\d+)*\s+/, '');
        element.textContent = `${number} ${cleanText}`;
    }
}