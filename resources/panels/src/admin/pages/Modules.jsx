import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

const PANELS = ['admin', 'seller', 'buyer', 'frontend'];

function Modules() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ name: '', slug: '', route: '', view_path: '', controller: '', panel: 'admin', status: true });

    const load = (p) => {
        api.get('/admin/modules', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const openCreate = () => { setEditing(null); setForm({ name: '', slug: '', route: '', view_path: '', controller: '', panel: 'admin', status: true }); setModalOpen(true); };
    const openEdit = (m) => { setEditing(m); setForm({ name: m.name, slug: m.slug, route: m.route, view_path: m.view_path, controller: m.controller, panel: m.panel, status: !!m.status }); setModalOpen(true); };

    const submit = async (e) => {
        e.preventDefault();
        try {
            if (editing) {
                await api.post(`/admin/modules/${editing.id}?_method=PUT`, form);
                toastr.success('Module updated');
            } else {
                await api.post('/admin/modules', form);
                toastr.success('Module created');
            }
            setModalOpen(false); load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save module');
        }
    };

    const remove = async (m) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete module ${m.name}?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/modules/${m.id}`);
            toastr.success('Module deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete module');
        }
        load(page);
    };

    const toggle = async (m) => {
        const target = !m.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set module "${m.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/modules/${m.id}/status`);
            if (target) toastr.success('Module activated');
            else toastr.warning('Module deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Modules</h1><p>Manage application modules</p></div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Module</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Route</th><th>Panel</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={7} className="empty">No modules found</td></tr>
                            ) : (
                                data.map((m) => (
                                    <tr key={m.id}>
                                        <td>#{m.id}</td>
                                        <td style={{ fontWeight: 500 }}>{m.name}</td>
                                        <td>{m.slug}</td>
                                        <td>{m.route}</td>
                                        <td><span className="badge badge-info">{m.panel}</span></td>
                                        <td>
                                            <button className={`status-toggle ${m.status ? 'on' : 'off'}`} onClick={() => toggle(m)}>
                                                <Power size={12} /> {m.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(m)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(m)}><Trash2 size={13} /></button>
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

            <Modal open={modalOpen} title={editing ? 'Edit Module' : 'Add Module'} onClose={() => setModalOpen(false)} width="520px">
                <form className="modal-content" onSubmit={submit}>
                    <div className="form-group">
                        <label>Name</label>
                        <input className="form-control" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                    </div>
                    <div className="form-group">
                        <label>Slug</label>
                        <input className="form-control" value={form.slug} onChange={(e) => setForm({ ...form, slug: e.target.value })} required />
                    </div>
                    <div className="form-group">
                        <label>Route</label>
                        <input className="form-control" value={form.route} onChange={(e) => setForm({ ...form, route: e.target.value })} required />
                    </div>
                    <div className="form-grid-2">
                        <div className="form-group">
                            <label>View Path</label>
                            <input className="form-control" value={form.view_path} onChange={(e) => setForm({ ...form, view_path: e.target.value })} required />
                        </div>
                        <div className="form-group">
                            <label>Controller</label>
                            <input className="form-control" value={form.controller} onChange={(e) => setForm({ ...form, controller: e.target.value })} required />
                        </div>
                    </div>
                    <div className="form-grid-2">
                        <div className="form-group">
                            <label>Panel</label>
                            <select className="form-control" value={form.panel} onChange={(e) => setForm({ ...form, panel: e.target.value })}>
                                {PANELS.map((p) => <option key={p} value={p}>{p}</option>)}
                            </select>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" checked={form.status} onChange={(e) => setForm({ ...form, status: e.target.checked })} /> Active</label>
                        </div>
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

export default Modules;
