import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Power, Tag } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

function ServiceTypes() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [categories, setCategories] = useState([]);
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ name: '', slug: '', category_id: '', description: '', icon: '' });

    const load = (p) => {
        api.get('/admin/service-types', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    useEffect(() => {
        api.get('/admin/categories', { params: { all: 1, per_page: 100 } }).then((res) => {
            const list = Array.isArray(res.data.data) ? res.data.data : (res.data || []);
            setCategories(Array.isArray(list) ? list : []);
        }).catch(() => {});
    }, []);

    const openCreate = () => {
        setEditing(null);
        setForm({ name: '', slug: '', category_id: categories[0]?.id || '', description: '', icon: '' });
        setModalOpen(true);
    };
    const openEdit = (c) => {
        setEditing(c);
        setForm({ name: c.name, slug: c.slug, category_id: c.category_id || '', description: c.description || '', icon: c.icon || '' });
        setModalOpen(true);
    };

    const submit = async (e) => {
        e.preventDefault();
        const payload = { ...form };
        if (payload.slug === '') delete payload.slug;
        try {
            if (editing) {
                await api.post(`/admin/service-types/${editing.id}?_method=PUT`, payload);
                toastr.success('Service type updated');
            } else {
                await api.post('/admin/service-types', payload);
                toastr.success('Service type created');
            }
            setModalOpen(false); load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save service type');
        }
    };

    const remove = async (c) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete service type "${c.name}"? Sellers' selections will be removed too.`, type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/admin/service-types/${c.id}`);
            toastr.success('Service type deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete service type');
        }
        load(page);
    };

    const toggle = async (c) => {
        const target = !c.is_active;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set service type "${c.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/service-types/${c.id}/status`);
            if (target) toastr.success('Service type activated');
            else toastr.warning('Service type deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    const catName = (id) => categories.find((c) => c.id === id)?.name || 'Uncategorized';

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1><Tag size={20} style={{ marginRight: 8, verticalAlign: 'middle' }} />Service Types</h1>
                    <p>Catalog of services sellers can offer (e.g. carpenter, painter, plumber, logo design)</p>
                </div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Service Type</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No service types found</td></tr>
                            ) : (
                                data.map((c) => (
                                    <tr key={c.id}>
                                        <td>#{c.id}</td>
                                        <td style={{ fontWeight: 500 }}>{c.name}</td>
                                        <td>{c.category?.name || catName(c.category_id)}</td>
                                        <td>{c.slug}</td>
                                        <td>
                                            <button className={`status-toggle ${c.is_active ? 'on' : 'off'}`} onClick={() => toggle(c)}>
                                                <Power size={12} /> {c.is_active ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(c)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(c)}><Trash2 size={13} /></button>
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

            <Modal open={modalOpen} title={editing ? 'Edit Service Type' : 'Add Service Type'} onClose={() => setModalOpen(false)}>
                <form className="modal-content" onSubmit={submit}>
                    <div className="form-group">
                        <label>Category</label>
                        <select className="form-control" value={form.category_id} onChange={(e) => setForm({ ...form, category_id: e.target.value })}>
                            <option value="">No category</option>
                            {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                        </select>
                    </div>
                    <div className="form-group">
                        <label>Name</label>
                        <input className="form-control" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required placeholder="e.g. Plumber, Logo Design" />
                    </div>
                    <div className="form-group">
                        <label>Slug (optional, auto-filled)</label>
                        <input className="form-control" value={form.slug} onChange={(e) => setForm({ ...form, slug: e.target.value })} placeholder="plumber" />
                    </div>
                    <div className="form-group">
                        <label>Description (optional)</label>
                        <textarea className="form-control" rows={2} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} placeholder="Short description for this service" />
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

export default ServiceTypes;