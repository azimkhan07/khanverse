import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2 } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

function Permissions() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ name: '', slug: '', module: '', group: '', status: true });

    const load = (p) => {
        api.get('/admin/permissions', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const openCreate = () => { setEditing(null); setForm({ name: '', slug: '', module: '', group: '', status: true }); setModalOpen(true); };
    const openEdit = (p) => { setEditing(p); setForm({ name: p.name, slug: p.slug, module: p.module, group: p.group, status: !!p.status }); setModalOpen(true); };

    const submit = async (e) => {
        e.preventDefault();
        try {
            if (editing) {
                await api.post(`/admin/permissions/${editing.id}?_method=PUT`, form);
                toastr.success('Permission updated');
            } else {
                await api.post('/admin/permissions', form);
                toastr.success('Permission created');
            }
            setModalOpen(false); load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save permission');
        }
    };

    const remove = async (p) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete permission ${p.name}?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/permissions/${p.id}`);
            toastr.success('Permission deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete permission');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Permissions</h1><p>Manage access permissions</p></div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Permission</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No permissions found</div> : (
                        <table>
                            <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Module</th><th>Group</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                {data.map((p) => (
                                    <tr key={p.id}>
                                        <td>#{p.id}</td>
                                        <td style={{ fontWeight: 500 }}>{p.name}</td>
                                        <td>{p.slug}</td>
                                        <td>{p.module}</td>
                                        <td>{p.group}</td>
                                        <td><span className={`badge ${p.status ? 'badge-success' : 'badge-danger'}`}>{p.status ? 'Active' : 'Inactive'}</span></td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(p)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(p)}><Trash2 size={13} /></button>
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

            <Modal open={modalOpen} title={editing ? 'Edit Permission' : 'Add Permission'} onClose={() => setModalOpen(false)} width="460px">
                <form className="modal-content" onSubmit={submit}>
                    <div className="form-group">
                        <label>Name</label>
                        <input className="form-control" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                    </div>
                    <div className="form-group">
                        <label>Slug</label>
                        <input className="form-control" value={form.slug} onChange={(e) => setForm({ ...form, slug: e.target.value })} required />
                    </div>
                    <div className="form-grid-2">
                        <div className="form-group">
                            <label>Module</label>
                            <input className="form-control" value={form.module} onChange={(e) => setForm({ ...form, module: e.target.value })} required />
                        </div>
                        <div className="form-group">
                            <label>Group</label>
                            <input className="form-control" value={form.group} onChange={(e) => setForm({ ...form, group: e.target.value })} required />
                        </div>
                    </div>
                    <div className="form-group">
                        <label><input type="checkbox" checked={form.status} onChange={(e) => setForm({ ...form, status: e.target.checked })} /> Active</label>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm">{editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default Permissions;
