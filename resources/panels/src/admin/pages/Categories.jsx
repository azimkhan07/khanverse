import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

function Categories() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({ name: '', slug: '', icon: '', category_type: 'technical' });
    const [fieldsJson, setFieldsJson] = useState('[]');

    const defaultFields = [
        { key: 'years_experience', label: 'Years of experience', type: 'number', required: true },
        { key: 'service_area', label: 'Areas you serve (cities/colonies)', type: 'textarea', required: true },
    ];

    const load = (p) => {
        api.get('/admin/categories', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const openCreate = () => {
        setEditing(null);
        setForm({ name: '', slug: '', icon: '', category_type: 'technical' });
        setFieldsJson(JSON.stringify(defaultFields, null, 2));
        setModalOpen(true);
    };
    const openEdit = (c) => {
        setEditing(c);
        setForm({ name: c.name, slug: c.slug, icon: c.icon || '', category_type: c.category_type || 'technical' });
        setFieldsJson(c.form_fields ? JSON.stringify(c.form_fields, null, 2) : '[]');
        setModalOpen(true);
    };

    const submit = async (e) => {
        e.preventDefault();
        let fields;
        try {
            const parsed = JSON.parse(fieldsJson || '[]');
            if (!Array.isArray(parsed)) { toastr.danger('Form fields must be a JSON array'); return; }
            fields = parsed;
        } catch {
            toastr.danger('Form fields JSON is invalid');
            return;
        }
        try {
            const payload = editing ? { ...form, form_fields: fields } : { ...form, form_fields: fields, status: true };
            if (editing) {
                await api.post(`/admin/categories/${editing.id}?_method=PUT`, payload);
                toastr.success('Category updated');
            } else {
                await api.post('/admin/categories', payload);
                toastr.success('Category created');
            }
            setModalOpen(false); load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save category');
        }
    };

    const remove = async (c) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete category ${c.name}?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/categories/${c.id}`);
            toastr.success('Category deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete category');
        }
        load(page);
    };

    const toggle = async (c) => {
        const target = !c.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set category "${c.name}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/categories/${c.id}/status`);
            if (target) toastr.success('Category activated');
            else toastr.warning('Category deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Categories</h1><p>Manage service categories</p></div>
                <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add Category</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Type</th><th>Fields</th><th>Services</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={8} className="empty">No categories found</td></tr>
                            ) : (
                                data.map((c) => (
                                    <tr key={c.id}>
                                        <td>#{c.id}</td>
                                        <td style={{ fontWeight: 500 }}>{c.name}</td>
                                        <td>{c.slug}</td>
                                        <td>{c.category_type === 'field' ? 'Field' : 'Technical'}</td>
                                        <td>{Array.isArray(c.form_fields) ? c.form_fields.length : 0}</td>
                                        <td>{c.services_count ?? '-'}</td>
                                        <td>
                                            <button className={`status-toggle ${c.status ? 'on' : 'off'}`} onClick={() => toggle(c)}>
                                                <Power size={12} /> {c.status ? 'Active' : 'Inactive'}
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

            <Modal open={modalOpen} title={editing ? 'Edit Category' : 'Add Category'} onClose={() => setModalOpen(false)}>
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
                        <label>Icon (emoji or name)</label>
                        <input className="form-control" value={form.icon} onChange={(e) => setForm({ ...form, icon: e.target.value })} />
                    </div>
                    <div className="form-group">
                        <label>Category Type</label>
                        <select className="form-control" value={form.category_type} onChange={(e) => setForm({ ...form, category_type: e.target.value })}>
                            <option value="technical">Technical / Digital — unified seller profile</option>
                            <option value="field">Field Service — per-category custom profile fields</option>
                        </select>
                        {form.category_type === 'field' && (
                            <p style={{ fontSize: 12, color: 'var(--muted, #64748B)', marginTop: 6, lineHeight: 1.5 }}>
                                Field categories show extra profile fields to sellers. Fill the JSON below or leave <code>[]</code> to use the default set.
                            </p>
                        )}
                    </div>
                    {form.category_type === 'field' && (
                        <div className="form-group">
                            <label>Form Fields (JSON)</label>
                            <textarea
                                className="form-control"
                                rows={10}
                                style={{ fontFamily: 'monospace', fontSize: 12 }}
                                value={fieldsJson}
                                onChange={(e) => setFieldsJson(e.target.value)}
                            />
                            <p style={{ fontSize: 12, color: 'var(--muted, #64748B)', marginTop: 6, lineHeight: 1.5 }}>
                                Each field: <code>{'{"key":"experience_years","label":"Years","type":"number","required":true,"placeholder":"2"}'}</code>.
                                Types: text, number, tel, textarea, select, multiselect, checkbox. select/multiselect need <code>"options":["A","B"]</code>.
                            </p>
                        </div>
                    )}
                    <div className="modal-actions">
                        <button type="button" className="btn" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm">{editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default Categories;
