import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { BellRing, Save, Settings as SettingsIcon } from 'lucide-react';
import api from '../../shared/api';

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const tabContent = {
    hidden: { opacity: 0, x: 12 },
    show: { opacity: 1, x: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
    exit: { opacity: 0, x: -12, transition: { duration: 0.2 } },
};

function Settings() {
    const [form, setForm] = useState({ email_notifications: true, push_notifications: true });
    const [message, setMessage] = useState(null);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        api.get('/buyer/settings').then(({ data }) => {
            const p = data.preferences || {};
            setForm({ email_notifications: p.email_notifications ?? true, push_notifications: p.push_notifications ?? true });
        }).catch(() => {});
    }, []);

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setMessage(null);
        try {
            await api.post('/buyer/settings', { preferences: form });
            setMessage({ type: 'success', text: 'Settings saved.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Failed.' });
        } finally {
            setSaving(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <h1><SettingsIcon size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Account Settings</h1>
                <p>Manage your preferences.</p>
            </motion.div>

            {message && <motion.div className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`} initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>{message.text}</motion.div>}

            <AnimatePresence mode="wait">
                <motion.div className="settings-wrap" key="settings-tab" variants={tabContent} initial="hidden" animate="show" exit="exit">
                    <h3 style={{ display: 'flex', alignItems: 'center', gap: 8 }}><BellRing size={18} /> Notifications</h3>
                    <form onSubmit={submit}>
                        <motion.div className="switch-row" initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.1 }}>
                            <div>
                                <strong>Email Notifications</strong>
                                <div style={{ fontSize: 13, color: 'var(--text-muted)' }}>Receive updates via email.</div>
                            </div>
                            <input type="checkbox" className="switch" checked={form.email_notifications} onChange={(e) => setForm((p) => ({ ...p, email_notifications: e.target.checked }))} />
                        </motion.div>
                        <motion.div className="switch-row" initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.2 }}>
                            <div>
                                <strong>Push Notifications</strong>
                                <div style={{ fontSize: 13, color: 'var(--text-muted)' }}>Receive updates in-app.</div>
                            </div>
                            <input type="checkbox" className="switch" checked={form.push_notifications} onChange={(e) => setForm((p) => ({ ...p, push_notifications: e.target.checked }))} />
                        </motion.div>
                        <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.35, delay: 0.3 }}>
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <button type="submit" className="btn btn-primary" disabled={saving}><Save size={16} /> {saving ? 'Saving...' : 'Save Settings'}</button>
                            </motion.div>
                        </motion.div>
                    </form>
                </motion.div>
            </AnimatePresence>
        </motion.div>
    );
}

export default Settings;
