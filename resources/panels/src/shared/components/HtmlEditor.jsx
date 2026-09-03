import { useEffect, useRef, useState } from 'react';
import {
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Heading2,
    Heading3,
    List,
    ListOrdered,
    Quote,
    Link2,
    Image,
    RemoveFormatting,
    Code,
} from 'lucide-react';

const TOOLS = [
    { icon: Bold, cmd: 'bold', title: 'Bold' },
    { icon: Italic, cmd: 'italic', title: 'Italic' },
    { icon: Underline, cmd: 'underline', title: 'Underline' },
    { icon: Strikethrough, cmd: 'strikeThrough', title: 'Strike through' },
    { icon: Heading2, cmd: 'formatBlock', value: 'h2', title: 'Heading 2' },
    { icon: Heading3, cmd: 'formatBlock', value: 'h3', title: 'Heading 3' },
    { icon: List, cmd: 'insertUnorderedList', title: 'Bullet list' },
    { icon: ListOrdered, cmd: 'insertOrderedList', title: 'Numbered list' },
    { icon: Quote, cmd: 'formatBlock', value: 'blockquote', title: 'Quote' },
    { icon: Link2, cmd: 'createLink', title: 'Insert link' },
    { icon: Image, cmd: 'insertImage', title: 'Insert image (URL)' },
    { icon: RemoveFormatting, cmd: 'removeFormat', title: 'Clear formatting' },
];

function HtmlEditor({ value = '', onChange, placeholder = 'Write content…', minHeight = 160, help }) {
    const ref = useRef(null);
    const [mode, setMode] = useState('visual');
    const [src, setSrc] = useState('');

    useEffect(() => {
        if (mode === 'visual' && ref.current && ref.current.innerHTML !== value) {
            ref.current.innerHTML = value || '';
        }
    }, [value, mode]);

    const emit = () => {
        if (ref.current) onChange(ref.current.innerHTML);
    };

    const run = (cmd, val) => {
        ref.current?.focus();
        document.execCommand(cmd, false, val);
        emit();
    };

    const onTool = (e, tool) => {
        e.preventDefault();
        if (tool.cmd === 'createLink') {
            const url = window.prompt('Link URL:', 'https://');
            if (url) run('createLink', url);
        } else if (tool.cmd === 'insertImage') {
            const url = window.prompt('Image URL:', 'https://');
            if (url) run('insertImage', url);
        } else {
            run(tool.cmd, tool.value);
        }
    };

    const toggleMode = () => {
        if (mode === 'visual') {
            setSrc(value);
            setMode('source');
        } else {
            onChange(src);
            setMode('visual');
        }
    };

    return (
        <div className="html-editor">
            <div className="html-editor-toolbar">
                {TOOLS.map((t, i) => {
                    const Icon = t.icon;
                    return (
                        <button key={i} type="button" className="html-editor-btn" title={t.title} onMouseDown={(e) => onTool(e, t)}>
                            <Icon size={14} />
                        </button>
                    );
                })}
                <span className="html-editor-fill" />
                <button type="button" className={`html-editor-btn ${mode === 'source' ? 'on' : ''}`} title="Toggle HTML source" onClick={toggleMode}>
                    <Code size={14} />
                </button>
            </div>
            {mode === 'visual' ? (
                <div
                    ref={ref}
                    className="html-editor-area"
                    data-placeholder={placeholder}
                    contentEditable
                    suppressContentEditableWarning
                    onInput={emit}
                    onKeyUp={emit}
                    onBlur={emit}
                    onPaste={() => setTimeout(emit, 0)}
                    style={{ minHeight }}
                />
            ) : (
                <textarea
                    className="html-editor-src"
                    value={src}
                    onChange={(e) => setSrc(e.target.value)}
                    placeholder={placeholder}
                    style={{ minHeight }}
                />
            )}
            {help && <p className="html-editor-help">{help}</p>}
        </div>
    );
}

export default HtmlEditor;