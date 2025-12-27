import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';

export default class AbntUI extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('abntTitle', locale => {
            const view = new ButtonView(locale);
            view.set({
                label: 'Título ABNT',
                withText: true,
                tooltip: true
            });

            view.on('execute', () => {
                editor.execute('abntTitle', 1);
            });

            return view;
        });
    }
}
