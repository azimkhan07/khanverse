import { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { AlertTriangle, X } from 'lucide-react';

const listeners = new Set();
let currentState = null;
let currentId = 0;

export function showConfirm(options) {
    const opts = typeof options === 'string' ? { message: options } : options;
    const id = ++currentId;
    const promise = new Promise((resolve) => {
        currentState = { ...opts, resolve, id };
        listeners.forEach((fn) => fn(currentState));
    });
    return promise;
}

export default function ConfirmDialog() {
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

    const variant = state?.type === 'danger' ? 'danger' : state?.type === 'warning' ? 'warning' : '';

    return (
        <AnimatePresence>
            {state && (
                <motion.div
                    className={`confirm-overlay ${variant ? `confirm-${variant}` : ''}`}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.2 }}
                >
                    <motion.div
                        className={`confirm-box ${variant ? `confirm-box-${variant}` : ''}`}
                        initial={{ opacity: 0, scale: 0.9, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 10 }}
                        transition={{ duration: 0.25, ease: [0.22, 1, 0.36, 1] }}
                    >
                        <button className="confirm-x" onClick={() => close(false)}><X size={15} /></button>
                        <motion.div
                            className="confirm-icon"
                            initial={{ scale: 0 }}
                            animate={{ scale: 1 }}
                            transition={{ delay: 0.1, type: 'spring', stiffness: 300, damping: 15 }}
                        >
                            <AlertTriangle size={21} />
                        </motion.div>
                        <h4>{state.title || 'Please confirm'}</h4>
                        <p>{state.message}</p>
                        <div className="confirm-actions">
                            <button className="btn" onClick={() => close(false)}>{state.cancelText || 'Cancel'}</button>
                            <button className={`btn ${variant === 'danger' ? 'btn-danger' : variant === 'warning' ? 'btn-warning' : 'btn-primary'}`} onClick={() => close(true)}>
                                {state.confirmText || (variant === 'danger' ? 'Delete' : 'Confirm')}
                            </button>
                        </div>
                    </motion.div>
                </motion.div>
            )}
        </AnimatePresence>
    );
}
