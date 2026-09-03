import { useState, useEffect } from 'react';
import { ArrowLeft, Save, Play } from 'lucide-react';
import { Link, useParams, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import api from '../../shared/api';
import toastr from '../../shared/toastr';

const ROLE_OPTIONS = ['admin', 'seller', 'buyer'];

function TutorialsForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = !!id;
    const [loading, setLoading] = useState(isEdit);
    const [saving, setSaving] = useState(false);
    const [form, setForm] = useState({
        title: '',
        description: '',
        video_url: '',
        thumbnail: '',
        duration: '',
        roles: ['admin'],
        sort_order: 0,
        status: true,
    });

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/admin/tutorials/${id}`).then(({ data }) => {
            const t = data.tutorial || {};
            setForm({
                title: t.title || '',
                description: t.description || '',
                video_url: t.video_url || '',
                thumbnail: t.thumbnail || '',
                duration: t.duration || '',
                roles: Array.isArray(t.roles) && t.roles.length ? t.roles : ['admin'],
                sort_order: t.sort_order || 0,
                status: !!t.status,
            });
        }).finally(() => setLoading(false));
    }, [id, isEdit]);

    const set = (name, value) => setForm((p) => ({ ...p, [name]: value }));

    const toggleRole = (r) => {
        const cur = form.roles.includes(r);
        set('roles', cur ? form.roles.filter((x) => x !== r) : [...form.roles, r]);
    };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            const payload = { ...form, status: form.status ? '1' : '0', sort_order: Number(form.sort_order || 0) };
            if (isEdit) {
                await api.post(`/admin/tutorials/${id}?_method=PUT`, payload);
                toastr.success('Tutorial updated');
            } else {
                await api.post('/admin/tutorials', payload);
                toastr.success('Tutorial created');
            }
            navigate('/tutorials');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save tutorial');
            setSaving(false);
        }
    };

    if (loading) return <div className="empty">Loading…</div>;

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <div className="page-heading">
                <div>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <Link to="/tutorials" style={{ color: 'var(--text-muted)', display: 'inline-flex' }}><ArrowLeft size={20} /></Link>
                        {isEdit ? 'Edit Tutorial' : 'Add Tutorial'}
                    </h1>
                    <p>Guide users through the platform with video tutorials.</p>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body">
                    <form onSubmit={submit} style={{ maxWidth: 720 }}>
                        <div className="form-group">
                            <label>Title</label>
                            <input className="form-control" value={form.title} onChange={(e) => set('title', e.target.value)} required placeholder="e.g. How to place your first order" />
                        </div>
                        <div className="form-group">
                            <label>Video URL</label>
                            <input className="form-control" value={form.video_url} onChange={(e) => set('video_url', e.target.value)} required placeholder="https://youtube.com/watch?v=…" />
                        </div>
                        <div className="form-grid-2">
                            <div className="form-group">
                                <label>Duration</label>
                                <input className="form-control" value={form.duration} onChange={(e) => set('duration', e.target.value)} placeholder="e.g. 3:45" />
                            </div>
                            <div className="form-group">
                                <label>Sort Order</label>
                                <input className="form-control" type="number" min="0" value={form.sort_order} onChange={(e) => set('sort_order', e.target.value)} />
                            </div>
                        </div>
                        <div className="form-group">
                            <label>Thumbnail URL</label>
                            <input className="form-control" value={form.thumbnail} onChange={(e) => set('thumbnail', e.target.value)} placeholder="https://…/thumbnail.jpg (optional)" />
                        </div>
                        <div className="form-group">
                            <label>Description</label>
                            <textarea className="form-control" rows="3" value={form.description} onChange={(e) => set('description', e.target.value)} placeholder="Short summary shown with the video" />
                        </div>
                        <div className="form-group">
                            <label>Show To</label>
                            <div style={{ display: 'flex', gap: 14, flexWrap: 'wrap' }}>
                                {ROLE_OPTIONS.map((r) => (
                                    <label key={r} style={{ display: 'flex', alignItems: 'center', gap: 6, cursor: 'pointer' }}>
                                        <input type="checkbox" checked={form.roles.includes(r)} onChange={() => toggleRole(r)} />
                                        <span style={{ textTransform: 'capitalize' }}>{r}</span>
                                    </label>
                                ))}
                            </div>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" checked={form.status} onChange={(e) => set('status', e.target.checked)} /> Active</label>
                        </div>
                        <div style={{ display: 'flex', gap: 10, marginTop: 8 }}>
                            <button type="submit" className="btn btn-primary" disabled={saving}><Save size={16} /> {saving ? 'Saving…' : 'Save Tutorial'}</button>
                            <Link to="/tutorials" className="btn">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </motion.div>
    );
}

export default TutorialsForm;
