import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, CheckCircle2, Send, FolderOpen, FileDown, Eye } from 'lucide-react';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';

const fallbackOrder = {
    id: 1001, buyer: { full_name: 'Alice Johnson', email: 'alice@mail.com' },
    service: { title: 'Logo Design' }, amount: 50, status: 'active',
    delivery_date: '2026-09-05', created_at: '2026-08-20',
};

const statusBadgeMap = { pending: 'badge-warning', active: 'badge-primary', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger', disputed: 'badge-danger' };

const statusLabels = {
    pending: 'Accept Pending',
    active: 'In Progress',
    delivered: 'Delivered',
    completed: 'Completed',
    cancelled: 'Cancelled',
    disputed: 'Disputed',
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
    visible: { transition: { staggerChildren: 0.08 } },
};

function OrderDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(fallbackOrder);
    const [newStatus, setNewStatus] = useState('');
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        api.get(`/seller/orders/${id}`).then(({ data }) => {
            setOrder(data);
        }).catch(() => {});
    }, [id]);

    useEffect(() => {
        setNewStatus('');
    }, [order.status]);

    const doTransition = async (target, confirmTitle, confirmMsg) => {
        const confirmed = await showDialog({ type: 'warning', title: confirmTitle, message: confirmMsg, confirmText: 'Yes, continue' });
        if (!confirmed) return;
        setBusy(true);
        try {
            const { data } = await api.post(`/seller/orders/${id}/status`, { status: target });

            if (target === 'active' && data.delivery_key) {
                await showDialog({
                    type: 'info',
                    title: 'Project Started — Save This Delivery Key',
                    message: (
                        <div>
                            <p style={{ marginBottom: 8 }}>
                                This is a <strong>hosting / handover</strong> project. The buyer&apos;s hosting credentials will be locked until you verify the delivery key below.
                            </p>
                            <div style={{ background: '#eef2ff', borderRadius: 8, padding: '10px 14px', textAlign: 'center', margin: '10px 0' }}>
                                <strong style={{ letterSpacing: 3, fontSize: 18, fontFamily: 'monospace', color: '#4338ca' }}>{data.delivery_key}</strong>
                            </div>
                            <p style={{ fontSize: 13 }}>Keep it safe. If you lose it, use the <em>Forgot Key</em> option on the project to get a new one.</p>
                        </div>
                    ),
                    confirmText: 'Got it — saved',
                });
            } else {
                await showDialog({ type: 'success', title: 'Done', message: data.message || `Order is now "${statusLabels[target] || target}".` });
            }

            setOrder((prev) => ({ ...prev, status: target, project_id: data.order?.project_id ?? prev.project_id, invoices: data.order?.invoices ?? prev.invoices }));
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Action Failed', message: err.response?.data?.message || 'Action failed.' });
        } finally {
            setBusy(false);
        }
    };

    const updateStatus = async () => {
        if (!newStatus || newStatus === order.status) return;
        await doTransition(newStatus, 'Change Order Status', `Set order #${order.id} to "${statusLabels[newStatus] || newStatus}"? This will be visible to the buyer.`);
    };

    const projectDelivered = order.project?.status === 'delivered';

    const updateOptions =
        order.status === 'pending'
            ? [
                { value: 'active', label: 'Accept & Start Project' },
                { value: 'cancelled', label: 'Reject / Cancel' },
            ]
            : order.status === 'active'
            ? [
                ...(projectDelivered ? [{ value: 'delivered', label: 'Mark Delivered' }] : []),
            ]
            : order.status === 'delivered'
            ? [{ value: 'completed', label: 'Complete Order' }]
            : [];

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/orders')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Orders
                    </button>
                    <h1>Order #{order.id}</h1>
                </div>
                <motion.span
                    className={`badge ${statusBadgeMap[order.status] || 'badge-info'}`}
                    whileHover={{ scale: 1.1 }}
                    transition={{ type: 'spring', stiffness: 400, damping: 17 }}
                >
                    {statusLabels[order.status] || order.status}
                </motion.span>
            </motion.div>

            <motion.div className="detail-grid" variants={fadeUp} custom={1}>
                <motion.div className="detail-box" variants={fadeUp} custom={1}>
                    <h3>Order Information</h3>
                    <div className="detail-row"><span className="label">Service</span><span className="value">{order.service?.title || '-'}</span></div>
                    <div className="detail-row"><span className="label">Amount</span><span className="value">₹{Number(order.amount || order.total_amount).toLocaleString('en-IN')}</span></div>
                    <div className="detail-row"><span className="label">Placed</span><span className="value">{order.created_at ? new Date(order.created_at).toLocaleDateString() : '-'}</span></div>
                    <div className="detail-row"><span className="label">Delivery</span><span className="value">{order.delivery_date ? new Date(order.delivery_date).toLocaleDateString() : '-'}</span></div>
                    {order.requirements ? (
                        <div className="detail-row"><span className="label">Requirements</span><span className="value">{order.requirements}</span></div>
                    ) : null}
                </motion.div>
                <motion.div className="detail-box" variants={fadeUp} custom={2}>
                    <h3>Buyer</h3>
                    <div className="detail-row"><span className="label">Name</span><span className="value">{order.buyer?.full_name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Email</span><span className="value">{order.buyer?.email || '-'}</span></div>
                </motion.div>
            </motion.div>

            {order.project && (
                <motion.div className="detail-box" style={{ marginTop: 16 }} variants={fadeUp} custom={3}>
                    <div className="card-header"><h3>Linked Project</h3></div>
                    <div className="detail-row">
                        <span className="label">Project</span>
                        <span className="value"><Link to={`/projects/${order.project.id}`} style={{ color: 'var(--accent)', fontWeight: 600 }}><FolderOpen size={14} style={{ verticalAlign: 'middle', marginRight: 4 }} />{order.project.title} (#{order.project.id})</Link></span>
                    </div>
                    <div className="detail-row"><span className="label">Status</span><span className="value">{order.project.status?.replace('_', ' ')}</span></div>
                </motion.div>
            )}

            {order.status === 'pending' && (
                <motion.div
                    className="detail-box"
                    style={{ marginTop: 16 }}
                    variants={fadeUp}
                    custom={4}
                    whileHover={{ boxShadow: '0 8px 32px rgba(99,102,241,0.08)' }}
                    transition={{ duration: 0.25 }}
                >
                    <div className="card-header"><h3>Order Confirmation</h3></div>
                    <p style={{ color: 'var(--text-muted)', marginBottom: 12, fontSize: 14 }}>Accepting this order will automatically create a linked project.</p>
                    <div className="status-form">
                        <motion.button
                            className="btn btn-primary"
                            disabled={busy}
                            onClick={() => doTransition('active', 'Accept Order', 'Accept this order and start a project?')}
                            whileHover={{ scale: 1.03 }}
                            whileTap={{ scale: 0.97 }}
                        >
                            <CheckCircle2 size={16} /> {busy ? 'Processing...' : 'Accept & Start Project'}
                        </motion.button>
                        <motion.button
                            className="btn btn-danger"
                            disabled={busy}
                            onClick={() => doTransition('cancelled', 'Reject Order', 'Reject this order?')}
                            whileHover={{ scale: 1.03 }}
                            whileTap={{ scale: 0.97 }}
                        >
                            Reject
                        </motion.button>
                    </div>
                </motion.div>
            )}

            {order.status === 'active' && (
                <motion.div
                    className="detail-box"
                    style={{ marginTop: 16 }}
                    variants={fadeUp}
                    custom={4}
                    whileHover={{ boxShadow: '0 8px 32px rgba(99,102,241,0.08)' }}
                    transition={{ duration: 0.25 }}
                >
                    <div className="card-header"><h3>In Progress</h3></div>
                    <p style={{ color: 'var(--text-muted)', marginBottom: 12, fontSize: 14 }}>
                        {projectDelivered
                            ? 'Project is delivered. You can mark this order delivered now.'
                            : 'Mark the linked project as delivered first to unlock the "Mark Delivered" option. Complete is only available after the order is delivered.'}
                    </p>
                    <div className="status-form">
                        {projectDelivered && (
                            <motion.button
                                className="btn btn-primary"
                                disabled={busy}
                                onClick={() => doTransition('delivered', 'Mark Delivered', 'Mark this order as delivered? The buyer will review it.')}
                                whileHover={{ scale: 1.03 }}
                                whileTap={{ scale: 0.97 }}
                            >
                                <Send size={16} /> {busy ? 'Processing...' : 'Mark Delivered'}
                            </motion.button>
                        )}
                    </div>
                </motion.div>
            )}

            {(order.invoices || []).length > 0 && (
                <motion.div className="detail-box" style={{ marginTop: 16 }} variants={fadeUp} custom={5}>
                    <div className="card-header"><h3>Invoices</h3></div>
                    <motion.div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }} initial="hidden" animate="visible" variants={stagger}>
                        {order.invoices.map((inv) => (
                            <motion.div
                                key={inv.id}
                                style={{
                                    border: '1px solid var(--border)', borderRadius: 12,
                                    padding: '12px 14px', display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap',
                                    background: 'rgba(255,255,255,0.02)',
                                }}
                                variants={fadeUp}
                                whileHover={{ y: -4, boxShadow: '0 8px 24px rgba(0,0,0,0.12)', transition: { duration: 0.25 } }}
                            >
                                <div>
                                    <div style={{ fontWeight: 700, fontSize: 13 }}>{inv.invoice_number}</div>
                                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>
                                        {inv.type === 'seller' ? 'Seller invoice' : 'Buyer invoice'} · ₹{Number(inv.total).toLocaleString('en-IN')}
                                    </div>
                                </div>
                                {inv.type === 'seller' && (
                                    <div style={{ display: 'flex', gap: 6 }}>
                                        <a className="btn btn-primary btn-sm" href={`/invoice/${inv.invoice_number}`} target="_blank" rel="noreferrer"><Eye size={13} /> View</a>
                                        <a className="btn btn-secondary btn-sm" href={`/invoice/${inv.invoice_number}/download`}><FileDown size={13} /> Download</a>
                                    </div>
                                )}
                            </motion.div>
                        ))}
                    </motion.div>
                </motion.div>
            )}

            <motion.div className="detail-box" style={{ marginTop: 16 }} variants={fadeUp} custom={6}>
                <div className="card-header"><h3>Update Status</h3></div>
                {updateOptions.length === 0 ? (
                    <p style={{ color: 'var(--text-muted)', fontSize: 14 }}>No further status changes are available for this order.</p>
                ) : (
                    <div className="status-form">
                        <select className="form-select" value={newStatus} onChange={(e) => setNewStatus(e.target.value)}>
                            <option value="" disabled>Select next status</option>
                            {updateOptions.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
                        </select>
                        <motion.button
                            className="btn btn-primary btn-sm"
                            onClick={updateStatus}
                            disabled={!newStatus || newStatus === order.status || busy}
                            whileHover={{ scale: 1.03 }}
                            whileTap={{ scale: 0.97 }}
                        >
                            Update
                        </motion.button>
                    </div>
                )}
            </motion.div>
        </motion.div>
    );
}

export default OrderDetail;
