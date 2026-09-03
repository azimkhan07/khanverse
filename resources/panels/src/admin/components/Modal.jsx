import ReactDOM from 'react-dom';
import { X } from 'lucide-react';

export default function Modal({ open, title, onClose, children, width }) {
    if (!open) return null;
    return ReactDOM.createPortal(
        <div className="modal-overlay" onClick={onClose}>
            <div className="modal-box" style={width ? { width } : {}} onClick={(e) => e.stopPropagation()}>
                <div className="modal-header">
                    <h3>{title}</h3>
                    <button className="modal-close" onClick={onClose} aria-label="Close"><X size={16} /></button>
                </div>
                <div className="modal-body">{children}</div>
            </div>
        </div>,
        document.body
    );
}
