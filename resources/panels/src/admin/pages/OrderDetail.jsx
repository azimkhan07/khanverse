import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { orderStatusBadge, fmtMoney, fmtTime } from '../helpers';

const statusOptions = ['pending', 'active', 'delivered', 'completed', 'cancelled', 'disputed'];

function OrderDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [order, setOrder] = useState(null);
    const [newStatus, setNewStatus] = useState('');
    const [message, setMessage] = useState(null);
    const [alertType, setAlertType] = useState('success');

    useEffect(() => {
        api.get(`/admin/orders/${id}`).then((res) => {
            setOrder(res.data);
            setNewStatus(res.data.status || '');
        }).catch(() => {});
    }, [id]);

    if (!order) return <div className="empty">Loading…</div>;

    const updateStatus = async () => {
        if (!newStatus || newStatus === order.status) return;
        try {
            await api.post(`/admin/orders/${id}/status`, { status: newStatus });
            setOrder((prev) => ({ ...prev, status: newStatus }));
            setAlertType('success');
            setMessage('Order status updated.');
            toastr.success(`Order status changed to ${newStatus}`);
        } catch (err) {
            setAlertType('error');
            setMessage(err.response?.data?.message || 'Update failed.');
            toastr.danger(err.response?.data?.message || 'Failed to update order status');
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div>
                    <button className="btn btn-sm" onClick={() => navigate('/orders')} style={{ marginBottom: 10 }}>
                        <ArrowLeft size={13} /> Back
                    </button>
                    <h1>{order.order_number || `Order #${order.id}`}</h1>
                    <p>Placed {fmtTime(order.created_at)}</p>
                </div>
                <span className={`badge ${orderStatusBadge[order.status] || 'badge-info'}`}>{order.status}</span>
            </div>

            {message && (
                <div style={{ padding: '8px 12px', borderRadius: 6, marginBottom: 12,
                    background: alertType === 'success' ? '#dcfce7' : '#fee2e2',
                    color: alertType === 'success' ? '#15803d' : '#b91c1c', fontSize: 12 }}>
                    {message}
                </div>
            )}

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Buyer</h3></div>
                    <div className="card-body">
                        <Row label="Name" value={order.buyer?.full_name || order.buyer?.name || '-'} />
                        <Row label="Email" value={order.buyer?.email || '-'} />
                    </div>
                </div>
                <div className="admin-card">
                    <div className="card-head"><h3>Seller</h3></div>
                    <div className="card-body">
                        <Row label="Name" value={order.seller?.full_name || order.seller?.name || '-'} />
                        <Row label="Email" value={order.seller?.email || '-'} />
                    </div>
                </div>
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Service</h3></div>
                    <div className="card-body">
                        <Row label="Title" value={order.service?.title || '-'} />
                        <Row label="Category" value={order.service?.category?.name || '-'} />
                    </div>
                </div>
                <div className="admin-card">
                    <div className="card-head"><h3>Payment</h3></div>
                    <div className="card-body">
                        <Row label="Amount" value={fmtMoney(order.amount)} />
                        <Row label="Order No." value={order.order_number || '-'} />
                        <Row label="Project" value={order.project ? `#${order.project.id}` : '-'} />
                    </div>
                </div>
            </div>

            {order.invoices && order.invoices.length > 0 && (
                <div className="admin-card">
                    <div className="card-head"><h3>Invoices</h3></div>
                    <div className="card-body">
                        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
                            {order.invoices.map((inv) => (
                                <div key={inv.id} style={{
                                    border: '1px solid var(--border)', borderRadius: 10, padding: '10px 14px',
                                    display: 'flex', alignItems: 'center', gap: 12,
                                }}>
                                    <div>
                                        <div style={{ fontWeight: 600, fontSize: 13 }}>{inv.invoice_number}</div>
                                        <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>
                                            {inv.type === 'seller' ? 'Seller' : 'Buyer'} · {fmtMoney(inv.total)}
                                        </div>
                                    </div>
                                    <a
                                        href={`/invoice/${inv.invoice_number}`}
                                        target="_blank" rel="noreferrer"
                                        className="btn btn-primary btn-sm"
                                    >View</a>
                                    <a
                                        href={`/invoice/${inv.invoice_number}/download`}
                                        className="btn btn-secondary btn-sm"
                                    >Download</a>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}

            <div className="admin-card">
                <div className="card-head"><h3>Update Status</h3></div>
                <div className="card-body">
                    <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
                        <select className="form-control" value={newStatus} onChange={(e) => setNewStatus(e.target.value)} style={{ minWidth: 160 }}>
                            {statusOptions.map((s) => <option key={s} value={s}>{s}</option>)}
                        </select>
                        <button className="btn btn-primary btn-sm" onClick={updateStatus} disabled={!newStatus || newStatus === order.status}>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

function Row({ label, value }) {
    return (
        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', fontSize: 12.5 }}>
            <span style={{ color: 'var(--text-muted)' }}>{label}</span>
            <span style={{ fontWeight: 500 }}>{value}</span>
        </div>
    );
}

export default OrderDetail;
