import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { BellRing, ShoppingCart, FolderKanban, Wallet, Star, Inbox } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fallbackData = [
    { id: 1, title: 'New order received', message: 'You have a new order for Logo Design.', time: '10 min ago', type: 'order', read: false },
    { id: 2, title: 'Project in progress', message: 'E-commerce Redesign has moved to in progress.', time: '1 hour ago', type: 'project', read: false },
    { id: 3, title: 'Payment received', message: 'Your wallet was credited ₹500.', time: '3 hours ago', type: 'wallet', read: true },
    { id: 4, title: 'New review', message: 'Alice left a 5-star review.', time: '1 day ago', type: 'review', read: true },
];

const icons = { order: <ShoppingCart />, project: <FolderKanban />, wallet: <Wallet />, review: <Star /> };

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const item = {
    hidden: { opacity: 0, y: 14, scale: 0.98 },
    show: { opacity: 1, y: 0, scale: 1, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function Notifications() {
    const [data, setData] = useState(fallbackData);
    const navigate = useNavigate();

    useEffect(() => {
        api.get('/seller/notifications').then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => {});
    }, []);

    const markAllRead = async () => {
        setData((prev) => prev.map((n) => ({ ...n, read: true })));
        try { await api.post('/seller/notifications/read-all'); } catch { /* ignore */ }
    };

    const openNotification = async (n) => {
        if (!n.read) {
            setData((prev) => prev.map((x) => (x.id === n.id ? { ...x, read: true } : x)));
            try { await api.post(`/seller/notifications/${n.id}/read`); } catch { /* ignore */ }
        }
        if (n.url && n.url !== '#') {
            const path = n.url;
            if (/^https?:\/\//.test(path)) {
                const u = new URL(path);
                navigate(u.pathname + u.search);
            } else if (path.startsWith('/')) {
                navigate(path);
            } else {
                navigate('/' + path);
            }
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <BellRing size={24} style={{ color: 'var(--accent)' }} />
                        Notifications
                    </h1>
                    <p>Stay updated with your activity.</p>
                </div>
                <button className="btn btn-secondary btn-sm" onClick={markAllRead}>Mark all as read</button>
            </motion.div>

            <motion.div
                className="detail-box"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
            >
                {data.length === 0 ? (
                    <motion.div
                        className="empty-state"
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        style={{ padding: '60px 20px', textAlign: 'center' }}
                    >
                        <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                            <Inbox size={28} style={{ color: 'var(--accent)' }} />
                        </div>
                        <h3 style={{ marginBottom: 6 }}>All caught up!</h3>
                        <p style={{ color: 'var(--text-muted)' }}>No notifications at the moment.</p>
                    </motion.div>
                ) : (
                    <motion.div variants={stagger} initial="hidden" animate="show">
                        {data.map((n) => (
                            <motion.div
                                key={n.id}
                                className={`notif-item ${n.read ? '' : 'unread'} ${n.url ? 'clickable' : ''}`}
                                variants={item}
                                onClick={() => openNotification(n)}
                                role={n.url ? 'button' : undefined}
                                whileHover={{ backgroundColor: 'var(--hover-bg, rgba(0,0,0,0.02))', transition: { duration: 0.15 } }}
                            >
                                <motion.div
                                    className={`notif-icon ${n.type}`}
                                    whileHover={{ scale: 1.15, rotate: 5 }}
                                    transition={{ duration: 0.2 }}
                                >
                                    {icons[n.type] || <BellRing />}
                                </motion.div>
                                <div style={{ flex: 1 }}>
                                    <div className="notif-title">{n.title}</div>
                                    <div className="notif-msg">{n.message}</div>
                                    <div className="notif-time">{n.time || (n.created_at ? new Date(n.created_at).toLocaleString() : '')}</div>
                                </div>
                                {!n.read && (
                                    <motion.span
                                        className="badge badge-primary"
                                        initial={{ scale: 0 }}
                                        animate={{ scale: 1 }}
                                        transition={{ duration: 0.3, type: 'spring', stiffness: 300 }}
                                    >
                                        New
                                    </motion.span>
                                )}
                            </motion.div>
                        ))}
                    </motion.div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default Notifications;
