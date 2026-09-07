import { useState, useEffect, useRef } from 'react';
import { Plus, Edit2, Trash2, Power, Upload, X } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';
import Modal from '../components/Modal';

function ImageUpload({ value, onChange }) {
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
            toastr.success('Image uploaded');
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
                    width: 90, height: 90, borderRadius: 12, border: '2px dashed #d1d5db',
                    display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer',
                    background: '#f8fafc', overflow: 'hidden', padding: 4, flexShrink: 0, borderRadius: '50%',
                }}
            >
                {preview ? (
                    <img src={preview} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '50%' }} />
                ) : uploading ? (
                    <span style={{ fontSize: 11, color: '#94a3b8' }}>Uploading...</span>
                ) : (
                    <span style={{ fontSize: 11, color: '#94a3b8', textAlign: 'center' }}>Click to upload photo</span>
                )}
            </div>
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', gap: 6 }}>
                <input ref={inputRef} type="file" accept="image/*" style={{ display: 'none' }}
                    onChange={(e) => { if (e.target.files?.[0]) upload(e.target.files[0]); e.target.value = ''; }} />
                <button type="button" className="btn btn-sm" onClick={() => inputRef.current?.click()} disabled={uploading}
                    style={{ display: 'inline-flex', alignItems: 'center', gap: 5, width: 'fit-content' }}>
                    <Upload size={12} /> {uploading ? 'Uploading...' : 'Choose Photo'}
                </button>
                {value && (
                    <button type="button" className="btn btn-sm" onClick={() => onChange('')}
                        style={{ display: 'inline-flex', alignItems: 'center', gap: 5, width: 'fit-content', color: '#ef4444' }}>
                        <X size={12} /> Remove
                    </button>
                )}
                <span style={{ fontSize: 10, color: '#94a3b8' }}>Square photos look best. Max 5MB. Leave empty to show initials.</span>
            </div>
        </div>
    );
}

function TeamMembers() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [search, setSearch] = useState('');
    const [filterStatus, setFilterStatus] = useState('active');
    const [showModal, setShowModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [saving, setSaving] = useState(false);
    const [form, setForm] = useState({ name: '', role: '', tagline: '', image: '', sort_order: 0, status: true });

    const load = (p, s, st) => {
        const status = st === 'all' ? undefined : (st === 'active' ? '1' : '0');
        api.get('/admin/team-members', { params: { page: p, per_page: 10, search: s || undefined, status } })
            .then((res) => { setData(res.data.data || []); setMeta(res.data); })
            .catch(() => {});
    };

    useEffect(() => { load(page, search, filterStatus); }, [page]); // eslint-disable-line react-hooks/exhaustive-deps

    const searchSubmit = (e) => { e.preventDefault(); setPage(1); load(1, search, filterStatus); };

    const changeFilter = (st) => {
        setFilterStatus(st);
        setPage(1);
        load(1, search, st);
    };

    const openModal = (member = null) => {
        if (member) {
            setEditing(member);
            setForm({
                name: member.name,
                role: member.role || '',
                tagline: member.tagline || '',
                image: member.image || '',
                sort_order: member.sort_order ?? 0,
                status: !!member.status,
            });
        } else {
            setEditing(null);
            setForm({ name: '', role: '', tagline: '', image: '', sort_order: data.length, status: true });
        }
        setShowModal(true);
    };

    const change = (key, value) => setForm((f) => ({ ...f, [key]: value }));

    const save = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing) {
                await api.put(`/admin/team-members/${editing.id}`, form);
                toastr.success('Team member updated');
            } else {
                await api.post('/admin/team-members', form);
                toastr.success('Team member created');
            }
            setShowModal(false);
            load(page, search, filterStatus);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save team member');
        } finally {
            setSaving(false);
        }
    };

    const remove = async (p) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete team member "${p.name}"?`, type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/admin/team-members/${p.id}`);
            toastr.success('Team member deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete team member');
        }
        load(page, search, filterStatus);
    };

    const toggle = async (p) => {
        const target = !p.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set "${p.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/team-members/${p.id}/status`);
            if (target) toastr.success('Team member activated');
            else toastr.warning('Team member deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page, search, filterStatus);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Team Members</h1><p>Founders &amp; leadership shown in the public "Our Team" section</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => openModal()}><Plus size={14} /> Add Member</button>
            </div>

            <form className="search-bar-wrap" onSubmit={searchSubmit}>
                <input className="search-input" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search team members..." />
                <button className="btn btn-primary btn-sm" type="submit">Search</button>
            </form>

            <div className="filter-tabs" style={{ display: 'flex', gap: 8, marginBottom: 12 }}>
                {[
                    { key: 'active', label: 'Active' },
                    { key: 'inactive', label: 'Inactive' },
                    { key: 'all', label: 'All' },
                ].map((f) => (
                    <button
                        key={f.key}
                        type="button"
                        className={`btn btn-sm ${filterStatus === f.key ? 'btn-primary' : ''}`}
                        onClick={() => changeFilter(f.key)}
                    >
                        {f.label}
                    </button>
                ))}
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead>
                            <tr><th>#</th><th>Member</th><th>Role</th><th>Sort</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No team members found</td></tr>
                            ) : (
                                data.map((p) => (
                                    <tr key={p.id}>
                                        <td>{p.id}</td>
                                        <td>
                                            <span style={{ display: 'inline-flex', alignItems: 'center', gap: 8 }}>
                                                {p.image_url
                                                    ? <img src={p.image_url} alt="" style={{ width: 34, height: 34, borderRadius: '50%', objectFit: 'cover' }} />
                                                    : <span style={{ width: 34, height: 34, borderRadius: '50%', background: 'linear-gradient(135deg,#4F46E5,#7C3AED)', color: '#fff', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: 12, fontWeight: 700 }}>{p.name?.slice(0, 1) || '?'}</span>}
                                                <strong>{p.name}</strong>
                                            </span>
                                        </td>
                                        <td>
                                            <div>{p.role || <span style={{ opacity: 0.5 }}>—</span>}</div>
                                            {p.tagline && <div style={{ fontSize: 11, color: 'var(--text-muted,#94a3b8)' }}>{p.tagline}</div>}
                                        </td>
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

            <Modal open={showModal} title={editing ? 'Edit Team Member' : 'Add Team Member'} onClose={() => setShowModal(false)} width="500px">
                <form onSubmit={save}>
                    <div className="form-group">
                        <label>Photo <small>(optional)</small></label>
                        <ImageUpload value={form.image} onChange={(val) => change('image', val)} />
                    </div>
                    <div className="form-group">
                        <label>Full Name <span className="text-danger">*</span></label>
                        <input className="form-control" value={form.name} onChange={(e) => change('name', e.target.value)} required placeholder="e.g. Azim Khan" />
                    </div>
                    <div className="form-group">
                        <label>Role / Title <small>(optional)</small></label>
                        <input className="form-control" value={form.role} onChange={(e) => change('role', e.target.value)} placeholder="e.g. Founder of SkillNest" />
                        <small style={{ opacity: 0.55 }}>Shown as the main headline under the name.</small>
                    </div>
                    <div className="form-group">
                        <label>Tagline <small>(optional)</small></label>
                        <input className="form-control" value={form.tagline} onChange={(e) => change('tagline', e.target.value)} placeholder="e.g. Brand Partner with AMTech" />
                        <small style={{ opacity: 0.55 }}>Shown as small text under the role.</small>
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

export default TeamMembers;