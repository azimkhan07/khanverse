import { useEffect } from 'react';
import ReactDOM from 'react-dom';
import { X } from 'lucide-react';

export default function Modal({ open, title, onClose, children, width }) {
    useEffect(() => {
        if (!open) return;
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [open, onClose]);

    if (!open) return null;
    return ReactDOM.createPortal(
        <div className="modal-overlay" onClick={onClose} role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div className="modal-box" style={width ? { width } : {}} onClick={(e) => e.stopPropagation()}>
                <div className="modal-header">
                    <h3 id="modal-title">{title}</h3>
                    <button className="modal-close" onClick={onClose} aria-label="Close"><X size={16} /></button>
                </div>
                <div className="modal-body">{children}</div>
            </div>
        </div>,
        document.body
    );
}
