import { useState, useEffect } from 'react';
import { Search, Eye, Settings2 } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import api from '../../shared/api';
import { orderStatusBadge, fmtMoney, fmtTime } from '../helpers';
import Pagination from '../components/Pagination';
import Modal from '../components/Modal';
import toastr from '../../shared/toastr';

const PLACEHOLDER_HELP = ['{prefix}', '{suffix}', '{year}', '{yy}', '{month}', '{seq}'];

function exampleNumber(pattern, prefix, suffix, padding) {
    const vars = {
        prefix, suffix,
        year: new Date().getFullYear(),
        yy: String(new Date().getFullYear()).slice(-2),
        month: String(new Date().getMonth() + 1).padStart(2, '0'),
        seq: String(1).padStart(Number(padding) || 4, '0'),
    };
    let out = pattern || '';
    Object.entries(vars).forEach(([k, v]) => { out = out.split(`{${k}}`).join(String(v)); });
    return out;
}

function composePattern({ prefix, suffix }) {
    let base = prefix ? '{prefix}-{year}-{seq}' : '{year}-{seq}';
    return suffix ? `${base}-{suffix}` : base;
}

function Orders() {
    const navigate = useNavigate();
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');
    const [query, setQuery] = useState('');
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});

    const [numOpen, setNumOpen] = useState(false);
    const [numForm, setNumForm] = useState({ pattern: 'ORD-{year}-{seq}', prefix: 'ORD', suffix: '', seq_padding: 4 });
    const [numBusy, setNumBusy] = useState(false);

    useEffect(() => {
        const t = setTimeout(() => {
            api.get('/admin/orders', { params: { status, search: query || undefined, page, per_page: 10 } })
                .then((res) => {
                    setData(res.data.data || res.data || []);
                    setMeta(res.data);
                })
                .catch(() => {});
        }, 300);
        return () => clearTimeout(t);
    }, [status, page, query]);

    const openNumbering = () => {
        api.get('/admin/invoice-settings/orders').then(({ data }) => setNumForm(data)).catch(() => {});
        setNumOpen(true);
    };

    const saveNumbering = async (e) => {
        e.preventDefault();
        setNumBusy(true);
        try {
            await api.put('/admin/invoice-settings/orders', { ...numForm, pattern: composePattern(numForm) });
            toastr.success('Order numbering saved — new orders will use this pattern');
            setNumOpen(false);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save numbering');
        } finally {
            setNumBusy(false);
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Orders</h1><p>Manage all platform orders</p></div>
                <button className="btn btn-primary btn-sm" onClick={openNumbering}><Settings2 size={14} /> Order Numbering</button>
            </div>

            <div className="toolbar">
                <div className="filters">
                    <select className="form-control" value={status} onChange={(e) => { setStatus(e.target.value); setPage(1); }}>
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="delivered">Delivered</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div className="search-box">
                    <Search size={14} style={{ color: 'var(--text-muted)' }} />
                    <input className="form-control" placeholder="Search…" value={query} onChange={(e) => { setQuery(e.target.value); setPage(1); }} />
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? (
                        <div className="empty">No orders found</div>
                    ) : (
                        <table>
                            <thead>
                                <tr><th>Order No.</th><th>Buyer</th><th>Seller</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr>
                            </thead>
                            <tbody>
                                {data.map((o) => (
                                    <tr key={o.id}>
                                        <td style={{ fontWeight: 600 }}>{o.order_number || `#${o.id}`}</td>
                                        <td>{o.buyer?.full_name || o.buyer?.name || '-'}</td>
                                        <td>{o.seller?.full_name || o.seller?.name || '-'}</td>
                                        <td>{fmtMoney(o.amount)}</td>
                                        <td><span className={`badge ${orderStatusBadge[o.status] || 'badge-info'}`}>{o.status}</span></td>
                                        <td>{fmtTime(o.created_at)}</td>
                                        <td>
                                            <button className="btn btn-sm" onClick={() => navigate(`/orders/${o.id}`)}>
                                                <Eye size={13} /> View
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            <Modal open={numOpen} title="Order Numbering Pattern" onClose={() => setNumOpen(false)} width="520px">
                <form onSubmit={saveNumbering}>
                    <p style={{ fontSize: 13, color: 'var(--text-muted)', marginBottom: 12 }}>
                        Every new order gets an auto-generated number using this pattern. Placeholders:
                    </p>
                    <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginBottom: 14 }}>
                        {PLACEHOLDER_HELP.map((t) => <code key={t} style={{ fontSize: 11, background: 'var(--bg-muted)', padding: '3px 7px', borderRadius: 5 }}>{t}</code>)}
                    </div>
                    <div className="form-group">
                        <label>Number Pattern</label>
                        <input className="form-control" value={composePattern(numForm)} readOnly
                            title="Auto-built from prefix/suffix"
                            style={{ background: 'var(--bg-muted, #f1f5f9)', color: 'var(--text-muted)' }} />
                    </div>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Prefix</label>
                            <input className="form-control" value={numForm.prefix} onChange={(e) => setNumForm({ ...numForm, prefix: e.target.value })} placeholder="ORD" />
                        </div>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Suffix</label>
                            <input className="form-control" value={numForm.suffix} onChange={(e) => setNumForm({ ...numForm, suffix: e.target.value })} />
                        </div>
                        <div className="form-group" style={{ flex: 0.7 }}>
                            <label>Seq Padding</label>
                            <input type="number" min="1" max="12" className="form-control" value={numForm.seq_padding} onChange={(e) => setNumForm({ ...numForm, seq_padding: Number(e.target.value) || 4 })} />
                        </div>
                    </div>
                    <div style={{ fontSize: 12, color: 'var(--text-muted)', marginBottom: 14 }}>
                        <strong>Next number example:</strong>{' '}
                        <code>{exampleNumber(composePattern(numForm), numForm.prefix || 'PREFIX', numForm.suffix, numForm.seq_padding)}</code>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn btn-sm" onClick={() => setNumOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={numBusy}>{numBusy ? 'Saving…' : 'Save Pattern'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default Orders;