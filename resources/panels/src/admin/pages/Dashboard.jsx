import { useState, useEffect } from 'react';
import { ShoppingCart, Wallet, Briefcase, Users, FolderKanban, Layers } from 'lucide-react';
import api from '../../shared/api';
import { fmtMoney, orderStatusBadge, fmtTime } from '../helpers';

const FallbackStats = () => [
    { icon: <ShoppingCart size={17} />, label: 'Total Orders', value: '0', color: 'blue' },
    { icon: <Wallet size={17} />, label: 'Revenue', value: fmtMoney(0), color: 'green' },
    { icon: <Briefcase size={17} />, label: 'Services', value: '0', color: 'orange' },
    { icon: <FolderKanban size={17} />, label: 'Projects', value: '0', color: 'purple' },
    { icon: <Users size={17} />, label: 'Buyers', value: '0', color: 'cyan' },
    { icon: <Layers size={17} />, label: 'Sellers', value: '0', color: 'red' },
];

function Dashboard() {
    const [stats, setStats] = useState(FallbackStats());
    const [recentOrders, setRecentOrders] = useState([]);
    const [activities, setActivities] = useState([]);

    useEffect(() => {
        api.get('/admin/dashboard/stats')
            .then((res) => {
                const d = res.data;
                if (d.stats) {
                    setStats([
                        { icon: <ShoppingCart size={17} />, label: 'Total Orders', value: d.stats.total_orders ?? 0, color: 'blue' },
                        { icon: <Wallet size={17} />, label: 'Revenue', value: fmtMoney(d.stats.total_revenue), color: 'green' },
                        { icon: <Briefcase size={17} />, label: 'Services', value: d.stats.total_services ?? 0, color: 'orange' },
                        { icon: <FolderKanban size={17} />, label: 'Projects', value: d.stats.total_projects ?? 0, color: 'purple' },
                        { icon: <Users size={17} />, label: 'Buyers', value: d.stats.total_buyers ?? 0, color: 'cyan' },
                        { icon: <Layers size={17} />, label: 'Sellers', value: d.stats.total_sellers ?? 0, color: 'red' },
                    ]);
                }
                if (Array.isArray(d.recent_orders)) setRecentOrders(d.recent_orders);
                if (Array.isArray(d.activities)) setActivities(d.activities);
            })
            .catch(() => {});
    }, []);

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1>Dashboard</h1>
                    <p>Platform overview</p>
                </div>
            </div>

            <div className="stat-grid">
                {stats.map((s, i) => (
                    <div className="stat-card" key={i}>
                        <div className={`stat-icon ${s.color}`}>{s.icon}</div>
                        <div>
                            <h3>{s.value}</h3>
                            <p>{s.label}</p>
                        </div>
                    </div>
                ))}
            </div>

            <div className="admin-grid">
                <div className="admin-card">
                    <div className="card-head"><h3>Recent Orders</h3></div>
                    <div className="card-body" style={{ padding: 0 }}>
                        {recentOrders.length === 0 ? (
                            <div className="empty">No orders yet</div>
                        ) : (
                            <table>
                                <thead>
                                    <tr><th>ID</th><th>Buyer</th><th>Seller</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                    {recentOrders.map((o) => (
                                        <tr key={o.id}>
                                            <td>#{o.id}</td>
                                            <td>{o.buyer?.full_name || o.buyer?.name || '-'}</td>
                                            <td>{o.seller?.full_name || o.seller?.name || '-'}</td>
                                            <td>{fmtMoney(o.amount)}</td>
                                            <td><span className={`badge ${orderStatusBadge[o.status] || 'badge-info'}`}>{o.status}</span></td>
                                            <td>{fmtTime(o.created_at)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>

                <div className="admin-card">
                    <div className="card-head"><h3>Recent Activity</h3></div>
                    <div className="card-body">
                        {activities.length === 0 ? (
                            <div className="empty">No activity yet</div>
                        ) : (
                            <ul className="activity-list">
                                {activities.map((a, i) => (
                                    <li key={i}>
                                        <span className={`activity-dot ${a.type}`} />
                                        <div style={{ flex: 1 }}>
                                            <div style={{ fontSize: 12, color: 'var(--text-primary)' }}>{a.title}</div>
                                            <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{a.time}</div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Dashboard;
