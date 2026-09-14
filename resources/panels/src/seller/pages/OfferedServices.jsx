import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Plus, X, Briefcase, Search, Check } from 'lucide-react';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import SearchableMultiSelect from '../../shared/components/SearchableMultiSelect';

const fadeUp = {
    hidden: { opacity: 0, y: 28 },
    visible: (i = 0) => ({
        opacity: 1,
        y: 0,
        transition: { duration: 0.5, delay: i * 0.07, ease: [0.22, 1, 0.36, 1] },
    }),
};

const stagger = { visible: { transition: { staggerChildren: 0.06 } } };

function OfferedServices() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [items, setItems] = useState([]);
    const [ids, setIds] = useState([]);
    const [categories, setCategories] = useState([]);
    const [modalOpen, setModalOpen] = useState(false);
    const [addCat, setAddCat] = useState('');
    const [options, setOptions] = useState([]);
    const [typesLoading, setTypesLoading] = useState(false);
    const [draftIds, setDraftIds] = useState([]);

    const load = () => {
        api.get('/seller/profile')
            .then(({ data }) => {
                const list = Array.isArray(data.service_types) ? data.service_types : [];
                setItems(list);
                setIds(Array.isArray(data.service_type_ids) ? data.service_type_ids.map(Number) : []);
            })
            .finally(() => setLoading(false));
    };

    useEffect(() => { load(); }, []);

    useEffect(() => {
        api.get('/frontend/categories').then(({ data: res }) => {
            const list = res.data || res || [];
            if (Array.isArray(list)) setCategories(list);
        }).catch(() => {});
    }, []);

    const catName = (id) => categories.find((c) => c.id === id)?.name || '';

    const openAdd = () => {
        setModalOpen(true);
        setAddCat(categories[0]?.id || '');
        setDraftIds(ids);
        setOptions([]);
    };

    useEffect(() => {
        if (!modalOpen || !addCat) return;
        const t = setTimeout(() => {
            setTypesLoading(true);
            api.get('/frontend/service-types', { params: { category_id: addCat } })
                .then(({ data }) => setOptions(Array.isArray(data.data) ? data.data : []))
                .catch(() => setOptions([]))
                .finally(() => setTypesLoading(false));
        }, 0);
        return () => clearTimeout(t);
    }, [modalOpen, addCat]);

    const save = async (nextIds) => {
        setSaving(true);
        try {
            const fd = new FormData();
            nextIds.forEach((id) => fd.append('service_type_ids[]', String(id)));
            await api.post('/seller/profile', fd);
            load();
        } catch (err) {
            return err.response?.data?.message || 'Failed to save offered services.';
        } finally {
            setSaving(false);
        }
        return null;
    };

    const submitAdd = async () => {
        const err = await save(draftIds);
        if (err) {
            return;
        }
        setModalOpen(false);
    };

    const removeService = async (item) => {
        const ok = await showConfirm({
            title: 'Remove service',
            message: `Remove "${item.name}" from your offered services?`,
            type: 'warning',
            confirmText: 'Remove',
        });
        if (!ok) return;
        save(ids.filter((id) => Number(id) !== Number(item.id)));
    };

    return (
        <motion.div initial="hidden" animate="visible" variants={stagger}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp}>
                <div>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <Briefcase size={22} style={{ color: 'var(--accent,#4F46E5)' }} />
                        Offered Services
                    </h1>
                    <p>
                        Pick the services you offer from the SkillNest catalog. Admins curate the catalog, so names stay
                        standard across the site.
                    </p>
                </div>
                <button className="btn btn-primary" onClick={openAdd} disabled={!categories.length}>
                    <Plus size={15} /> Add Services
                </button>
            </motion.div>

            {loading ? (
                <motion.div className="empty-state" variants={fadeUp}><p>Loading…</p></motion.div>
            ) : (
                <motion.div variants={fadeUp} custom={1}>
                    {items.length === 0 ? (
                        <div className="empty-state">
                            <Search size={26} style={{ opacity: .4, marginBottom: 8 }} />
                            <p>You have not added any services yet. Add the services you offer to build your profile.</p>
                            <button className="btn btn-primary" onClick={openAdd} disabled={!categories.length}>
                                <Plus size={15} /> Add Services
                            </button>
                        </div>
                    ) : (
                        <div className="grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(240px,1fr))', gap: 12 }}>
                            {items.map((item) => (
                                <div
                                    key={item.id}
                                    className="admin-card"
                                    style={{ padding: '14px 16px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 10 }}
                                >
                                    <div style={{ minWidth: 0 }}>
                                        <div style={{ fontWeight: 700, color: 'var(--text,#0F172A)', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            {item.name}
                                        </div>
                                        {catName(item.category_id) && (
                                            <div style={{ fontSize: 12, color: 'var(--muted,#64748B)', marginTop: 3 }}>
                                                {catName(item.category_id)}
                                            </div>
                                        )}
                                    </div>
                                    <button className="btn-icon btn-icon-danger" title="Remove" onClick={() => removeService(item)}>
                                        <X size={14} />
                                    </button>
                                </div>
                            ))}
                        </div>
                    )}
                </motion.div>
            )}

            {modalOpen && (
                <div style={{ position: 'fixed', inset: 0, zIndex: 1200, background: 'rgba(15,23,42,.55)', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: 16 }} onClick={() => setModalOpen(false)}>
                    <div
                        onClick={(e) => e.stopPropagation()}
                        style={{ width: '100%', maxWidth: 520, background: '#fff', borderRadius: 16, padding: 22, boxShadow: '0 24px 60px rgba(15,23,42,.25)' }}
                    >
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 14 }}>
                            <h3 style={{ margin: 0 }}>Add Offered Services</h3>
                            <button className="btn-icon" onClick={() => setModalOpen(false)}><X size={16} /></button>
                        </div>
                        <div className="form-group" style={{ marginBottom: 14 }}>
                            <label>Category</label>
                            <select className="form-input" value={addCat} onChange={(e) => setAddCat(e.target.value)}>
                                {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        </div>
                        <SearchableMultiSelect
                            options={options}
                            selected={draftIds}
                            onChange={setDraftIds}
                            loading={typesLoading}
                            placeholder="Search services in this category…"
                            emptyText="No services in this category yet."
                        />
                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 18 }}>
                            <button type="button" className="btn btn-secondary" onClick={() => setModalOpen(false)}>Cancel</button>
                            <button type="button" className="btn btn-primary" onClick={submitAdd} disabled={saving}>
                                {saving ? 'Saving…' : (<><Check size={15} /> Save Services</>)}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </motion.div>
    );
}

export default OfferedServices;