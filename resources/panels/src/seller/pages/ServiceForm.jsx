import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ArrowLeft, Save, Briefcase, Sparkles } from 'lucide-react';
import api from '../../shared/api';
import { showDialog } from '../../shared/components/Dialog';
import SearchableMultiSelect from '../../shared/components/SearchableMultiSelect';

const emptyForm = {
    title: '', slug: '', description: '', price: '', delivery_days: 1, revisions: 0,
    delivery_method: 'digital', category_id: '', status: 'draft',
    visit_start_time: '', visit_end_time: '', working_days: [],
};

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

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
    const [serviceTypes, setServiceTypes] = useState([]);
    const [serviceTypeIds, setServiceTypeIds] = useState([]);
    const [typesLoading, setTypesLoading] = useState(false);
    const [aiLoading, setAiLoading] = useState(false);
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

    const loadServiceTypes = (categoryId, keep) => {
        if (!categoryId) { if (!keep) setServiceTypes([]); return; }
        setTypesLoading(true);
        api.get('/frontend/service-types', { params: { category_id: categoryId } })
            .then(({ data: res }) => setServiceTypes(Array.isArray(res.data) ? res.data : []))
            .catch(() => { if (!keep) setServiceTypes([]); })
            .finally(() => setTypesLoading(false));
    };

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/seller/services/${id}`).then(({ data }) => {
            setForm({
                title: data.title || '', slug: data.slug || '', description: data.description || '',
                price: data.price ?? '', delivery_days: data.delivery_days || 1, revisions: data.revisions || 0,
                delivery_method: data.delivery_method || 'digital',
                category_id: data.category_id || data.category?.id || '', status: data.status || 'draft',
                visit_start_time: data.visit_start_time || '', visit_end_time: data.visit_end_time || '',
                working_days: Array.isArray(data.working_days) ? data.working_days : [],
            });
            const existing = Array.isArray(data.service_types)
                ? data.service_types.map((t) => (typeof t === 'object' ? t.id : t))
                : [];
            setServiceTypeIds(existing);
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
        if (name === 'category_id') {
            setServiceTypeIds([]);
            loadServiceTypes(value);
        }
    };

    const toggleDay = (day) => {
        setForm((prev) => {
            const days = Array.isArray(prev.working_days) ? prev.working_days : [];
            const next = days.includes(day) ? days.filter((d) => d !== day) : [...days, day];
            return { ...prev, working_days: next };
        });
    };

    const selectedCat = categories.find((c) => String(c.id) === String(form.category_id));
    const isFieldCategory = selectedCat?.category_type === 'field';

    const runAiSuggest = async () => {
        if (!form.title.trim() || !form.category_id) {
            await showDialog({
                type: 'danger',
                title: 'AI Suggest',
                message: 'Add a service title and pick a category first — the AI uses them to suggest matching services.',
            });
            return;
        }
        setAiLoading(true);
        try {
            const { data: res } = await api.post('/ai/suggest-services', {
                title: form.title,
                description: form.description,
                category_id: form.category_id,
            });
            const suggested = Array.isArray(res.data) ? res.data : [];
            if (!suggested.length) {
                await showDialog({ type: 'warning', title: 'AI Suggest', message: 'No matching services were found. Adjust your keywords and try again.' });
                return;
            }
            const merged = [];
            suggested.forEach((s) => {
                if (!merged.some((m) => m.id === s.id)) merged.push(s);
                setServiceTypes((prev) => (prev.some((p) => p.id === s.id) ? prev : [...prev, { id: s.id, name: s.name, category_id: s.category_id, category: s.category }]));
            });
            setServiceTypeIds((prev) => [...new Set([...prev, ...merged.map((m) => m.id)])]);
            await showDialog({ type: 'success', title: 'AI Suggest', message: `${suggested.length} matching services suggested (from catalog).` });
        } catch (err) {
            await showDialog({ type: 'danger', title: 'AI Suggest', message: err.response?.data?.message || 'AI suggestion failed. Try again.' });
        } finally {
            setAiLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        const payload = new FormData();
        Object.entries(form).forEach(([k, v]) => {
            if (Array.isArray(v)) return;
            payload.append(k, String(v));
        });
        if (Array.isArray(form.working_days) && form.working_days.length) {
            form.working_days.forEach((d) => payload.append('working_days[]', d));
        }
        serviceTypeIds.forEach((id) => payload.append('service_type_ids[]', String(id)));
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
            <motion.div className="page-heading page-toolbar" variants={fadeUp} style={{ marginBottom: 10 }}>
                <div>
                    <Link to="/services" className="btn btn-secondary btn-sm" style={{ marginBottom: 8 }}>
                        <ArrowLeft size={14} /> Back to Services
                    </Link>
                    <h1 style={{ fontSize: 20 }}>{isEdit ? 'Edit Service' : 'Add Service'}</h1>
                </div>
                <span className="badge badge-primary" style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                    <Briefcase size={13} /> {isEdit ? `#${id}` : 'New'}
                </span>
            </motion.div>

            <motion.div className="kv-form-hero" style={{ padding: '14px 18px', marginBottom: 12 }} variants={fadeUp} custom={1}>
                <h2 style={{ fontSize: 18 }}>{isEdit ? 'Update your service listing' : 'Create a service'}</h2>
                <p style={{ fontSize: 13, margin: 4 }}>Keep it short and clear.</p>
            </motion.div>

            <motion.form onSubmit={handleSubmit} className="kv-form-card" style={{ padding: '18px 20px' }} variants={fadeUp} custom={2}>
                <motion.div className="kv-form-grid" style={{ gap: '14px 16px' }} variants={stagger}>
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
                        <textarea className="form-input" name="description" rows={4} value={form.description} onChange={handleChange} required placeholder="Describe what buyers get…"/>
                    </motion.div>

                    <motion.div className="kv-field full" variants={fadeUp}>
                        <label style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 10 }}>
                            <span>Service Type(s)</span>
                            <button
                                type="button"
                                className="btn btn-sm"
                                onClick={runAiSuggest}
                                disabled={aiLoading || !form.category_id}
                                style={{
                                    display: 'inline-flex', alignItems: 'center', gap: 6,
                                    background: 'linear-gradient(135deg,#4F46E5,#7C3AED)',
                                    color: '#fff', border: 'none', borderRadius: 10, padding: '5px 12px',
                                    fontSize: 12, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit',
                                }}
                            >
                                <Sparkles size={13} />
                                {aiLoading ? 'Thinking…' : 'AI Suggest'}
                            </button>
                        </label>
                        <p className="hint" style={{ marginTop: 2 }}>
                            Select from admin-managed service types for this category.
                        </p>
                        <SearchableMultiSelect
                            options={serviceTypes}
                            selected={serviceTypeIds}
                            onChange={setServiceTypeIds}
                            loading={typesLoading}
                            disabled={!form.category_id}
                            placeholder={form.category_id ? 'Search service types in this category…' : 'Select a category first'}
                            emptyText={form.category_id ? 'No service types in this category yet.' : 'Select a category to load service types.'}
                        />
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

                    {isFieldCategory && (
                        <motion.div
                            className="kv-field full"
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            exit={{ opacity: 0, height: 0 }}
                            transition={{ duration: 0.3 }}
                            style={{ borderTop: '1px solid rgba(99,102,241,.15)', paddingTop: 16, marginTop: 8 }}
                        >
                            <label>Working Hours (show to buyers)</label>
                            <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
                                <div style={{ flex: '1 1 160px' }}>
                                    <label className="hint" style={{ display: 'block', marginBottom: 4 }}>Starts at</label>
                                    <input
                                        className="form-input"
                                        type="time"
                                        name="visit_start_time"
                                        value={form.visit_start_time}
                                        onChange={handleChange}
                                        required={isFieldCategory}
                                    />
                                </div>
                                <div style={{ flex: '1 1 160px' }}>
                                    <label className="hint" style={{ display: 'block', marginBottom: 4 }}>Ends at</label>
                                    <input
                                        className="form-input"
                                        type="time"
                                        name="visit_end_time"
                                        value={form.visit_end_time}
                                        onChange={handleChange}
                                        required={isFieldCategory}
                                    />
                                </div>
                            </div>
                            <label className="hint" style={{ display: 'block', margin: '10px 0 6px' }}>
                                Working days
                            </label>
                            <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                                {weekDays.map((day) => {
                                    const active = Array.isArray(form.working_days) && form.working_days.includes(day);
                                    return (
                                        <button
                                            key={day}
                                            type="button"
                                            onClick={() => toggleDay(day)}
                                            style={{
                                                padding: '5px 12px', borderRadius: 999, border: active ? '1px solid #4F46E5' : '1px solid #d1d5db',
                                                background: active ? 'rgba(79,70,229,.12)' : '#fff', color: active ? '#4F46E5' : '#6b7280',
                                                fontSize: 12, fontWeight: active ? 700 : 500, cursor: 'pointer', fontFamily: 'inherit',
                                            }}
                                        >
                                            {day}
                                        </button>
                                    );
                                })}
                            </div>
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
