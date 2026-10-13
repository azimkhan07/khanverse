import { useState, useEffect, useCallback } from 'react';
import { Plus, Edit2, Trash2, Search, X, Upload, MapPin, Globe, Navigation, Compass, Download } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';

const TABS = [
    { key: 'countries', label: 'Countries', icon: Globe, endpoint: '/admin/locations/countries' },
    { key: 'states', label: 'States', icon: Compass, endpoint: '/admin/locations/states' },
    { key: 'cities', label: 'Cities', icon: Navigation, endpoint: '/admin/locations/cities' },
];

const IMPORT_COLUMNS = {
    countries: 'name (required), iso2, iso3, phone_code, currency, currency_symbol, timezone, emoji, status',
    states: 'country (required, name or code), name (required), state_code, status',
    cities: 'country, state (required), name (required), status',
};

function Locations() {
    const [tab, setTab] = useState('countries');
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [q, setQ] = useState('');
    const [countryFilter, setCountryFilter] = useState('');
    const [stateFilter, setStateFilter] = useState('');
    const [countries, setCountries] = useState([]);
    const [states, setStates] = useState([]);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState({});
    const [saving, setSaving] = useState(false);

    const [importOpen, setImportOpen] = useState(false);
    const [importFile, setImportFile] = useState(null);
    const [importing, setImporting] = useState(false);

    const activeTab = TABS.find((t) => t.key === tab);

    const load = useCallback((p) => {
        const params = { page: p, per_page: 15 };
        if (q.trim() !== '') params.q = q.trim();
        if (tab === 'states' && countryFilter) params.country_id = countryFilter;
        if (tab === 'cities') {
            if (stateFilter) params.state_id = stateFilter;
            if (countryFilter) params.country_id = countryFilter;
        }
        api.get(activeTab.endpoint, { params }).then((res) => {
            setData(res.data.data || []);
            setMeta(res.data);
        }).catch(() => setData([]));
    }, [tab, q, countryFilter, stateFilter, activeTab]);

    useEffect(() => { load(page); }, [page, load]);

    useEffect(() => {
        const t = setTimeout(() => { setPage(1); load(1); }, 350);
        return () => clearTimeout(t);
    }, [q, countryFilter, stateFilter, load]);

    useEffect(() => {
        api.get('/admin/locations/countries', { params: { all: 1 } })
            .then((res) => setCountries(res.data.data || [])).catch(() => {});
    }, []);

    useEffect(() => {
        if (!countryFilter && tab !== 'cities') { setStates([]); return; }
        api.get('/admin/locations/states', { params: { all: 1, country_id: countryFilter || undefined } })
            .then((res) => setStates(res.data.data || [])).catch(() => setStates([]));
    }, [countryFilter, tab]);

    const resetFilters = () => { setQ(''); setCountryFilter(''); setStateFilter(''); };

    const switchTab = (key) => {
        setTab(key); setPage(1); setData([]);
        setQ(''); setCountryFilter(''); setStateFilter('');
    };

    const openCreate = () => {
        setEditing(null);
        setForm(tab === 'countries'
            ? { name: '', iso2: '', iso3: '', phone_code: '', currency: '', currency_symbol: '', timezone: '', emoji: '', status: true }
            : tab === 'states'
                ? { country_id: countryFilter || '', name: '', state_code: '', status: true }
                : { country_id: countryFilter || '', state_id: stateFilter || '', name: '', status: true });
        setModalOpen(true);
    };

    const openEdit = (row) => {
        setEditing(row);
        setForm({ ...row, status: !!row.status });
        setModalOpen(true);
    };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing) {
                await api.put(`${activeTab.endpoint}/${editing.id}`, form);
                toastr.success('Updated successfully');
            } else {
                await api.post(activeTab.endpoint, form);
                toastr.success('Created successfully');
            }
            setModalOpen(false);
            load(page);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save');
        } finally {
            setSaving(false);
        }
    };

    const remove = async (row) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete "${row.name}"?`, type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`${activeTab.endpoint}/${row.id}`);
            toastr.success('Deleted successfully');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete');
        }
        load(page);
    };

    const runImport = async (e) => {
        e.preventDefault();
        if (!importFile) { toastr.warning('Please choose a file'); return; }
        setImporting(true);
        const fd = new FormData();
        fd.append('type', tab);
        fd.append('file', importFile);
        try {
            const res = await api.post('/admin/locations/import', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            toastr.success(res.data.message || 'Import complete');
            setImportOpen(false);
            setImportFile(null);
            load(1);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Import failed');
        } finally {
            setImporting(false);
        }
    };

    const downloadTemplate = () => {
        const headers = {
            countries: 'name,iso2,iso3,phone_code,currency,currency_symbol,timezone,emoji,status',
            states: 'country,name,state_code,status',
            cities: 'country,state,name,status',
        }[tab];
        const blob = new Blob([headers + '\n'], { type: 'text/csv;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${tab}-template.csv`;
        a.click();
        URL.revokeObjectURL(url);
    };

    const countryName = (id) => countries.find((c) => c.id === id)?.name || '—';

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1><MapPin size={20} style={{ marginRight: 8, verticalAlign: 'middle' }} />Locations</h1>
                    <p>Manage countries, states and cities used across profiles and services.</p>
                </div>
                <div style={{ display: 'flex', gap: 8 }}>
                    <button className="btn btn-sm" onClick={() => { setImportFile(null); setImportOpen(true); }}><Upload size={14} /> Import</button>
                    <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> Add {activeTab.label.slice(0, -1)}</button>
                </div>
            </div>

            <div className="admin-card">
                <div style={{ display: 'flex', gap: 6, padding: '12px 16px', borderBottom: '1px solid rgba(148,163,184,.2)', flexWrap: 'wrap' }}>
                    {TABS.map((t) => {
                        const Icon = t.icon;
                        return (
                            <button
                                key={t.key}
                                type="button"
                                className={`btn btn-sm ${tab === t.key ? 'btn-primary' : ''}`}
                                onClick={() => switchTab(t.key)}
                            >
                                <Icon size={13} /> {t.label}
                            </button>
                        );
                    })}
                </div>

                <div className="card-body" style={{ padding: 0 }}>
                    <div style={{ display: 'flex', gap: 9, padding: '14px 16px', borderBottom: '1px solid rgba(148,163,184,.2)', flexWrap: 'wrap' }}>
                        <div style={{ position: 'relative', flex: '1 1 240px', maxWidth: 360 }}>
                            <Search size={14} style={{ position: 'absolute', left: 10, top: '50%', transform: 'translateY(-50%)', opacity: .5 }} />
                            <input className="form-control" style={{ paddingLeft: 30 }} placeholder={`Search ${activeTab.label.toLowerCase()}…`} value={q} onChange={(e) => setQ(e.target.value)} />
                        </div>
                        {(tab === 'states' || tab === 'cities') && (
                            <select className="form-control" style={{ width: 200 }} value={countryFilter} onChange={(e) => { setCountryFilter(e.target.value); setStateFilter(''); }}>
                                <option value="">All countries</option>
                                {countries.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        )}
                        {tab === 'cities' && (
                            <select className="form-control" style={{ width: 200 }} value={stateFilter} onChange={(e) => setStateFilter(e.target.value)}>
                                <option value="">All states</option>
                                {states.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                            </select>
                        )}
                        {(q !== '' || countryFilter !== '' || stateFilter !== '') && (
                            <button type="button" className="btn btn-sm" onClick={resetFilters}><X size={13} /> Reset</button>
                        )}
                    </div>

                    <div style={{ overflowX: 'auto' }}>
                        <table>
                            {tab === 'countries' && (
                                <>
                                    <thead><tr><th>ID</th><th>Name</th><th>ISO2</th><th>ISO3</th><th>Phone</th><th>Currency</th><th>Status</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        {data.length === 0 ? <tr><td colSpan={8} className="empty">No countries found</td></tr> : data.map((c) => (
                                            <tr key={c.id}>
                                                <td>#{c.id}</td>
                                                <td style={{ fontWeight: 500 }}>{c.emoji ? `${c.emoji} ` : ''}{c.name}</td>
                                                <td>{c.iso2 || '—'}</td>
                                                <td>{c.iso3 || '—'}</td>
                                                <td>{c.phone_code || '—'}</td>
                                                <td>{c.currency || '—'}</td>
                                                <td><span className={`badge ${c.status ? 'badge-success' : 'badge-secondary'}`}>{c.status ? 'Active' : 'Inactive'}</span></td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: 5 }}>
                                                        <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(c)}><Edit2 size={13} /></button>
                                                        <button className="btn-icon btn-icon-danger" onClick={() => remove(c)}><Trash2 size={13} /></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </>
                            )}
                            {tab === 'states' && (
                                <>
                                    <thead><tr><th>ID</th><th>State</th><th>Code</th><th>Country</th><th>Status</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        {data.length === 0 ? <tr><td colSpan={6} className="empty">No states found</td></tr> : data.map((s) => (
                                            <tr key={s.id}>
                                                <td>#{s.id}</td>
                                                <td style={{ fontWeight: 500 }}>{s.name}</td>
                                                <td>{s.state_code || '—'}</td>
                                                <td>{s.country?.name || countryName(s.country_id)}</td>
                                                <td><span className={`badge ${s.status ? 'badge-success' : 'badge-secondary'}`}>{s.status ? 'Active' : 'Inactive'}</span></td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: 5 }}>
                                                        <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(s)}><Edit2 size={13} /></button>
                                                        <button className="btn-icon btn-icon-danger" onClick={() => remove(s)}><Trash2 size={13} /></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </>
                            )}
                            {tab === 'cities' && (
                                <>
                                    <thead><tr><th>ID</th><th>City</th><th>State</th><th>Country</th><th>Status</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        {data.length === 0 ? <tr><td colSpan={6} className="empty">No cities found</td></tr> : data.map((c) => (
                                            <tr key={c.id}>
                                                <td>#{c.id}</td>
                                                <td style={{ fontWeight: 500 }}>{c.name}</td>
                                                <td>{c.state?.name || '—'}</td>
                                                <td>{c.country?.name || countryName(c.country_id)}</td>
                                                <td><span className={`badge ${c.status ? 'badge-success' : 'badge-secondary'}`}>{c.status ? 'Active' : 'Inactive'}</span></td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: 5 }}>
                                                        <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => openEdit(c)}><Edit2 size={13} /></button>
                                                        <button className="btn-icon btn-icon-danger" onClick={() => remove(c)}><Trash2 size={13} /></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </>
                            )}
                        </table>
                    </div>
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            <Modal open={modalOpen} title={`${editing ? 'Edit' : 'Add'} ${activeTab.label.slice(0, -1)}`} onClose={() => setModalOpen(false)}>
                <form className="modal-content" onSubmit={submit}>
                    {tab === 'countries' && (
                        <>
                            <div className="form-group">
                                <label>Name *</label>
                                <input className="form-control" value={form.name || ''} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <div className="form-group">
                                    <label>ISO2</label>
                                    <input className="form-control" maxLength={2} value={form.iso2 || ''} onChange={(e) => setForm({ ...form, iso2: e.target.value })} />
                                </div>
                                <div className="form-group">
                                    <label>ISO3</label>
                                    <input className="form-control" maxLength={3} value={form.iso3 || ''} onChange={(e) => setForm({ ...form, iso3: e.target.value })} />
                                </div>
                                <div className="form-group">
                                    <label>Phone Code</label>
                                    <input className="form-control" value={form.phone_code || ''} onChange={(e) => setForm({ ...form, phone_code: e.target.value })} placeholder="+91" />
                                </div>
                                <div className="form-group">
                                    <label>Currency</label>
                                    <input className="form-control" value={form.currency || ''} onChange={(e) => setForm({ ...form, currency: e.target.value })} placeholder="INR" />
                                </div>
                                <div className="form-group">
                                    <label>Currency Symbol</label>
                                    <input className="form-control" value={form.currency_symbol || ''} onChange={(e) => setForm({ ...form, currency_symbol: e.target.value })} placeholder="₹" />
                                </div>
                                <div className="form-group">
                                    <label>Timezone</label>
                                    <input className="form-control" value={form.timezone || ''} onChange={(e) => setForm({ ...form, timezone: e.target.value })} />
                                </div>
                            </div>
                            <div className="form-group">
                                <label>Emoji / Flag</label>
                                <input className="form-control" value={form.emoji || ''} onChange={(e) => setForm({ ...form, emoji: e.target.value })} placeholder="🇮🇳" />
                            </div>
                        </>
                    )}

                    {(tab === 'states' || tab === 'cities') && (
                        <div className="form-group">
                            <label>Country {tab === 'states' ? '*' : ''}</label>
                            <select className="form-control" value={form.country_id || ''} onChange={(e) => setForm({ ...form, country_id: e.target.value, state_id: tab === 'cities' ? '' : form.state_id })} required={tab === 'states'}>
                                <option value="">Select country</option>
                                {countries.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        </div>
                    )}

                    {tab === 'states' && (
                        <>
                            <div className="form-group">
                                <label>State Name *</label>
                                <input className="form-control" value={form.name || ''} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                            </div>
                            <div className="form-group">
                                <label>State Code</label>
                                <input className="form-control" value={form.state_code || ''} onChange={(e) => setForm({ ...form, state_code: e.target.value })} />
                            </div>
                        </>
                    )}

                    {tab === 'cities' && (
                        <>
                            <div className="form-group">
                                <label>State *</label>
                                <select
                                    className="form-control"
                                    value={form.state_id || ''}
                                    onChange={(e) => setForm({ ...form, state_id: e.target.value })}
                                    required
                                    disabled={!form.country_id}
                                >
                                    <option value="">{form.country_id ? 'Select state' : 'Select country first'}</option>
                                    {states.filter((s) => !form.country_id || String(s.country_id) === String(form.country_id)).map((s) => (
                                        <option key={s.id} value={s.id}>{s.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="form-group">
                                <label>City Name *</label>
                                <input className="form-control" value={form.name || ''} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
                            </div>
                        </>
                    )}

                    <div className="form-group">
                        <label style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                            <input type="checkbox" checked={!!form.status} onChange={(e) => setForm({ ...form, status: e.target.checked })} /> Active
                        </label>
                    </div>

                    <div className="modal-actions">
                        <button type="button" className="btn" onClick={() => setModalOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={saving}>{saving ? 'Saving…' : editing ? 'Update' : 'Save'}</button>
                    </div>
                </form>
            </Modal>

            <Modal open={importOpen} title={`Import ${activeTab.label}`} onClose={() => setImportOpen(false)}>
                <form className="modal-content" onSubmit={runImport}>
                    <div className="form-group">
                        <label>File (.csv or .xlsx)</label>
                        <input type="file" className="form-control" accept=".csv,.txt,.xlsx" onChange={(e) => setImportFile(e.target.files?.[0] || null)} />
                    </div>
                    <div style={{ fontSize: 12.5, color: 'var(--text-muted,#64748b)', lineHeight: 1.6, background: 'rgba(148,163,184,.12)', padding: '10px 12px', borderRadius: 10 }}>
                        <strong>Flexible columns:</strong> only the columns present in your file are used; missing ones are ignored and existing rows are updated (matched by code/name).<br />
                        <strong>Expected columns:</strong> {IMPORT_COLUMNS[tab]}
                        <button type="button" className="btn btn-sm" style={{ marginTop: 8 }} onClick={downloadTemplate}><Download size={13} /> Download template</button>
                    </div>
                    <div className="modal-actions">
                        <button type="button" className="btn" onClick={() => setImportOpen(false)}>Cancel</button>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={importing}>{importing ? 'Importing…' : 'Import'}</button>
                    </div>
                </form>
            </Modal>
        </div>
    );
}

export default Locations;
