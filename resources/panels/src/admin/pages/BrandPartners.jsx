import { useState, useEffect, useRef } from 'react';
import { Plus, Edit2, Trash2, Power, Upload, X } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';
import Modal from '../components/Modal';

function LogoUpload({ value, onChange }) {
    const [uploading, setUploading] = useState(false);
    const inputRef = useRef(null);

    const upload = async (file) => {
        if (!file || !file.type.startsWith('image/')) { toastr.danger('Please select an image file'); return; }
        if (file.size > 5 * 1024 * 1024) { toastr.danger('Max file size is 5MB'); return; }
        setUploading(true);
        try {
            const fd = new FormData();
            fd.append('file', file);
            const res = await api.post('/admin/settings/upload', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            onChange(res.data.path);
            toastr.success('Logo uploaded');
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Upload failed');
        } finally {
            setUploading(false);
        }
    };

    const preview = value ? (value.startsWith('http') ? value : `/storage/${value}`) : null;

    return (
        <div style={{ display: 'flex', gap: 12, alignItems: 'flex-start' }}>
            <div
                onClick={() => inputRef.current?.click()}
                style={{
                    width: 110, height: 60, borderRadius: 10, border: '2px dashed #d1d5db',
                    display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer',
                    background: '#f8fafc', overflow: 'hidden', padding: 6, flexShrink: 0,
                }}
            >
                {preview ? (
                    <img src={preview} alt="" style={{ maxWidth: '100%', maxHeight: '100%', objectFit: 'contain' }} />
                ) : uploading ? (
                    <span style={{ fontSize: 11, color: '#94a3b8' }}>Uploading...</span>
                ) : (
                    <span style={{ fontSize: 11, color: '#94a3b8', textAlign: 'center' }}>Click to upload logo</span>
                )}
            </div>
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', gap: 6 }}>
                <input ref={inputRef} type="file" accept="image/*" style={{ display: 'none' }}
                    onChange={(e) => { if (e.target.files?.[0]) upload(e.target.files[0]); e.target.value = ''; }} />
                <button type="button" className="btn btn-sm" onClick={() => inputRef.current?.click()} disabled={uploading}
                    style={{ display: 'inline-flex', alignItems: 'center', gap: 5, width: 'fit-content' }}>
                    <Upload size={12} /> {uploading ? 'Uploading...' : 'Choose Logo'}
                </button>
                {value && (
                    <button type="button" className="btn btn-sm" onClick={() => onChange('')}
                        style={{ display: 'inline-flex', alignItems: 'center', gap: 5, width: 'fit-content', color: '#ef4444' }}>
                        <X size={12} /> Remove
                    </button>
                )}
                <span style={{ fontSize: 10, color: '#94a3b8' }}>PNG with transparent background looks best. Max 5MB.</span>
            </div>
        </div>
    );
}

function BrandPartners() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [search, setSearch] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [saving, setSaving] = useState(false);
    const [form, setForm] = useState({ name: '', logo: '', url: '', sort_order: 0, status: true });

    const load = (p, s) => {
        api.get('/admin/brand-partners', { params: { page: p, per_page: 10, search: s || undefined } })
            .then((res) => { setData(res.data.data || []); setMeta(res.data); })
            .catch(() => {});
    };

    useEffect(() => { load(page, search); }, [page]); // eslint-disable-line react-hooks/exhaustive-deps

    const searchSubmit = (e) => { e.preventDefault(); setPage(1); load(1, search); };

    const openModal = (partner = null) => {
        if (partner) {
            setEditing(partner);
            setForm({ name: partner.name, logo: partner.logo || '', url: partner.url || '', sort_order: partner.sort_order ?? 0, status: !!partner.status });
        } else {
            setEditing(null);
            setForm({ name: '', logo: '', url: '', sort_order: data.length, status: true });
        }
        setShowModal(true);
    };

    const change = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const save = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing) {
                await api.put(`/admin/brand-partners/${editing.id}`, form);
                toastr.success('Brand partner updated');
            } else {
                await api.post('/admin/brand-partners', form);
                toastr.success('Brand partner created');
            }
            setShowModal(false);
            load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save brand partner');
        } finally {
            setSaving(false);
        }
    };

    const remove = async (p) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete brand partner "${p.name}"?`, type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/admin/brand-partners/${p.id}`);
            toastr.success('Brand partner deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete brand partner');
        }
        load(page);
    };

    const toggle = async (p) => {
        const target = !p.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set "${p.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/brand-partners/${p.id}/status`);
            if (target) toastr.success('Brand partner activated');
            else toastr.warning('Brand partner deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Brand Partners</h1><p>Manage the "Trusted by" marquee shown on the homepage</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => openModal()}><Plus size={14} /> Add Brand</button>
            </div>

            <form className="search-bar-wrap" onSubmit={searchSubmit}>
                <input className="search-input" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search brand partners..." />
                <button className="btn btn-primary btn-sm" type="submit">Search</button>
            </form>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead>
                            <tr><th>#</th><th>Name</th><th>URL</th><th>Sort</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No brand partners found</td></tr>
                            ) : (
                                data.map((p) => (
                                    <tr key={p.id}>
                                        <td>{p.id}</td>
                                        <td>{p.logo_url
                                            ? <span style={{ display: 'inline-flex', alignItems: 'center', gap: 8 }}><img src={p.logo_url} alt="" style={{ height: 22, maxWidth: 90, objectFit: 'contain' }} /> {p.name}</span>
                                            : <strong>{p.name}</strong>}</td>
                                        <td>{p.url ? <a href={p.url} target="_blank" rel="noreferrer" style={{ color: 'var(--accent)' }}>{p.url}</a> : <span style={{ opacity: 0.5 }}>—</span>}</td>
                                        <td>{p.sort_order}</td>
                                        <td>
                                            <span className={`badge ${p.status ? 'badge-success' : 'badge-danger'}`}>{p.status ? 'Active' : 'Inactive'}</span>
                                        </td>
                                        <td>
                                            <div className="table-actions">
                                                <button className="btn-icon" title="Edit" onClick={() => openModal(p)}><Edit2 size={15} /></button>
                                                <button className="btn-icon" title="Toggle status" onClick={() => toggle(p)}><Power size={15} /></button>
                                                <button className="btn-icon" title="Delete" onClick={() => remove(p)}><Trash2 size={15} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                    {meta && meta.last_page > 1 && <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />}
                </div>
            </div>

            <Modal open={showModal} title={editing ? 'Edit Brand Partner' : 'Add Brand Partner'} onClose={() => setShowModal(false)} width="480px">
                <form onSubmit={save}>
                    <div className="form-group">
                        <label>Brand Name <span className="text-danger">*</span></label>
                        <input className="form-control" value={form.name} onChange={(e) => change('name', e.target.value)} required placeholder="e.g. NovaTech" />
                        <small style={{ opacity: 0.55 }}>Use generic / fictional names to avoid trademark issues with real brands.</small>
                    </div>
                    <div className="form-group">
                        <label>URL <small>(optional)</small></label>
                        <input className="form-control" value={form.url} onChange={(e) => change('url', e.target.value)} placeholder="https://" />
                    </div>
                    <div className="form-group">
                        <label>Logo <small>(optional)</small></label>
                        <LogoUpload value={form.logo} onChange={(val) => change('logo', val)} />
                        <small style={{ opacity: 0.55 }}>If empty, the brand name is shown as styled text.</small>
                    </div>
                    <div className="form-group">
                        <label>Sort Order</label>
                        <input className="form-control" type="number" min="0" value={form.sort_order} onChange={(e) => change('sort_order', Number(e.target.value))} />
                    </div>
                    <label className="toggle-row">
                        <input type="checkbox" checked={form.status} onChange={(e) => change('status', e.target.checked)} />
                        <span>Active</span>
                    </label>
                    <div className="modal-actions">
                        <button type="button" className="btn btn-sm" onClick={() => setShowModal(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={saving}>{saving ? 'Saving...' : 'Save'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default BrandPartners;
