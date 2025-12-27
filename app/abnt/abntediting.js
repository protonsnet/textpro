import Plugin from '@ckeditor/ckeditor5-core/src/plugin';

export default class AbntEditing extends Plugin {
    init() {
        const editor = this.editor;

        editor.commands.add(
            'abntTitle',
            {
                execute(level = 1) {
                    editor.model.change(writer => {
                        const selection = editor.model.document.selection;
                        writer.setAttribute(
                            'headingLevel',
                            level,
                            selection.getFirstPosition().parent
                        );
                    });
                }
            }
        );
    }
}
