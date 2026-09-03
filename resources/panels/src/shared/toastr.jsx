import toast from 'react-hot-toast';
import { CheckCircle2, XCircle, AlertTriangle, Info } from 'lucide-react';
import './toastr.css';

const ICONS = { success: CheckCircle2, danger: XCircle, warning: AlertTriangle, info: Info };

export function showToastr(type = 'info', message, options = {}) {
    const Icon = ICONS[type] || Info;
    toast(
        (t) => (
            <div className={`ta-toast ta-toast--${type}${t.visible ? ' ta-toast--show' : ''}`} role="status">
                <span className="ta-toast__icon"><Icon size={13} strokeWidth={2.6} /></span>
                <span className="ta-toast__msg">{message}</span>
            </div>
        ),
        { duration: 3500, ...options }
    );
}

const toastr = {
    success: (m, o) => showToastr('success', m, o),
    danger: (m, o) => showToastr('danger', m, o),
    warning: (m, o) => showToastr('warning', m, o),
    info: (m, o) => showToastr('info', m, o),
};

export default toastr;