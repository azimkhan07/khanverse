import { X } from "lucide-react";

function Modal({ open, title, onClose, children, width }) {
    if (!open) return null;
    return (
        <div className="modal-overlay" onClick={onClose}>
            <div
                className="modal-box"
                style={width ? { maxWidth: width } : undefined}
                onClick={(e) => e.stopPropagation()}
            >
                <div className="modal-header">
                    <h3>{title}</h3>
                    <button className="modal-close" onClick={onClose} aria-label="Close">
                        <X size={18} />
                    </button>
                </div>
                <div className="modal-body">{children}</div>
            </div>
        </div>
    );
}

export default Modal;
