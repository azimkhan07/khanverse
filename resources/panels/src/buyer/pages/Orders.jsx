import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Eye, Package, CreditCard, XCircle } from 'lucide-react';
import { Link } from 'react-router-dom';
import DataTable from '../../shared/components/DataTable';
import FilterSelect from '../../shared/components/FilterSelect';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';

const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'pending', label: 'Accept Pending' },
    { value: 'active', label: 'In Progress' },
    { value: 'delivered', label: 'Delivered' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const fallbackData = [
    { id: 1001, seller: { full_name: 'Sara Ali' }, service: { title: 'Logo Design' }, amount: 50, status: 'active', delivery_date: '2026-08-25' },
    { id: 1002, seller: { full_name: 'Usman Tariq' }, service: { title: 'SEO Audit' }, amount: 150, status: 'completed', delivery_date: '2026-08-22' },
    { id: 1003, seller: { full_name: 'Hina Malik' }, service: { title: 'Content Writing' }, amount: 80, status: 'pending', delivery_date: '2026-09-05' },
];

const statusBadgeMap = { pending: 'badge-warning', active: 'badge-primary', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };
const paymentBadgeMap = { pending: 'badge-warning', paid: 'badge-success', failed: 'badge-danger' };

const statusLabels = {
    pending: 'Accept Pending',
    active: 'In Progress',
    delivered: 'Delivered',
    completed: 'Completed',
    cancelled: 'Cancelled',
};

const paymentLabels = { pending: 'Unpaid', paid: 'Paid', failed: 'Failed' };

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const rowVariant = {
    hidden: { opacity: 0, y: 12 },
    show: (i) => ({ opacity: 1, y: 0, transition: { duration: 0.35, delay: i * 0.04, ease: [0.22, 1, 0.36, 1] } }),
};

function Orders() {
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');

    useEffect(() => {
        api.get('/buyer/orders', { params: status ? { status } : {} })
            .then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, [status]);

    const cancelOrder = async (row) => {
        const ok = await showDialog({
            type: 'confirm',
            title: 'Cancel Order',
            message: `Are you sure you want to cancel order #${row.id}? Only pending, unpaid orders can be cancelled.`,
            confirmText: 'Yes, Cancel',
            cancelText: 'Keep Order',
        });
        if (!ok) return;
        try {
            const res = await api.post(`/buyer/orders/${row.id}/cancel`);
            await showDialog({ type: 'success', title: 'Cancelled', message: res.data?.message || 'Order cancelled.', confirmText: 'OK' });
            setData((prev) => prev.map((o) => (o.id === row.id ? { ...o, status: 'cancelled' } : o)));
        } catch (err) {
            await showDialog({ type: 'error', title: 'Cancellation Failed', message: err.response?.data?.message || 'Could not cancel this order.', confirmText: 'OK' });
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <h1><Package size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />My Orders</h1>
                    <p>Track and manage your orders.</p>
                </div>
                <div className="toolbar-filters">
                    <FilterSelect
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        options={statusOptions}
                    />
                </div>
            </motion.div>

            <DataTable
                columns={[
                    { key: 'id', label: 'Order' },
                    { key: 'service', label: 'Service' },
                    { key: 'seller', label: 'Seller' },
                    { key: 'amount', label: 'Amount' },
                    { key: 'status', label: 'Status' },
                    { key: 'payment', label: 'Payment' },
                    { key: 'action', label: 'Action' },
                ]}
                data={data}
                renderRow={(row, i) => (
                    <motion.tr key={row.id ?? i} custom={i} variants={rowVariant} initial="hidden" animate="show">
                        <td>#{row.id}</td>
                        <td>{row.service?.title || row.project?.title || '-'}</td>
                        <td>{row.seller?.full_name || '-'}</td>
                        <td>â‚¹{Number(row.amount).toLocaleString('en-IN')}</td>
                        <td><span className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}>{statusLabels[row.status] || row.status}</span></td>
                        <td>
                            <span className={`badge ${paymentBadgeMap[row.payment_status] || 'badge-warning'}`}>
                                {paymentLabels[row.payment_status] || row.payment_status || 'Unpaid'}
                            </span>
                        </td>
                        <td>
                            <div style={{ display: 'flex', gap: 6 }}>
                                <Link to={`/orders/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link>
                                {(row.payment_status === 'pending' || row.payment_status === 'failed') && (
                                    <Link to={`/checkout/${row.id}`} className="btn btn-primary btn-sm"><CreditCard size={14} /> Pay Now</Link>
                                )}
                                {row.status === 'pending' && row.payment_status !== 'paid' && (
                                    <button
                                        onClick={() => cancelOrder(row)}
                                        className="btn btn-danger btn-sm"
                                        style={{ display: 'inline-flex', alignItems: 'center', gap: 4 }}
                                    >
                                        <XCircle size={14} /> Cancel
                                    </button>
                                )}
                            </div>
                        </td>
                    </motion.tr>
                )}
            />
        </motion.div>
    );
}

export default Orders;
