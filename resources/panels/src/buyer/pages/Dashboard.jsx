import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { ShoppingCart, FolderKanban, CheckCircle, Wallet as WalletIcon, ArrowRight, Package, Layers } from 'lucide-react';
import { Link } from 'react-router-dom';
import StatCard from '../../shared/components/StatCard';
import api from '../../shared/api';

const fallbackStats = [
    { icon: <ShoppingCart />, label: 'Total Orders', value: 0, color: 'blue' },
    { icon: <FolderKanban />, label: 'Active Projects', value: 0, color: 'orange' },
    { icon: <CheckCircle />, label: 'Completed', value: 0, color: 'green' },
    { icon: <WalletIcon />, label: 'Wallet Balance', value: '₹0', color: 'red' },
];

function fmtMoney(n) {
    return `₹${Number(n || 0).toLocaleString('en-IN')}`;
}

const orderBadge = { active: 'badge-primary', completed: 'badge-success', pending: 'badge-warning', cancelled: 'badge-danger', delivered: 'badge-info' };
const projectBadge = { open: 'badge-primary', in_progress: 'badge-warning', completed: 'badge-success', cancelled: 'badge-danger' };

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.06 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const slideIn = {
    hidden: { opacity: 0, x: -16 },
    show: { opacity: 1, x: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function Dashboard({ user }) {
    const [stats, setStats] = useState(fallbackStats);
    const [orders, setOrders] = useState([]);
    const [projects, setProjects] = useState([]);

    useEffect(() => {
        api.get('/buyer/dashboard')
            .then(({ data }) => {
                const s = data.stats || {};
                setStats([
                    { icon: <ShoppingCart />, label: 'Total Orders', value: s.total_orders ?? 0, color: 'blue' },
                    { icon: <FolderKanban />, label: 'Active Projects', value: s.active_projects ?? 0, color: 'orange' },
                    { icon: <CheckCircle />, label: 'Completed', value: s.completed_projects ?? 0, color: 'green' },
                    { icon: <WalletIcon />, label: 'Wallet Balance', value: fmtMoney(s.wallet_balance), color: 'red' },
                ]);
                if (Array.isArray(data.recent_orders)) setOrders(data.recent_orders);
                if (Array.isArray(data.recent_projects)) setProjects(data.recent_projects);
            })
            .catch(() => {});
    }, []);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <h1 style={{ background: 'linear-gradient(135deg, var(--accent), #a78bfa)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent' }}>
                    Welcome, {user.name || 'Buyer'}
                </h1>
                <p>Here's an overview of your account.</p>
            </motion.div>

            <motion.div className="stat-grid" variants={container} initial="hidden" animate="show">
                {stats.map((s, i) => <StatCard key={i} {...s} index={i} />)}
            </motion.div>

            <motion.div className="detail-grid" variants={container} initial="hidden" animate="show">
                <motion.div className="detail-box" variants={fadeUp}>
                    <div className="card-header">
                        <h3><Package size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Recent Orders</h3>
                        <Link to="/orders" className="btn btn-secondary btn-sm">View All <ArrowRight size={14} /></Link>
                    </div>
                    {orders.length === 0 ? (
                        <div className="empty-state" style={{ padding: '32px 16px' }}>
                            <ShoppingCart size={36} style={{ opacity: 0.3, marginBottom: 8 }} />
                            <p style={{ color: 'var(--text-muted)' }}>No orders yet</p>
                        </div>
                    ) : (
                        <motion.div variants={container} initial="hidden" animate="show">
                            {orders.map((o) => (
                                <motion.div key={o.id} className="detail-row" variants={slideIn}>
                                    <span className="label">
                                        <strong>#{o.id}</strong> — {o.service?.title}
                                        <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{o.seller?.full_name || 'Seller'}</span>
                                    </span>
                                    <span className="value" style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                                        {fmtMoney(o.amount)}
                                        <span className={`badge ${orderBadge[o.status] || 'badge-info'}`}>{o.status}</span>
                                    </span>
                                </motion.div>
                            ))}
                        </motion.div>
                    )}
                </motion.div>

                <motion.div className="detail-box" variants={fadeUp}>
                    <div className="card-header">
                        <h3><Layers size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Recent Projects</h3>
                        <Link to="/projects" className="btn btn-secondary btn-sm">View All <ArrowRight size={14} /></Link>
                    </div>
                    {projects.length === 0 ? (
                        <div className="empty-state" style={{ padding: '32px 16px' }}>
                            <FolderKanban size={36} style={{ opacity: 0.3, marginBottom: 8 }} />
                            <p style={{ color: 'var(--text-muted)' }}>No projects yet</p>
                        </div>
                    ) : (
                        <motion.div variants={container} initial="hidden" animate="show">
                            {projects.map((p) => (
                                <motion.div key={p.id} className="detail-row" variants={slideIn}>
                                    <span className="label">
                                        <strong>{p.title}</strong>
                                        <br /><span style={{ color: 'var(--text-muted)', fontSize: 12 }}>{p.seller?.full_name || 'Seller'}</span>
                                    </span>
                                    <span className="value" style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                                        {fmtMoney(p.budget)}
                                        <span className={`badge ${projectBadge[p.status] || 'badge-info'}`}>{p.status?.replace('_', ' ')}</span>
                                    </span>
                                </motion.div>
                            ))}
                        </motion.div>
                    )}
                </motion.div>
            </motion.div>
        </motion.div>
    );
}

export default Dashboard;
