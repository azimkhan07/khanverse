import { useState, useEffect, useCallback } from 'react';
import { Plus, Edit2, Trash2, Eye, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

const emptyForm = {
    type: 'buyer',
    name: '',
    company_name: '',
    logo_text: '',
    logo_url: '',
    footer_line: '',
    about_line: '',
    body: '',
    is_active: true,
};

function InvoiceDesigns() {
    const [rows, setRows] = useState([]);
    const [meta, setMeta] = useState({});
    const [page, setPage] = useState(1);
    const [typeFilter, setTypeFilter] = useState('');
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState(emptyForm);
    const [preview, setPreview] = useState(null);

    const load = useCallback(() => {
        api.get('/admin/invoice-designs', { params: { page, per_page: 10, type: typeFilter || undefined } })
            .then((res) => {
                const d = res.data;
                setRows(d.data || d || []);
                setMeta(d);
            })
            .catch(() => {});
    }, [page, typeFilter]);

    useEffect(() => { load(); }, [load]);

    const openCreate = () => { setEditing(null); setForm({ ...emptyForm }); setModalOpen(true); };
    const openEdit = (d) => { setEditing(d); setForm({ ...d }); setModalOpen(true); };

    const submit = async (e) => {
        e.preventDefault();
        try {
            if (editing) {
                await api.put(`/admin/invoice-designs/${editing.id}`, form);
                toastr.success('Invoice design updated');
            } else {
                await api.post('/admin/invoice-designs', form);
                toastr.success('Invoice design created');
            }
            setModalOpen(false); load();
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save design');
        }
    };

    const toggle = async (d) => {
        const target = !d.is_active;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set design "${d.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/invoice-designs/${d.id}/status`);
            if (target) toastr.success('Design activated');
            else toastr.warning('Design deactivated');
            load();
        } catch {
            toastr.danger('Failed to update status');
        }
    };

    const remove = async (d) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete design "${d.name}"?`, type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/admin/invoice-designs/${d.id}`);
            toastr.success('Invoice design deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete design');
        }
        load();
    };

    const showPreview = async (d) => {
        try {
            const { data } = await api.get(`/admin/invoice-designs/${d.id}/preview`);
            if (!data.html) {
                toastr.warning(data.message || 'No order available for preview');
                return;
            }
            setPreview({ html: data.html, order: data.order_number });
        } catch {
            toastr.danger('Failed to generate preview');
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Invoice Designs</h1><p>Design templates for buyer &amp; seller invoices (HTML)</p></div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Design</button>
            </div>

            <div className="toolbar">
                <div className="filters">
                    <select className="form-control" value={typeFilter} onChange={(e) => { setTypeFilter(e.target.value); setPage(1); }}>
                        <option value="">All Types</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                    </select>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead>
                            <tr><th>Name</th><th>Type</th><th>Company</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No invoice designs found</td></tr>
                            ) : (
                                rows.map((d) => (
                                    <tr key={d.id}>
                                        <td style={{ fontWeight: 500 }}>{d.name}</td>
                                        <td><span className={`badge ${d.type === 'seller' ? 'badge-info' : 'badge-primary'}`}>{d.type}</span></td>
                                        <td>{d.company_name || '-'}</td>
                                        <td>
                                            <span className={`badge ${d.is_active ? 'badge-success' : 'badge-danger'}`}>{d.is_active ? 'Active' : 'Inactive'}</span>
                                        </td>
                                        <td>{d.updated_at ? new Date(d.updated_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }) : '-'}</td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} title="Preview" onClick={() => showPreview(d)}><Eye size={13} /></button>
                                                <button className={`btn-icon ${d.is_active ? 'btn-icon-success' : 'btn-icon-danger'}`} title={d.is_active ? 'Deactivate' : 'Activate'} onClick={() => toggle(d)}><Power size={13} /></button>
                                                <button className="btn-icon" style={{ color: '#7c3aed' }} title="Edit" onClick={() => openEdit(d)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" title="Delete" onClick={() => remove(d)}><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            <Modal open={modalOpen} title={editing ? 'Edit Invoice Design' : 'Add Invoice Design'} onClose={() => setModalOpen(false)} width="680px">
                <form className="modal-content" onSubmit={submit}>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Type</label>
                            <select className="form-control" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
                                <option value="buyer">Buyer</option>
                                <option value="seller">Seller</option>
                            </select>
                        </div>
                        <div className="form-group" style={{ flex: 2 }}>
                            <label>Design Name</label>
                            <input className="form-control" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                        </div>
                    </div>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Company Name</label>
                            <input className="form-control" value={form.company_name} onChange={(e) => setForm({ ...form, company_name: e.target.value })} />
                        </div>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Logo Text</label>
                            <input className="form-control" value={form.logo_text} onChange={(e) => setForm({ ...form, logo_text: e.target.value })} />
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Logo URL</label>
                        <input className="form-control" value={form.logo_url} onChange={(e) => setForm({ ...form, logo_url: e.target.value })} placeholder="https://…" />
                    </div>
                    <div style={{ display: 'flex', gap: 12 }}>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>Footer Line</label>
                            <input className="form-control" value={form.footer_line} onChange={(e) => setForm({ ...form, footer_line: e.target.value })} />
                        </div>
                        <div className="form-group" style={{ flex: 1 }}>
                            <label>About Line</label>
                            <input className="form-control" value={form.about_line} onChange={(e) => setForm({ ...form, about_line: e.target.value })} />
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Body (HTML design)</label>
                        <textarea
                            className="form-control"
                            rows={9}
                            value={form.body}
                            onChange={(e) => setForm({ ...form, body: e.target.value })}
                            placeholder={'Invoice body HTML. Available placeholders:\n{{buyer_name}} {{buyer_email}} {{seller_name}} {{seller_email}}\n{{invoice_number}} {{serial_number}} {{order_number}} {{service_title}} {{project_title}}\n{{amount}} {{fee}} {{fee_pct}} {{total}} {{currency}}'}
                        />
                        <div style={{ fontSize: 11, color: 'var(--text-muted)', marginTop: 6 }}>
                            Use <code>{'{{placeholders}}'}</code> for live values. The barcode + serial appear automatically on the <strong>top-right</strong> of the invoice.
                        </div>
                    </div>
                    <div className="form-group">
                        <label><input type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active (used for new invoices)</label>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn btn-sm" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm">{editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>

            <Modal open={!!preview} title={`Design Preview`} onClose={() => setPreview(null)} width="860px">
                {preview && (
                    <div>
                        {preview.order && <div style={{ marginBottom: 10, fontSize: 12, color: 'var(--text-muted)' }}>Showing preview using order <strong>{preview.order}</strong>.</div>}
                        <div style={{ border: '1px solid var(--border)', borderRadius: 8, overflow: 'hidden', background: '#fff' }}>
                            <iframe title="invoice-preview" srcDoc={preview.html} style={{ width: '100%', height: 560, border: 0 }} />
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default InvoiceDesigns;