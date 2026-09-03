import { useState, useEffect } from 'react';
import { BadgeCheck, Banknote, Clock, Wallet } from 'lucide-react';
import api from '../../shared/api';
import { fmtMoney, fmtTime } from '../helpers';
import Pagination from '../components/Pagination';
import Modal from '../components/Modal';
import toastr from '../../shared/toastr';

function Settlements() {
    const [status, setStatus] = useState('pending');
    const [data, setData] = useState([]);
    const [meta, setMeta] = useState({});
    const [stats, setStats] = useState({});
    const [page, setPage] = useState(1);

    const [payTarget, setPayTarget] = useState(null);
    const [note, setNote] = useState('');
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        api.get('/admin/settlements', { params: { status, page, per_page: 15 } })
            .then((res) => {
                setData(res.data.data || res.data || []);
                setMeta(res.data);
            })
            .catch(() => {});
    }, [status, page]);

    useEffect(() => {
        api.get('/admin/settlements/stats').then((res) => setStats(res.data)).catch(() => {});
    }, [data]);

    const pay = async (e) => {
        e.preventDefault();
        setBusy(true);
        try {
            const res = await api.post(`/admin/settlements/${payTarget.id}/pay`, { admin_note: note || undefined });
            toastr.success(res.data.message);
            setPayTarget(null);
            setNote('');
            setData((prev) => prev.filter((s) => s.id !== payTarget.id));
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to process payment');
        } finally {
            setBusy(false);
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Settlements</h1><p>Payouts to sellers after fee deduction (escrow)</p></div>
            </div>

            <div className="stat-grid" style={{ marginBottom: 16 }}>
                <div className="stat-card">
                    <div className="stat-icon blue"><Clock size={16} /></div>
                    <div><div className="stat-value">{fmtMoney(stats.pending_amount || 0)}</div><div className="stat-label">Pending Payout</div></div>
                </div>
                <div className="stat-card">
                    <div className="stat-icon green"><BadgeCheck size={16} /></div>
                    <div><div className="stat-value">{fmtMoney(stats.paid_amount || 0)}</div><div className="stat-label">Total Paid</div></div>
                </div>
                <div className="stat-card">
                    <div className="stat-icon orange"><Wallet size={16} /></div>
                    <div><div className="stat-value">{fmtMoney(stats.total_platform_fee || 0)}</div><div className="stat-label">Platform Fee Earned</div></div>
                </div>
            </div>

            <div className="toolbar" style={{ marginBottom: 12 }}>
                <div className="filters">
                    <select className="form-control" value={status} onChange={(e) => { setStatus(e.target.value); setPage(1); }}>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? (
                        <div className="empty">No settlements found</div>
                    ) : (
                        <table>
                            <thead>
                                <tr>
                                    <th>Order No.</th><th>Invoice</th><th>Seller</th>
                                    <th>Order Amt</th><th>Fee</th><th>Payout</th><th>Date</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.map((s) => (
                                    <tr key={s.id}>
                                        <td style={{ fontWeight: 600 }}>{s.order_number || `#${s.order_id}`}</td>
                                        <td>{s.invoice_number || '-'}</td>
                                        <td>{s.seller?.full_name || s.seller?.name || '-'}</td>
                                        <td>{fmtMoney(s.order_amount)}</td>
                                        <td style={{ color: 'var(--danger)' }}>−{fmtMoney(s.platform_fee)}</td>
                                        <td style={{ fontWeight: 600, color: 'var(--success)' }}>{fmtMoney(s.seller_amount)}</td>
                                        <td>{fmtTime(status === 'paid' ? s.paid_at : s.created_at)}</td>
                                        <td>
                                            {s.status === 'pending' ? (
                                                <button className="btn btn-primary btn-sm" onClick={() => { setPayTarget(s); setNote(''); }}>
                                                    <Banknote size={13} /> Pay
                                                </button>
                                            ) : (
                                                <span className="badge badge-success">Paid · {s.transaction_id}</span>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            <Modal open={!!payTarget} title={`Pay Settlement · ${payTarget?.order_number}`} onClose={() => setPayTarget(null)} width="480px">
                {payTarget && (
                    <form onSubmit={pay}>
                        <div style={{ background: 'var(--accent-light, #eef2ff)', borderRadius: 10, padding: 14, marginBottom: 14 }}>
                            <div className="row"><span>Order Amount</span><strong>{fmtMoney(payTarget.order_amount)}</strong></div>
                            <div className="row"><span>Platform Fee ({payTarget.platform_pct}%)</span><strong style={{ color: 'var(--danger)' }}>−{fmtMoney(payTarget.platform_fee)}</strong></div>
                            <div className="row" style={{ borderTop: '1px dashed var(--border-color)', marginTop: 8, paddingTop: 8 }}>
                                <span>Seller Payout</span><strong style={{ color: 'var(--success)' }}>{fmtMoney(payTarget.seller_amount)}</strong>
                            </div>
                        </div>
                        <div className="form-group">
                            <label>Admin Note (optional)</label>
                            <textarea className="form-control" rows={3} value={note} onChange={(e) => setNote(e.target.value)} placeholder="Payment notes…" />
                        </div>
                        <p style={{ fontSize: 12, color: 'var(--text-muted)', marginBottom: 8 }}>
                            Confirming pays ₹{payTarget.seller_amount.toLocaleString('en-IN', { maximumFractionDigits: 2 })} to the seller&apos;s wallet and credits the platform fee to your admin wallet.
                        </p>
                        <div className="modal-actions">
                            <button type="button" className="btn btn-sm" onClick={() => setPayTarget(null)}>Cancel</button>
                            <button type="submit" className="btn btn-primary btn-sm" disabled={busy}>{busy ? 'Processing…' : 'Confirm Payment'}</button>
                        </div>
                        <style>{`
                            .row { display:flex; justify-content:space-between; align-items:center; font-size:13px; }
                            .row span { color: var(--text-muted); }
                        `}</style>
                    </form>
                )}
            </Modal>
        </div>
    );
}

export default Settlements;