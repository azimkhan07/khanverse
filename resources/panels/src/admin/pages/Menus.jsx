import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2 } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

function flatten(tree, depth = 0, result = []) {
    (tree || []).forEach((m) => {
        result.push({ ...m, depth });
        if (m.children && m.children.length) flatten(m.children, depth + 1, result);
    });
    return result;
}

function Menus() {
    const [rows, setRows] = useState([]);
    const [parents, setParents] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ title: '', icon: '', route_name: '', parent_id: '', sort_order: 0, is_active: true });

    const load = (p) => {
        api.get('/admin/menus', { params: { page: p, per_page: 10 } }).then((res) => {
            const tree = res.data.data ?? [];
            setRows(flatten(tree));
            setMeta(res.data);
        }).catch(() => {});
        api.get('/admin/menus/parents').then((res) => {
            setParents(res.data.data ?? res.data ?? []);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const openCreate = () => { setEditing(null); setForm({ title: '', icon: '', route_name: '', parent_id: '', sort_order: 0, is_active: true }); setModalOpen(true); };
    const openEdit = (m) => { setEditing(m); setForm({ title: m.title, icon: m.icon || '', route_name: m.route_name || '', parent_id: m.parent_id || '', sort_order: m.sort_order || 0, is_active: !!m.is_active }); setModalOpen(true); };

    const submit = async (e) => {
        e.preventDefault();
        const payload = { ...form, parent_id: form.parent_id || null };
        try {
            if (editing) {
                await api.post(`/admin/menus/${editing.id}?_method=PUT`, payload);
                toastr.success('Menu updated');
            } else {
                await api.post('/admin/menus', payload);
                toastr.success('Menu created');
            }
            setModalOpen(false); load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save menu');
        }
    };

    const remove = async (m) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete menu "${m.title}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/menus/${m.id}`);
            toastr.success('Menu deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete menu');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Menus</h1><p>Manage navigation menus</p></div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Menu</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {rows.length === 0 ? <div className="empty">No menus found</div> : (
                        <table>
                            <thead><tr><th>Title</th><th>Route</th><th>Icon</th><th>Parent</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                {rows.map((m) => (
                                    <tr key={m.id}>
                                        <td style={{ fontWeight: 500, paddingLeft: 10 + m.depth * 20 }}>{m.title}</td>
                                        <td>{m.route_name || '-'}</td>
                                        <td>{m.icon || '-'}</td>
                                        <td>{m.parent?.title || '-'}</td>
                                        <td><span className={`badge ${m.is_active ? 'badge-success' : 'badge-danger'}`}>{m.is_active ? 'Active' : 'Inactive'}</span></td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(m)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(m)}><Trash2 size={13} /></button>
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

            <Modal open={modalOpen} title={editing ? 'Edit Menu' : 'Add Menu'} onClose={() => setModalOpen(false)} width="460px">
                <form className="modal-content" onSubmit={submit}>
                    <div className="form-group">
                        <label>Title</label>
                        <input className='form-control' value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} required />
                    </div>
                    <div className="form-group">
                        <label>Route Name</label>
                        <input className="form-control" value={form.route_name} onChange={(e) => setForm({ ...form, route_name: e.target.value })} placeholder="admin.orders" />
                    </div>
                    <div className="form-group">
                        <label>Icon</label>
                        <input className="form-control" value={form.icon} onChange={(e) => setForm({ ...form, icon: e.target.value })} placeholder="feather icon-folder" />
                    </div>
                    <div className="form-group">
                        <label>Parent</label>
                        <select className="form-control" value={form.parent_id} onChange={(e) => setForm({ ...form, parent_id: e.target.value })}>
                            <option value="">None (top level)</option>
                            {parents.map((p) => <option key={p.id} value={p.id}>{p.title}</option>)}
                        </select>
                    </div>
                    <div className="form-group">
                        <label>Sort Order</label>
                        <input type="number" className="form-control" value={form.sort_order} onChange={(e) => setForm({ ...form, sort_order: e.target.value })} />
                    </div>
                    <div className="form-group">
                        <label><input type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active</label>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn btn-sm" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm">{editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default Menus;
