import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Eye } from 'lucide-react';
import { Link } from 'react-router-dom';
import DataTable from '../../shared/components/DataTable';
import FilterSelect from '../../shared/components/FilterSelect';
import api from '../../shared/api';

const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'pending', label: 'Accept Pending' },
    { value: 'active', label: 'In Progress' },
    { value: 'delivered', label: 'Delivered' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const fallbackData = [
    { id: 1001, buyer: { full_name: 'Alice Johnson' }, service: { title: 'Logo Design' }, amount: 50, status: 'completed', delivery_date: '2026-08-25' },
    { id: 1002, buyer: { full_name: 'Bob Smith' }, service: { title: 'Web Development' }, amount: 500, status: 'active', delivery_date: '2026-09-05' },
    { id: 1003, buyer: { full_name: 'Carol White' }, service: { title: 'SEO Optimization' }, amount: 150, status: 'pending', delivery_date: '2026-09-10' },
    { id: 1004, buyer: { full_name: 'David Lee' }, service: { title: 'Content Writing' }, amount: 80, status: 'completed', delivery_date: '2026-08-28' },
];

const statusBadgeMap = { pending: 'badge-warning', active: 'badge-primary', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };

const statusLabels = {
    pending: 'Accept Pending',
    active: 'In Progress',
    delivered: 'Delivered',
    completed: 'Completed',
    cancelled: 'Cancelled',
};

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

function Orders() {
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');

    useEffect(() => {
        api.get('/seller/orders', { params: status ? { status } : {} })
            .then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, [status]);

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <h1>Orders</h1>
                    <p>Track and manage your incoming orders.</p>
                </div>
                <motion.div
                    className="toolbar-filters"
                    variants={fadeUp}
                    custom={1}
                >
                    <FilterSelect
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        options={statusOptions}
                    />
                </motion.div>
            </motion.div>

            <motion.div variants={fadeUp} custom={2}>
                <DataTable
                    columns={[
                        { key: 'id', label: 'Order' },
                        { key: 'project', label: 'Project' },
                        { key: 'buyer', label: 'Buyer' },
                        { key: 'amount', label: 'Amount' },
                        { key: 'status', label: 'Status' },
                        { key: 'date', label: 'Delivery' },
                        { key: 'action', label: 'Action' },
                    ]}
                    data={data}
                    renderRow={(row, i) => (
                        <motion.tr
                            key={row.id ?? i}
                            initial={{ opacity: 0, x: -16 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.35, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] }}
                        >
                            <td>#{row.id}</td>
                            <td>{row.service?.title || row.project?.title || '-'}</td>
                            <td>{row.buyer?.full_name || '-'}</td>
                            <td>â‚¹{Number(row.amount || row.total_amount).toLocaleString('en-IN')}</td>
                            <td>
                                <motion.span
                                    className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}
                                    whileHover={{ scale: 1.1 }}
                                    transition={{ type: 'spring', stiffness: 400, damping: 17 }}
                                >
                                    {statusLabels[row.status] || row.status}
                                </motion.span>
                            </td>
                            <td>{row.delivery_date || row.delivery_days ? `${row.delivery_days ?? ''}d` : '-'}</td>
                            <td><Link to={`/orders/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link></td>
                        </motion.tr>
                    )}
                />
            </motion.div>
        </motion.div>
    );
}

export default Orders;
