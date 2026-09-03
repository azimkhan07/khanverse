import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { ShoppingCart, DollarSign, FolderKanban, Star, ArrowRight, TrendingUp, PackageSearch, FolderMinus } from 'lucide-react';
import { Link } from 'react-router-dom';
import StatCard from '../../shared/components/StatCard';
import api from '../../shared/api';

const orderBadge = { completed: 'badge-success', active: 'badge-primary', pending: 'badge-warning', delivered: 'badge-info', cancelled: 'badge-danger' };
const projectBadge = { open: 'badge-primary', in_progress: 'badge-warning', completed: 'badge-success', cancelled: 'badge-danger' };

function fmtMoney(n) {
    return `₹${Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 })}`;
}

const fadeUp = {
    hidden: { opacity: 0, y: 28 },
    visible: (i = 0) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.1, ease: [0.22, 1, 0.36, 1] },
    }),
};

const stagger = {
    visible: { transition: { staggerChildren: 0.1 } },
};

function Dashboard({ user }) {
    const [stats, setStats] = useState([]);
    const [orders, setOrders] = useState([]);
    const [projects, setProjects] = useState([]);

    useEffect(() => {
        api.get('/seller/dashboard')
            .then(({ data }) => {
                const s = data.stats || {};
                const iconColor = ['blue', 'green', 'orange', 'red'];
                const fallbackCards = [
                    { label: 'Total Orders', value: s.total_orders ?? 0 },
                    { label: 'Total Earnings', value: fmtMoney(s.total_earnings) },
                    { label: 'Active Projects', value: s.active_projects ?? 0 },
                    { label: 'Avg Rating', value: (s.average_rating ?? 0).toFixed(1) },
                ];
                setStats(fallbackCards.map((c, i) => ({ ...c, icon: <StatIcon i={i} />, color: iconColor[i] })));
                if (Array.isArray(data.recent_orders)) setOrders(data.recent_orders);
                if (Array.isArray(data.recent_projects)) setProjects(data.recent_projects);
            })
            .catch(() => {});
    }, []);

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading" variants={fadeUp}>
                <h1 style={{
                    background: 'linear-gradient(135deg, var(--accent), #a78bfa, #f472b6)',
                    WebkitBackgroundClip: 'text',
                    WebkitTextFillColor: 'transparent',
                    backgroundClip: 'text',
                }}>
                    Welcome back, {user.name || 'Seller'}
                </h1>
                <p>Here&apos;s an overview of your seller activity.</p>
            </motion.div>

            <motion.div className="stat-grid" variants={fadeUp} custom={1}>
                {stats.map((s, i) => (
                    <StatCard key={i} icon={s.icon} label={s.label} value={s.value} color={s.color} index={i} />
                ))}
            </motion.div>

            <motion.div className="detail-grid" variants={fadeUp} custom={2}>
                <div className="detail-box" style={{ overflow: 'hidden' }}>
                    <div className="card-header">
                        <h3>Recent Orders</h3>
                        <Link to="/orders" className="btn btn-secondary btn-sm">View All <ArrowRight size={14} /></Link>
                    </div>
                    {orders.length === 0 ? (
                        <motion.div
                            className="empty-state"
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ delay: 0.3, duration: 0.4 }}
                        >
                            <PackageSearch size={48} style={{ color: 'var(--text-muted)', marginBottom: 12 }} strokeWidth={1.2} />
                            <p>No recent orders yet</p>
                        </motion.div>
                    ) : (
                        <motion.div initial="hidden" animate="visible" variants={stagger}>
                            {orders.map((o) => (
                                <motion.div
                                    key={o.id || o.order_id}
                                    className="detail-row"
                                    variants={fadeUp}
                                    whileHover={{ x: 4, backgroundColor: 'rgba(255,255,255,0.02)', transition: { duration: 0.2 } }}
                                    style={{ borderRadius: 8, padding: '6px 4px' }}
                                >
                                    <span className="label">
                                        <strong>#{o.order_id || o.id}</strong> — {o.buyer?.full_name || 'Buyer'}
                                        <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{o.service?.title}</span>
                                    </span>
                                    <span className="value" style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                                        {fmtMoney(o.amount)}
                                        <span className={`badge ${orderBadge[o.status] || 'badge-info'}`}>{o.status}</span>
                                    </span>
                                </motion.div>
                            ))}
                        </motion.div>
                    )}
                </div>

                <div className="detail-box" style={{ overflow: 'hidden' }}>
                    <div className="card-header">
                        <h3>Recent Projects</h3>
                        <Link to="/projects" className="btn btn-secondary btn-sm">View All <ArrowRight size={14} /></Link>
                    </div>
                    {projects.length === 0 ? (
                        <motion.div
                            className="empty-state"
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ delay: 0.3, duration: 0.4 }}
                        >
                            <FolderMinus size={48} style={{ color: 'var(--text-muted)', marginBottom: 12 }} strokeWidth={1.2} />
                            <p>No recent projects yet</p>
                        </motion.div>
                    ) : (
                        <motion.div initial="hidden" animate="visible" variants={stagger}>
                            {projects.map((p) => (
                                <motion.div
                                    key={p.id}
                                    className="detail-row"
                                    variants={fadeUp}
                                    whileHover={{ x: 4, backgroundColor: 'rgba(255,255,255,0.02)', transition: { duration: 0.2 } }}
                                    style={{ borderRadius: 8, padding: '6px 4px' }}
                                >
                                    <span className="label">
                                        <strong>{p.title}</strong>
                                        <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{p.buyer?.full_name}</span>
                                    </span>
                                    <span className="value" style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                                        {fmtMoney(p.budget)}
                                        <span className={`badge ${projectBadge[p.status] || 'badge-info'}`}>{p.status?.replace('_', ' ')}</span>
                                    </span>
                                </motion.div>
                            ))}
                        </motion.div>
                    )}
                </div>
            </motion.div>
        </motion.div>
    );
}

function StatIcon({ i }) {
    const icons = [<ShoppingCart />, <DollarSign />, <FolderKanban />, <Star />];
    return icons[i] || <TrendingUp />;
}

export default Dashboard;
