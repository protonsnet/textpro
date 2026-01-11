/**
 * CKEditor 5 – Build Profissional (Open-Source)
 * Compatível com Webpack 4 e Node 18+
 * Customizado para documentos A4 / ABNT
 */
console.log('CKEDITOR BUILD EXECUTADO');

/* ================= EDITOR BASE ================= */
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

/* ================= CORE ================= */
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';

/* ================= TEXTO E ESTILOS ================= */
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
import Strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import FontFamily from '@ckeditor/ckeditor5-font/src/fontfamily';
import FontSize from '@ckeditor/ckeditor5-font/src/fontsize';
import FontColor from '@ckeditor/ckeditor5-font/src/fontcolor';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor';

/* ================= LISTAS E LINKS ================= */
import List from '@ckeditor/ckeditor5-list/src/list';
import Link from '@ckeditor/ckeditor5-link/src/link';

/* ================= TABELAS ================= */
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TableProperties from '@ckeditor/ckeditor5-table/src/tableproperties';
import TableCellProperties from '@ckeditor/ckeditor5-table/src/tablecellproperties';

/* ================= IMAGENS E UPLOAD ================= */
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
import FileRepository from '@ckeditor/ckeditor5-upload/src/filerepository';

/* ================= UTILITÁRIOS ================= */
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat';
import PageBreak from '@ckeditor/ckeditor5-page-break/src/pagebreak';

/* ================= TEMA ================= */
import '@ckeditor/ckeditor5-theme-lark/theme/theme.css';
import '@ckeditor/ckeditor5-editor-classic/theme/classiceditor.css';

/* ================= CLASSE DO EDITOR ================= */
class Editor extends ClassicEditor {}

/* ================= REGISTRO DE PLUGINS ================= */
Editor.builtinPlugins = [
    Essentials,
    Paragraph,
    Heading,

    Bold,
    Italic,
    Underline,
    Strikethrough,
    RemoveFormat,

    Alignment,
    FontFamily,
    FontSize,
    FontColor,
    FontBackgroundColor,

    List,
    Link,

    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties,

    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    ImageUpload,
    FileRepository,

    MediaEmbed,
    PasteFromOffice,
    PageBreak,
];

/* ================= CONFIGURAÇÃO PADRÃO ================= */
Editor.defaultConfig = {
    language: 'pt-br',

    toolbar: {
        items: [
            'undo', 'redo', '|',
            'heading', '|',

            'fontFamily', 'fontSize',
            'fontColor', 'fontBackgroundColor', '|',

            'bold', 'italic', 'underline', 'strikethrough', 'removeFormat', '|',

            'alignment', '|',

            'bulletedList', 'numberedList', '|',

            'pageBreak', '|',

            'insertTable', 'imageUpload', 'mediaEmbed', '|',

            'link'
        ],
        shouldNotGroupWhenFull: true
    },

    fontFamily: {
        options: [
            'default',
            'Arial, Helvetica, sans-serif',
            'Calibri, sans-serif',
            'Times New Roman, Times, serif',
            'Georgia, serif',
            'Courier New, Courier, monospace'
        ]
    },

    fontSize: {
        options: [ 8, 9, 10, 11, 12, 14, 16, 18, 20, 24, 28, 32 ]
    },

    alignment: {
        options: [ 'left', 'center', 'right', 'justify' ]
    },

    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells',
            'tableProperties',
            'tableCellProperties'
        ]
    },

    image: {
        resizeUnit: 'px',
        toolbar: [
            'imageStyle:alignLeft',
            'imageStyle:alignCenter',
            'imageStyle:alignRight',
            '|',
            'resizeImage',
            '|',
            'toggleImageCaption',
            'imageTextAlternative'
        ]
    }
};

/* ================= EXPORT + GLOBAL ================= */
window.ClassicEditor = Editor;
export default Editor;
