import { useState, useEffect, useCallback } from 'react';
import { Plus, Edit2, Trash2, Power, Star, Eye, EyeOff } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

const emptyForm = {
    name: '',
    slug: '',
    merchant_id: '',
    access_code: '',
    working_key: '',
    endpoint: '',
    is_default: false,
    is_active: true,
};

function PaymentGateways() {
    const [rows, setRows] = useState([]);
    const [meta, setMeta] = useState({});
    const [page, setPage] = useState(1);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState(emptyForm);
    const [keyTouched, setKeyTouched] = useState(false);
    const [showKey, setShowKey] = useState(false);
    const [saving, setSaving] = useState(false);

    const load = useCallback(() => {
        api.get('/admin/payment-gateways', { params: { page, per_page: 10 } })
            .then((res) => {
                const d = res.data;
                setRows(d.data || d || []);
                setMeta(d);
            })
            .catch(() => {});
    }, [page]);

    useEffect(() => { load(); }, [load]);

    const slug = (name) => String(name || '')
        .toLowerCase()
        .replace(/[^a-z0-9_]+/g, '_')
        .replace(/^_+|_+$/g, '');

    const openCreate = () => {
        setEditing(null);
        setForm({ ...emptyForm });
        setKeyTouched(false);
        setShowKey(false);
        setModalOpen(true);
    };

    const openEdit = (d) => {
        setEditing(d);
        setForm({ ...d });
        setKeyTouched(true);
        setShowKey(false);
        setModalOpen(true);
    };

    const onName = (name) => {
        setForm((f) => ({
            ...f,
            name,
            slug: (!editing && !keyTouched) ? slug(name) : f.slug,
        }));
    };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing) {
                await api.put(`/admin/payment-gateways/${editing.id}`, form);
                toastr.success('Payment gateway updated');
            } else {
                await api.post('/admin/payment-gateways', form);
                toastr.success('Payment gateway added');
            }
            setModalOpen(false);
            load();
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save gateway');
        } finally {
            setSaving(false);
        }
    };

    const toggle = async (d) => {
        const target = !d.is_active;
        const ok = await showConfirm({
            title: 'Please confirm',
            message: target
                ? `Set gateway "${d.name}" to active? All other gateways will be automatically deactivated.`
                : `Deactivate gateway "${d.name}"?`,
            type: 'warning',
            confirmText: target ? 'Activate' : 'Deactivate',
        });
        if (!ok) return;
        try {
            await api.post(`/admin/payment-gateways/${d.id}/status`);
            if (target) toastr.success('Gateway activated. Others were deactivated automatically');
            else toastr.warning('Gateway deactivated');
            load();
        } catch {
            toastr.danger('Failed to update status');
        }
    };

    const makeDefault = async (d) => {
        const ok = await showConfirm({
            title: 'Please confirm',
            message: `Set "${d.name}" as the default gateway? It will be activated and every other gateway will be deactivated automatically.`,
            type: 'warning',
            confirmText: 'Make Default',
        });
        if (!ok) return;
        try {
            await api.post(`/admin/payment-gateways/${d.id}/default`);
            toastr.success(`${d.name} is now the default gateway`);
            load();
        } catch {
            toastr.danger('Failed to set default');
        }
    };

    const remove = async (d) => {
        const ok = await showConfirm({
            title: 'Please confirm',
            message: `Delete gateway "${d.name}"?`,
            type: 'danger',
            confirmText: 'Delete',
        });
        if (!ok) return;
        try {
            await api.delete(`/admin/payment-gateways/${d.id}`);
            toastr.success('Gateway deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete gateway');
        }
        load();
    };

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1>Payment Gateways</h1>
                    <p>Gateway providers — the default active one is used for all payments (no code change on switch)</p>
                </div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Gateway</button>
            </div>

            {rows.length > 0 && (
                <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap', alignItems: 'center', margin: '0 0 12px' }}>
                    <span style={{ fontSize: 11, color: 'var(--text-muted)' }}>
                        Only one gateway stays active at a time — activating one deactivates the rest automatically:
                    </span>
                    {rows.filter((d) => d.is_active).length === 0 ? (
                        <span className="badge badge-danger">No active gateway — add &amp; activate one for payments to work</span>
                    ) : (
                        rows.filter((d) => d.is_active).map((d) => (
                            <span key={d.id} className={`badge ${d.is_default ? 'badge-primary' : 'badge-success'}`}>
                                {d.is_default ? 'Default · ' : ''}{d.name}
                            </span>
                        ))
                    )}
                </div>
            )}

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {rows.length === 0 ? <div className="empty">No payment gateways found — add your first one</div> : (
                        <table>
<thead>
                                    <tr><th>Name</th><th>Slug</th><th>Merchant ID</th><th>Access Code</th><th>Endpoint</th><th>Default</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    {rows.map((d) => (
                                        <tr key={d.id}>
                                            <td style={{ fontWeight: 500 }}>{d.name}</td>
                                            <td><code style={{ fontSize: 11, background: 'var(--bg-muted)', padding: '2px 7px', borderRadius: 6 }}>{d.slug}</code></td>
                                            <td style={{ fontSize: 12 }}>{d.merchant_id || '-'}</td>
                                            <td style={{ fontSize: 12 }}>{d.access_code}</td>
                                            <td style={{ fontSize: 12, color: 'var(--text-muted)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', maxWidth: 160 }} title={d.endpoint}>{d.endpoint}</td>
                                        <td>
                                            {d.is_default ? (
                                                <span className="badge badge-warning"><Star size={10} style={{ marginRight: 3 }} /> Default</span>
                                            ) : (
                                                <span className="badge badge-danger" style={{ background: 'transparent' }}>—</span>
                                            )}
                                        </td>
                                        <td>
                                            <span className={`badge ${d.is_active ? 'badge-success' : 'badge-danger'}`}>{d.is_active ? 'Active' : 'Inactive'}</span>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#f59e0b' }} title="Set default" onClick={() => makeDefault(d)}><Star size={13} /></button>
                                                <button className={`btn-icon ${d.is_active ? 'btn-icon-success' : 'btn-icon-danger'}`} title={d.is_active ? 'Deactivate' : 'Activate'} onClick={() => toggle(d)}><Power size={13} /></button>
                                                <button className="btn-icon" style={{ color: '#7c3aed' }} title="Edit" onClick={() => openEdit(d)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" title="Delete" onClick={() => remove(d)}><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            <Modal open={modalOpen} title={editing ? 'Edit Payment Gateway' : 'Add Payment Gateway'} onClose={() => setModalOpen(false)} width="620px">
                <form className="modal-content" onSubmit={submit}>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1.4 }}>
                            <label>Title</label>
                            <input
                                className="form-control"
                                value={form.name}
                                onChange={(e) => onName(e.target.value)}
                                placeholder="e.g. Razorpay"
                                required
                            />
                        </div>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Slug (unique, snake_case)</label>
                            <input
                                className="form-control"
                                value={form.slug}
                                onChange={(e) => { setKeyTouched(true); setForm({ ...form, slug: e.target.value }); }}
                                placeholder={slug(form.name) || 'razorpay'}
                                required
                            />
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Merchant ID (optional — some gateways need it separately)</label>
                        <input
                            className="form-control"
                            value={form.merchant_id || ''}
                            onChange={(e) => setForm({ ...form, merchant_id: e.target.value })}
                            placeholder="MID12345"
                        />
                    </div>
                    <div className="form-group">
                        <label>Access Code (Key ID / API Key)</label>
                        <input
                            className="form-control"
                            value={form.access_code}
                            onChange={(e) => setForm({ ...form, access_code: e.target.value })}
                            placeholder="rzp_live_XXXX or your key id"
                            required
                        />
                    </div>
                    <div className="form-group">
                        <label>Working Key (Secret)</label>
                        <div className="pwd-wrap">
                            <input
                                type={showKey ? 'text' : 'password'}
                                className="form-control"
                                value={form.working_key}
                                onChange={(e) => setForm({ ...form, working_key: e.target.value })}
                                placeholder="Secret key / working key"
                                style={{ paddingRight: 34 }}
                                required
                            />
                            <button type="button" className="btn-icon" onClick={() => setShowKey(!showKey)} title={showKey ? 'Hide key' : 'Show key'}>{showKey ? <Eye size={13} /> : <EyeOff size={13} />}</button>
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Endpoint (payment page URL)</label>
                        <input
                            className="form-control"
                            value={form.endpoint}
                            onChange={(e) => setForm({ ...form, endpoint: e.target.value })}
                            placeholder="https://pay.example.com/checkout"
                            required
                        />
                    </div>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label><input type="checkbox" checked={!!form.is_default} onChange={(e) => setForm({ ...form, is_default: e.target.checked })} /> Default gateway</label>
                        </div>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label><input type="checkbox" checked={!!form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active</label>
                        </div>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn btn-sm" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={saving}>{saving ? 'Saving…' : editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default PaymentGateways;