import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Save, Briefcase } from 'lucide-react';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';

const emptyForm = {
    title: '', slug: '', description: '', price: '', delivery_days: 1, revisions: 0,
    delivery_method: 'digital', category_id: '', status: 'draft',
};

const fadeUp = {
    hidden: { opacity: 0, y: 28 },
    visible: (i = 0) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.07, ease: [0.22, 1, 0.36, 1] },
    }),
};

const stagger = {
    visible: { transition: { staggerChildren: 0.06 } },
};

function ServiceForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = !!id;

    const [form, setForm] = useState(emptyForm);
    const [categories, setCategories] = useState([]);
    const [thumbnail, setThumbnail] = useState(null);
    const [currentThumb, setCurrentThumb] = useState(null);
    const [saving, setSaving] = useState(false);
    const [loading, setLoading] = useState(isEdit);

    useEffect(() => {
        api.get('/frontend/categories').then(({ data: res }) => {
            const list = res.data || res || [];
            if (Array.isArray(list) && list.length) setCategories(list);
        }).catch(() => {});
    }, []);

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/seller/services/${id}`).then(({ data }) => {
            setForm({
                title: data.title || '', slug: data.slug || '', description: data.description || '',
                price: data.price ?? '', delivery_days: data.delivery_days || 1, revisions: data.revisions || 0,
                delivery_method: data.delivery_method || 'digital',
                category_id: data.category_id || data.category?.id || '', status: data.status || 'draft',
            });
            setCurrentThumb(data.thumbnail || null);
            setLoading(false);
        }).catch(() => setLoading(false));
    }, [id, isEdit]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (name === 'title' && !isEdit) {
            setForm((prev) => ({
                ...prev,
                title: value,
                slug: value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''),
            }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        const payload = new FormData();
        Object.entries(form).forEach(([k, v]) => payload.append(k, String(v)));
        if (thumbnail) payload.append('thumbnail', thumbnail);

        try {
            let res;
            if (isEdit) {
                res = await api.post(`/seller/services/${id}?_method=PUT`, payload, { headers: { 'Content-Type': 'multipart/form-data' } });
            } else {
                res = await api.post('/seller/services', payload, { headers: { 'Content-Type': 'multipart/form-data' } });
            }
            await showDialog({ type: 'success', title: isEdit ? 'Service Updated' : 'Service Created', message: res.data.message || 'Saved successfully.' });
            navigate(`/services/${isEdit ? id : res.data.service.id}`);
        } catch (err) {
            await showDialog({ type: 'danger', title: 'Save Failed', message: err.response?.data?.message || 'Something went wrong.' });
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return <div className="empty-state"><p>Loading…</p></div>;
    }

    return (
        <motion.div
            className="kv-form-page"
            initial="hidden"
            animate="visible"
            variants={stagger}
        >
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <Link to="/services" className="btn btn-secondary btn-sm" style={{ marginBottom: 12 }}>
                        <ArrowLeft size={14} /> Back to Services
                    </Link>
                    <h1>{isEdit ? 'Edit Service' : 'Add New Service'}</h1>
                </div>
                <span className="badge badge-primary" style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                    <Briefcase size={13} /> {isEdit ? `#${id}` : 'New'}
                </span>
            </motion.div>

            <motion.div className="kv-form-hero" variants={fadeUp} custom={1}>
                <h2>{isEdit ? 'Update your service listing' : 'Create a compelling service'}</h2>
                <p>Showcase your work clearly — buyers decide in seconds.</p>
            </motion.div>

            <motion.form onSubmit={handleSubmit} className="kv-form-card" variants={fadeUp} custom={2}>
                <motion.div className="kv-form-grid" variants={stagger}>
                    <motion.div className="kv-field full" variants={fadeUp}>
                        <label>Service Title</label>
                        <input className="form-input" name="title" value={form.title} onChange={handleChange} required placeholder="e.g. Premium Logo Design" />
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Slug</label>
                        <input className="form-input" name="slug" value={form.slug} onChange={handleChange} required placeholder="premium-logo-design" />
                        <span className="hint">Used in the service URL.</span>
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Category</label>
                        <select className="form-select" name="category_id" value={form.category_id} onChange={handleChange} required>
                            <option value="">Select category</option>
                            {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                        </select>
                    </motion.div>

                    <motion.div className="kv-field full" variants={fadeUp}>
                        <label>Description</label>
                        <textarea className="form-input" name="description" rows={5} value={form.description} onChange={handleChange} required placeholder="Describe what buyers get…" />
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Price (₹)</label>
                        <input className="form-input" name="price" type="number" min="0" step="0.01" value={form.price} onChange={handleChange} required placeholder="500" />
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Delivery Days</label>
                        <input className="form-input" name="delivery_days" type="number" min="1" value={form.delivery_days} onChange={handleChange} required />
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Revisions</label>
                        <input className="form-input" name="revisions" type="number" min="0" value={form.revisions} onChange={handleChange} />
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Delivery Method</label>
                        <select className="form-select" name="delivery_method" value={form.delivery_method} onChange={handleChange}>
                            <option value="digital">Digital (File upload)</option>
                            <option value="hosting">Hosting / Handover</option>
                        </select>
                    </motion.div>

                    {form.delivery_method === 'hosting' && (
                        <motion.div
                            className="kv-field full hint"
                            style={{ background: 'rgba(99,102,241,.08)', borderRadius: 10, padding: '10px 14px' }}
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            transition={{ duration: 0.3 }}
                        >
                            Hosting services use a secure delivery-key handover flow and require the buyer to submit hosting credentials.
                        </motion.div>
                    )}

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Status</label>
                        <select className="form-select" name="status" value={form.status} onChange={handleChange}>
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                        </select>
                    </motion.div>

                    <motion.div className="kv-field" variants={fadeUp}>
                        <label>Thumbnail</label>
                        <input className="form-input" type="file" accept="image/*" onChange={(e) => setThumbnail(e.target.files[0] || null)} />
                        {(currentThumb || thumbnail) && (
                            <motion.img
                                src={thumbnail ? URL.createObjectURL(thumbnail) : currentThumb}
                                alt=""
                                style={{ width: 96, height: 96, objectFit: 'cover', borderRadius: 12, marginTop: 8 }}
                                initial={{ opacity: 0, scale: 0.8 }}
                                animate={{ opacity: 1, scale: 1 }}
                                transition={{ duration: 0.3 }}
                            />
                        )}
                    </motion.div>
                </motion.div>

                <motion.div className="kv-form-actions" variants={fadeUp}>
                    <Link to="/services" className="btn btn-secondary">Cancel</Link>
                    <motion.button
                        type="submit"
                        className="btn btn-primary"
                        disabled={saving}
                        whileHover={{
                            scale: 1.03,
                            boxShadow: '0 0 24px rgba(99,102,241,0.35), 0 0 48px rgba(99,102,241,0.12)',
                        }}
                        whileTap={{ scale: 0.97 }}
                        transition={{ type: 'spring', stiffness: 400, damping: 20 }}
                    >
                        <Save size={15} /> {saving ? 'Saving…' : isEdit ? 'Update Service' : 'Create Service'}
                    </motion.button>
                </motion.div>
            </motion.form>
        </motion.div>
    );
}

export default ServiceForm;
