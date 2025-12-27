import AbntEditing from './abntediting';
import AbntUI from './abntui';

export default class Abnt extends ClassicEditor.Plugin {
    static get requires() {
        return [ AbntEditing, AbntUI ];
    }
}
