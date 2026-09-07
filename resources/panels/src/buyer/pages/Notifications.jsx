import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Bell, CheckCheck, BellOff } from 'lucide-react';
import api from '../../shared/api';

const fallbackData = [
    { id: 1, title: 'Welcome', body: 'Welcome to SkillNest buyer panel.', is_read: 0, created_at: '2026-08-20' },
];

const iconMap = {
    order: '🛒', project: '📁', payment: '💰', review: '⭐', message: '💬', system: '🔔', default: '🔔',
};

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.05 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const slideIn = {
    hidden: { opacity: 0, x: -16 },
    show: { opacity: 1, x: 0, transition: { duration: 0.35, ease: [0.22, 1, 0.36, 1] } },
};

function Notifications() {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/buyer/notifications').then(({ data: res }) => {
            const list = Array.isArray(res) ? res : (Array.isArray(res.data) ? res.data : []);
            setData(list);
        }).catch(() => setData(fallbackData)).finally(() => setLoading(false));
    }, []);

    const markAll = async () => {
        try { await api.post('/buyer/notifications/read-all');         setData((d) => d.map((n) => ({ ...n, read: 1 }))); } catch { /* ignore */ }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div><h1><Bell size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />Notifications</h1><p>Stay updated on your activity.</p></div>
                <motion.div whileHover={{ scale: 1.04 }} whileTap={{ scale: 0.96 }}>
                    <button className="btn btn-secondary btn-sm" onClick={markAll}><CheckCheck size={14} /> Mark all read</button>
                </motion.div>
            </motion.div>

            {loading ? (
                <div className="empty-state"><motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}><p>Loading...</p></motion.div></div>
            ) : data.length === 0 ? (
                <motion.div className="empty-state" style={{ padding: '48px 16px' }} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}>
                    <BellOff size={40} style={{ opacity: 0.25, marginBottom: 12 }} />
                    <p style={{ color: 'var(--text-muted)', fontSize: 15 }}>No notifications yet.</p>
                </motion.div>
            ) : (
                <motion.div variants={container} initial="hidden" animate="show">
                    {data.map((n) => (
                        <motion.div key={n.id} className={`notif-item ${n.read || n.is_read ? '' : 'unread'}`} variants={slideIn}>
                            <span className="notif-icon">{iconMap[n.type || 'default'] || iconMap.default}</span>
                            <div className="notif-body">
                                <strong>{n.title}</strong>
                                <p>{n.body || n.message}</p>
                                <span className="notif-time">{n.time || (n.created_at ? new Date(n.created_at).toLocaleString() : '')}</span>
                            </div>
                            {!(n.read || n.is_read) && (
                                <motion.span
                                    className="notif-dot"
                                    animate={{ scale: [1, 1.3, 1] }}
                                    transition={{ duration: 1.5, repeat: Infinity, ease: 'easeInOut' }}
                                />
                            )}
                        </motion.div>
                    ))}
                </motion.div>
            )}
        </motion.div>
    );
}

export default Notifications;
