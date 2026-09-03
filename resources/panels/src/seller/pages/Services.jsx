import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Plus, Edit2, Trash2, Eye, PackageCheck, PackageOpen } from 'lucide-react';
import { Link } from 'react-router-dom';
import DataTable from '../../shared/components/DataTable';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';

const fallbackData = [
    { id: 1, title: 'Logo Design', category: { name: 'Design' }, price: 50, status: 'active', thumbnail: null },
    { id: 2, title: 'Web Development', category: { name: 'Development' }, price: 500, status: 'active', thumbnail: null, delivery_method: 'hosting' },
    { id: 3, title: 'SEO Optimization', category: { name: 'Marketing' }, price: 150, status: 'paused', thumbnail: null },
    { id: 4, title: 'Content Writing', category: { name: 'Writing' }, price: 80, status: 'draft', thumbnail: null },
];

const statusBadgeMap = { active: 'badge-success', paused: 'badge-warning', draft: 'badge-info', inactive: 'badge-danger' };

const fadeUp = {
    hidden: { opacity: 0, y: 28 },
    visible: (i = 0) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.1, ease: [0.22, 1, 0.36, 1] },
    }),
};

const stagger = {
    visible: { transition: { staggerChildren: 0.06 } },
};

function Services() {
    const [data, setData] = useState(fallbackData);

    useEffect(() => {
        api.get('/seller/services').then(({ data: res }) => {
            if (res.data) setData(res.data);
        }).catch(() => {});
    }, []);

    const handleDelete = async (id) => {
        const ok = await showConfirm({ title: 'Please confirm', message: 'Delete this service?', type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/seller/services/${id}`);
            setData((prev) => prev.filter((r) => r.id !== id));
        } catch { /* ignore */ }
    };

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <h1>My Services</h1>
                    <p>Manage your listed services.</p>
                </div>
                <Link to="/services/new">
                    <motion.span
                        className="btn btn-primary"
                        style={{ position: 'relative', overflow: 'hidden', display: 'inline-flex', alignItems: 'center', gap: 6 }}
                        whileHover={{ scale: 1.05, boxShadow: '0 0 24px rgba(99,102,241,0.4), 0 0 48px rgba(99,102,241,0.15)' }}
                        whileTap={{ scale: 0.96 }}
                        transition={{ type: 'spring', stiffness: 400, damping: 20 }}
                    >
                        <Plus size={16} /> Add Service
                    </motion.span>
                </Link>
            </motion.div>

            <motion.div variants={fadeUp} custom={1}>
                <DataTable
                    columns={[
                        { key: 'id', label: 'ID' },
                        { key: 'title', label: 'Title' },
                        { key: 'category', label: 'Category' },
                        { key: 'delivery', label: 'Delivery' },
                        { key: 'price', label: 'Price' },
                        { key: 'status', label: 'Status' },
                        { key: 'actions', label: 'Actions' },
                    ]}
                    data={data}
                    renderRow={(row, i) => (
                        <motion.tr
                            key={row.id ?? i}
                            initial={{ opacity: 0, x: -16 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.35, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] }}
                        >
                            <td>{row.id}</td>
                            <td><Link to={`/services/${row.id}`} style={{ color: 'var(--accent)', fontWeight: 600 }}>{row.title}</Link></td>
                            <td>{row.category?.name || row.category || '-'}</td>
                            <td>
                                <span className={`badge ${row.delivery_method === 'hosting' ? 'badge-primary' : 'badge-info'}`} style={{ display: 'inline-flex', alignItems: 'center', gap: 4 }}>
                                    {row.delivery_method === 'hosting' ? <><PackageCheck size={11} /> Hosting</> : 'Digital'}
                                </span>
                            </td>
                            <td>₹{Number(row.price).toLocaleString('en-IN')}</td>
                            <td>
                                <motion.span
                                    className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}
                                    whileHover={{ scale: 1.1 }}
                                    transition={{ type: 'spring', stiffness: 400, damping: 17 }}
                                >
                                    {row.status}
                                </motion.span>
                            </td>
                            <td>
                                <div style={{ display: 'flex', gap: 6 }}>
                                    <Link to={`/services/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /></Link>
                                    <Link to={`/services/${row.id}/edit`} className="btn btn-secondary btn-sm"><Edit2 size={14} /></Link>
                                    <motion.button
                                        className="btn btn-danger btn-sm"
                                        onClick={() => handleDelete(row.id)}
                                        whileHover={{ scale: 1.08 }}
                                        whileTap={{ scale: 0.92 }}
                                    >
                                        <Trash2 size={14} />
                                    </motion.button>
                                </div>
                            </td>
                        </motion.tr>
                    )}
                />
            </motion.div>

            {data.length === 0 && (
                <motion.div
                    className="empty-state"
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: 0.3, duration: 0.5 }}
                >
                    <PackageOpen size={56} style={{ color: 'var(--text-muted)', marginBottom: 12 }} strokeWidth={1.2} />
                    <p>No services yet. Create your first service to start selling.</p>
                </motion.div>
            )}
        </motion.div>
    );
}

export default Services;
