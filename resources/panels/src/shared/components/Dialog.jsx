import { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { CheckCircle2, XCircle, AlertTriangle, Info, X } from 'lucide-react';
import './dialog.css';

const listeners = new Set();
let currentState = null;
let currentId = 0;

export function showDialog(options) {
    const opts = typeof options === 'string' ? { message: options } : options;
    const id = ++currentId;
    const promise = new Promise((resolve) => {
        currentState = { ...opts, resolve, id };
        listeners.forEach((fn) => fn(currentState));
    });
    return promise;
}

const META = {
    success: { icon: CheckCircle2, confirmText: 'OK', title: 'Success' },
    danger: { icon: XCircle, confirmText: 'Got it', title: 'Error', action: 'danger' },
    warning: { icon: AlertTriangle, confirmText: 'Confirm', title: 'Warning', action: 'warning' },
    info: { icon: Info, confirmText: 'OK', title: 'Information' },
};

export default function DialogHost() {
    const [state, setState] = useState(null);

    useEffect(() => {
        const update = (s) => setState(s);
        listeners.add(update);
        update(currentState);
        return () => { listeners.delete(update); };
    }, []);

    const close = (result) => {
        if (state?.resolve) state.resolve(!!result);
        currentState = null;
        listeners.forEach((fn) => fn(null));
    };

    const type = state?.type || 'info';
    const meta = META[type] || META.info;
    const Icon = meta.icon;
    const action = state?.action || meta.action;
    const actionBtn = action === 'danger' ? 'btn-danger' : action === 'warning' ? 'btn-warning' : 'btn-primary';

    return (
        <AnimatePresence>
            {state && (
                <motion.div
                    className={`dialog-overlay dialog-overlay--${type}`}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.2 }}
                    onClick={() => close(false)}
                >
                    <motion.div
                        className="dialog-box"
                        initial={{ opacity: 0, scale: 0.9, y: 24 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 12 }}
                        transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
                        onClick={(e) => e.stopPropagation()}
                    >
                        <button className="dialog-x" onClick={() => close(false)} aria-label="Close"><X size={15} /></button>
                        <motion.span
                            className={`dialog-icon dialog-icon--${type}`}
                            initial={{ scale: 0, rotate: -180 }}
                            animate={{ scale: 1, rotate: 0 }}
                            transition={{ delay: 0.1, type: 'spring', stiffness: 260, damping: 14 }}
                        >
                            <Icon size={24} strokeWidth={2.2} />
                        </motion.span>
                        <h4>{state.title || meta.title}</h4>
                        {state.message && <div className="dialog-body">{state.message}</div>}
                        <div className="dialog-actions">
                            <motion.button
                                className={`btn ${actionBtn}`}
                                onClick={() => close(true)}
                                whileHover={{ scale: 1.04 }}
                                whileTap={{ scale: 0.97 }}
                            >
                                {state.confirmText || meta.confirmText}
                            </motion.button>
                        </div>
                    </motion.div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}
