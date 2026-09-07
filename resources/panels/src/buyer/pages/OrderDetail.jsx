import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, MessageSquare, Star, FolderKanban, FileDown, Eye, Info } from 'lucide-react';
import { Link } from 'react-router-dom';
import api from '../../shared/api';

const fallbackOrder = {
    id: 1001, seller: { full_name: 'Sara Ali' }, service: { title: 'Logo Design' },
    amount: 50, status: 'active', delivery_date: '2026-09-05', created_at: '2026-08-20',
    review: null, project: null,
};

const statusBadgeMap = { pending: 'badge-warning', active: 'badge-primary', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };

const statusLabels = {
    pending: 'Accept Pending',
    active: 'In Progress',
    delivered: 'Delivered',
    completed: 'Completed',
    cancelled: 'Cancelled',
};

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.08 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const slideRight = {
    hidden: { opacity: 0, x: -20 },
    show: { opacity: 1, x: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

function OrderDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(fallbackOrder);

    useEffect(() => {
        api.get(`/buyer/orders/${id}`).then(({ data }) => setOrder(data)).catch(() => {});
    }, [id]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/orders')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Orders
                    </button>
                    <h1>Order #{order.id}</h1>
                </div>
                <span className={`badge ${statusBadgeMap[order.status] || 'badge-info'}`}>{statusLabels[order.status] || order.status}</span>
            </motion.div>

            <motion.div className="detail-grid" variants={container} initial="hidden" animate="show">
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3><Info size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Order Information</h3>
                    <div className="detail-row"><span className="label">Service</span><span className="value">{order.service?.title || '-'}</span></div>
                    <div className="detail-row"><span className="label">Seller</span><span className="value">{order.seller?.full_name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Amount</span><span className="value">₹{Number(order.amount).toLocaleString('en-IN')}</span></div>
                    <div className="detail-row"><span className="label">Placed</span><span className="value">{order.created_at ? new Date(order.created_at).toLocaleDateString() : '-'}</span></div>
                    <div className="detail-row"><span className="label">Delivery</span><span className="value">{order.delivery_date ? new Date(order.delivery_date).toLocaleDateString() : '-'}</span></div>

                    {(order.requirements_title || order.requirements_type || order.requirements || order.requirements_docs) && (
                        <>
                            <h3 style={{ marginTop: 18 }}>My Requirements</h3>
                            {order.requirements_title ? (
                                <div className="detail-row"><span className="label">Title</span><span className="value">{order.requirements_title}</span></div>
                            ) : null}
                            {order.requirements_type ? (
                                <div className="detail-row"><span className="label">Type</span><span className="value">{order.requirements_type}</span></div>
                            ) : null}
                            {order.requirements ? (
                                <div className="detail-row"><span className="label">Description</span><span className="value" style={{ whiteSpace: 'pre-wrap' }}>{order.requirements}</span></div>
                            ) : null}
                            {order.requirements_docs ? (
                                <div className="detail-row">
                                    <span className="label">Document</span>
                                    <span className="value">
                                        <a href={`/storage/${order.requirements_docs}`} target="_blank" rel="noreferrer" style={{ color: 'var(--accent)', fontWeight: 600 }}><Eye size={14} style={{ verticalAlign: 'middle', marginRight: 6 }} />View attached file</a>
                                    </span>
                                </div>
                            ) : null}
                        </>
                    )}
                </motion.div>
                {order.status === 'cancelled' && order.decline_reason && (
                    <motion.div className="detail-box" variants={fadeUp} style={{ marginTop: 16, borderColor: 'rgba(239,68,68,0.3)' }}>
                        <h3 style={{ color: 'var(--danger,#EF4444)' }}><Info size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Order Declined</h3>
                        <div className="detail-row">
                            <span className="label">Reason</span>
                            <span className="value">{order.decline_reason}</span>
                        </div>
                        <p style={{ color: 'var(--text-muted)', fontSize: 13, marginTop: 10 }}>
                            The seller could not accept your order. You may contact support or place a new order for a different service.
                        </p>
                    </motion.div>
                )}
                <motion.div className="detail-box" variants={fadeUp}>
                    <h3>Actions</h3>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                        {order.project && (
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <Link to={`/projects/${order.project.id}`} className="btn btn-secondary" style={{ display: 'flex', width: '100%', justifyContent: 'center' }}><FolderKanban size={16} /> View Project</Link>
                            </motion.div>
                        )}
                        {order.status === 'delivered' && !order.review && (
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <Link to={`/reviews/create/${order.id}`} className="btn btn-primary" style={{ display: 'flex', width: '100%', justifyContent: 'center' }}><Star size={16} /> Leave a Review</Link>
                            </motion.div>
                        )}
                        {order.project && (
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <Link to={`/projects/${order.project.id}`} className="btn btn-secondary" style={{ display: 'flex', width: '100%', justifyContent: 'center' }}><MessageSquare size={16} /> Chat with Seller</Link>
                            </motion.div>
                        )}
                    </div>
                    {order.review && (
                        <div className="alert alert-success" style={{ marginTop: 16 }}>You have already left a review.</div>
                    )}
                </motion.div>
            </motion.div>

            {(order.invoices || []).length > 0 && (
                <motion.div className="detail-box" style={{ marginTop: 16 }} variants={container} initial="hidden" animate="show">
                    <div className="card-header"><h3>Invoices</h3></div>
                    <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap' }}>
                        {order.invoices.map((inv, i) => (
                            <motion.div key={inv.id} custom={i} variants={slideRight} initial="hidden" animate="show" style={{
                                border: '1px solid var(--border)', borderRadius: 12,
                                padding: '12px 14px', display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap',
                            }}>
                                <div>
                                    <div style={{ fontWeight: 700, fontSize: 13 }}>{inv.invoice_number}</div>
                                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>
                                        {inv.type === 'buyer' ? 'Buyer invoice' : 'Seller invoice'} · ₹{Number(inv.total).toLocaleString('en-IN')}
                                    </div>
                                </div>
                                {inv.type === 'buyer' && (
                                    <div style={{ display: 'flex', gap: 6 }}>
                                        <a className="btn btn-primary btn-sm" href={`/invoice/${inv.invoice_number}`} target="_blank" rel="noreferrer"><Eye size={13} /> View</a>
                                        <a className="btn btn-secondary btn-sm" href={`/invoice/${inv.invoice_number}/download`}><FileDown size={13} /> Download</a>
                                    </div>
                                )}
                            </motion.div>
                        ))}
                    </div>
                </motion.div>
            )}
        </motion.div>
    );
}

export default OrderDetail;
