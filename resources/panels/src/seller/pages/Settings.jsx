import { useState } from 'react';
import { Bell, Lock, Shield, LifeBuoy, Info, Settings as SettingsIcon } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';

const tabs = [
    { key: 'notifications', label: 'Notifications', icon: <Bell size={16} /> },
    { key: 'security', label: 'Security', icon: <Lock size={16} /> },
    { key: 'privacy', label: 'Privacy', icon: <Shield size={16} /> },
    { key: 'help', label: 'Help', icon: <LifeBuoy size={16} /> },
];

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const tabContent = {
    hidden: { opacity: 0, x: 12 },
    show: { opacity: 1, x: 0, transition: { duration: 0.35, ease: [0.22, 1, 0.36, 1] } },
    exit: { opacity: 0, x: -12, transition: { duration: 0.2 } },
};

function SettingsPage() {
    const [tab, setTab] = useState('notifications');
    const [message, setMessage] = useState(null);
    const [prefs, setPrefs] = useState({
        email_notifications: true, push_notifications: true, order_updates: true,
        two_factor: false,
    });

    const togglePref = (key) => setPrefs((p) => ({ ...p, [key]: !p[key] }));

    const save = async () => {
        setMessage(null);
        try {
            await api.post('/seller/settings', { preferences: prefs });
            setMessage({ type: 'success', text: 'Settings saved.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Save failed.' });
        }
    };

    const openHelpInfo = () => {
        showDialog({
            type: 'info',
            title: 'Seller Help',
            message: 'Need help getting started? Contact our support team, check the FAQ section, or open a chat with us. We are here to help you succeed on SkillNest.',
        });
    };

    const renderTab = () => {
        switch (tab) {
            case 'notifications':
                return (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        {[
                            { key: 'email_notifications', label: 'Email notifications', desc: 'Receive updates via email' },
                            { key: 'push_notifications', label: 'Push notifications', desc: 'Real-time browser notifications' },
                            { key: 'order_updates', label: 'Order updates', desc: 'Get notified on order changes' },
                        ].map((p) => (
                            <motion.div key={p.key} className="detail-row" variants={fadeUp}>
                                <span className="label">
                                    <strong>{p.label}</strong>
                                    <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{p.desc}</span>
                                </span>
                                <span className="value">
                                    <label className="switch">
                                        <input type="checkbox" checked={prefs[p.key]} onChange={() => togglePref(p.key)} />
                                        <span className="slider" />
                                    </label>
                                </span>
                            </motion.div>
                        ))}
                    </motion.div>
                );
            case 'security':
                return (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        <motion.div className="detail-row" variants={fadeUp}>
                            <span className="label"><strong>Two-factor authentication</strong></span>
                            <span className="value">
                                <label className="switch">
                                    <input type="checkbox" checked={prefs.two_factor} onChange={() => togglePref('two_factor')} />
                                    <span className="slider" />
                                </label>
                            </span>
                        </motion.div>
                        <motion.div className="detail-box" variants={fadeUp} style={{ marginTop: 16 }}><p style={{ color: 'var(--text-secondary)' }}>Change password and manage sessions in your account page.</p></motion.div>
                    </motion.div>
                );
            case 'privacy':
                return (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        <motion.div className="detail-row" variants={fadeUp}>
                            <span className="label"><strong>Make profile public</strong></span>
                            <span className="value"><span className="badge badge-success">Public</span></span>
                        </motion.div>
                        <motion.div className="detail-row" variants={fadeUp}>
                            <span className="label"><strong>Show earnings</strong></span>
                            <span className="value"><label className="switch"><input type="checkbox" defaultChecked /><span className="slider" /></label></span>
                        </motion.div>
                    </motion.div>
                );
            case 'help':
                return (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        <motion.p variants={fadeUp} style={{ color: 'var(--text-secondary)', lineHeight: 1.7 }}>
                            Need help? Contact our support team or check the FAQ. We're here to help you succeed.
                        </motion.p>
                        <motion.button className="btn btn-secondary btn-sm" style={{ marginTop: 14 }} onClick={openHelpInfo} variants={fadeUp} whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                            <Info size={15} /> More Info
                        </motion.button>
                    </motion.div>
                );
            default:
                return null;
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <SettingsIcon size={24} style={{ color: 'var(--accent)' }} />
                    Account Settings
                </h1>
                <p>Manage your preferences.</p>
            </motion.div>

            {message && (
                <motion.div
                    className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`}
                    initial={{ opacity: 0, y: -10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35 }}
                >
                    {message.text}
                </motion.div>
            )}

            <motion.div
                className="settings-wrap"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
            >
                <div className="settings-menu">
                    {tabs.map((t) => (
                        <motion.button
                            key={t.key}
                            className={`tab-btn ${tab === t.key ? 'active' : ''}`}
                            onClick={() => setTab(t.key)}
                            whileHover={{ x: 2 }}
                            whileTap={{ scale: 0.97 }}
                        >
                            {t.icon} {t.label}
                        </motion.button>
                    ))}
                </div>
                <div className="detail-box" style={{ flex: 1 }}>
                    <h3 style={{ marginBottom: 8 }}>{tabs.find((t) => t.key === tab)?.label}</h3>
                    <AnimatePresence mode="wait">
                        <motion.div key={tab} variants={tabContent} initial="hidden" animate="show" exit="exit">
                            {renderTab()}
                        </motion.div>
                    </AnimatePresence>
                    {tab !== 'help' && tab !== 'privacy' && (
                        <motion.button
                            className="btn btn-primary"
                            style={{ marginTop: 16 }}
                            onClick={save}
                            whileHover={{ scale: 1.02 }}
                            whileTap={{ scale: 0.98 }}
                        >
                            Save Settings
                        </motion.button>
                    )}
                </div>
            </motion.div>
        </motion.div>
    );
}

export default SettingsPage;
