import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, CreditCard, Loader2, Wallet, ShieldCheck, CheckCircle2, RotateCcw } from 'lucide-react';
import api from '../../shared/api';
import toastr from '../../shared/toastr';

const paymentBadgeMap = { pending: 'badge-warning', paid: 'badge-success', failed: 'badge-danger' };
const paymentLabels = { pending: 'Payment Pending', paid: 'Paid', failed: 'Payment Failed' };

function Checkout() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(null);
    const [loading, setLoading] = useState(true);
    const [paying, setPaying] = useState(false);

    useEffect(() => {
        api.get(`/buyer/orders/${id}`).then(({ data }) => setOrder(data)).catch(() => toastr.danger('Could not load order')).finally(() => setLoading(false));
    }, [id]);

    const pay = async () => {
        setPaying(true);
        try {
            const { data } = await api.post(`/buyer/orders/${id}/pay`);
            if (data.method === 'GET') {
                window.location.href = data.url;
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = data.url;
            for (const [key, value] of Object.entries(data.fields || {})) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Could not start payment. Please try again.');
        } finally {
            setPaying(false);
        }
    };

    if (loading) {
        return (
            <div style={{ display: 'flex', justifyContent: 'center', padding: '80px 0' }}>
                <Loader2 size={26} className="spin" style={{ color: 'var(--accent)' }} />
            </div>
        );
    }

    if (!order) return null;

    const paidStatus = order.payment_status || (order.paid_at ? 'paid' : 'pending');
    const showGateBtn = paidStatus === 'pending' || paidStatus === 'failed';

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.45, ease: [0.22, 1, 0.36, 1] }}>
                <div>
                    <button className="btn btn-secondary btn-sm" onClick={() => navigate('/orders')} style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Orders
                    </button>
                    <h1>Checkout · Order #{order.id}</h1>
                </div>
                <span className={`badge ${paymentBadgeMap[paidStatus] || 'badge-warning'}`}>
                    {paidStatus === 'paid' ? <CheckCircle2 size={13} style={{ verticalAlign: 'middle', marginRight: 4 }} /> : null}
                    {paymentLabels[paidStatus] || paidStatus}
                </span>
            </motion.div>

            <div className="detail-grid">
                <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}>
                    <h3><CreditCard size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Order Summary</h3>
                    <div className="detail-row"><span className="label">Service</span><span className="value">{order.service?.title || '-'}</span></div>
                    <div className="detail-row"><span className="label">Seller</span><span className="value">{order.seller?.full_name || '-'}</span></div>
                    <div className="detail-row"><span className="label">Placed</span><span className="value">{order.created_at ? new Date(order.created_at).toLocaleDateString() : '-'}</span></div>
                    <div style={{ borderTop: '1px dashed var(--border)', marginTop: 14, paddingTop: 14, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <strong style={{ fontSize: 15 }}>Total Payable</strong>
                        <strong style={{ fontSize: 22, color: 'var(--accent,#4F46E5)' }}>₹{Number(order.amount).toLocaleString('en-IN')}</strong>
                    </div>

                    {paidStatus === 'paid' && (
                        <div className="alert alert-success" style={{ marginTop: 16 }}>
                            <CheckCircle2 size={15} style={{ verticalAlign: 'middle', marginRight: 6 }} />
                            Payment received{order.paid_at ? ` on ${new Date(order.paid_at).toLocaleString()}` : ''}.
                            {order.transaction_id ? ` Ref: ${order.transaction_id}` : ''}
                        </div>
                    )}
                    {paidStatus === 'failed' && (
                        <div className="alert alert-danger" style={{ marginTop: 16 }}>Your payment attempt failed. Please try again.</div>
                    )}
                </motion.div>

                <motion.div className="detail-box" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.45, ease: [0.22, 1, 0.36, 1] }}>
                    <h3><Wallet size={16} style={{ marginRight: 6, verticalAlign: 'middle' }} />Payment</h3>
                    {showGateBtn ? (
                        <>
                            <p style={{ color: 'var(--text-muted)', fontSize: 13, lineHeight: 1.6, marginBottom: 14 }}>
                                Pay securely to confirm this order. Once the seller accepts and completes the work, this amount is released to them.
                            </p>
                            <motion.button whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.97 }} className="btn btn-primary" style={{ width: '100%', display: 'inline-flex', justifyContent: 'center', alignItems: 'center', gap: 8 }} onClick={pay} disabled={paying}>
                                {paying ? <Loader2 size={16} className="spin" /> : <CreditCard size={16} />}
                                {paying ? 'Redirecting to payment…' : paidStatus === 'failed' ? <span><RotateCcw size={15} style={{ marginRight: 6 }} />Retry Payment</span> : `Pay ₹${Number(order.amount).toLocaleString('en-IN')}`}
                            </motion.button>
                        </>
                    ) : (
                        <div style={{ display: 'flex', alignItems: 'center', gap: 10, color: 'var(--success,#16A34A)', fontWeight: 600 }}>
                            <ShieldCheck size={20} /> This order is paid. No action needed.
                        </div>
                    )}
                    <div className="detail-row" style={{ marginTop: 16 }}>
                        <span className="label">Payment Method</span>
                        <span className="value">{order.payment_method ? order.payment_method.toUpperCase() : 'Not chosen yet'}</span>
                    </div>
                    <div className="detail-row">
                        <span className="label">Transaction</span>
                        <span className="value">{order.transaction_id || '-'}</span>
                    </div>
                </motion.div>
            </div>
        </motion.div>
    );
}

export default Checkout;