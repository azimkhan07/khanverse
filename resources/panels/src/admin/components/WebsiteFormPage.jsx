import { useEffect, useState } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { ArrowLeft, Save } from 'lucide-react';
import HtmlEditor from '../../shared/components/HtmlEditor';
import api from '../../shared/api';
import toastr from '../../shared/toastr';

const slugify = (s) => String(s || '')
    .toLowerCase().trim()
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');

const pickSelect = (f, v) => {
    if (v === undefined || v === null) {
        return f.default ?? (f.options?.[0] === undefined ? '' : (Array.isArray(f.options[0]) ? f.options[0][0] : f.options[0]));
    }
    return v;
};

function defaultsFromFields(fields) {
    const d = {};
    fields.forEach((f) => {
        if (f.name === 'slug') return;
        if (f.type === 'toggle') d[f.name] = f.default ?? true;
        else if (f.type === 'select') d[f.name] = pickSelect(f, undefined);
        else if (f.type === 'number') d[f.name] = f.default ?? 0;
        else d[f.name] = f.default ?? '';
    });
    return d;
}

function WebsiteFormPage({ config }) {
    const { id: paramId } = useParams();
    const navigate = useNavigate();
    const isNew = !config.singular && paramId === 'new';
    const [form, setForm] = useState(() => defaultsFromFields(config.fields));
    const [current, setCurrent] = useState(null);
    const [loaded, setLoaded] = useState(config.singular ? false : isNew ? true : false);
    const [files, setFiles] = useState({});
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        let alive = true;
        if (config.singular || !isNew) {
            const url = config.singular ? config.fetchUrl : config.fetchUrl.replace('{id}', paramId);
            api.get(url).then(({ data }) => {
                if (!alive) return;
                const item = config.singular ? data.maintenance : data[config.itemKey];
                setCurrent(item || null);
                const next = defaultsFromFields(config.fields);
                config.fields.forEach((f) => {
                    let v = item?.[f.name];
                    if (v === null || v === undefined) v = f.default ?? (f.type === 'toggle' ? true : '');
                    if (f.type === 'toggle') v = !!v;
                    if (f.type === 'datetime' && v) v = String(v).replace(' ', 'T').slice(0, 16);
                    next[f.name] = v;
                });
                setForm(next);
                setLoaded(true);
            }).catch(() => { if (alive) setLoaded(true); });
        }
        return () => { alive = false; };
    }, [config, paramId, isNew]);

    const set = (name, v) => setForm((f) => ({ ...f, [name]: v }));

    const required = (f) => f.required;

    const submit = async (e) => {
        e.preventDefault();
        setBusy(true);
        try {
            const fd = new FormData();
            config.fields.forEach((f) => {
                if (f.name === 'slug' && f.type === 'slug') return;
                const v = form[f.name];
                if (f.type === 'toggle') { fd.append(f.name, v ? '1' : '0'); return; }
                if (f.type === 'image') { if (files[f.name]) fd.append(f.name, files[f.name]); return; }
                const s = String(v ?? '');
                if (f.omitIfEmpty && s === '') return;
                fd.append(f.name, s);
            });
            if (config.singular) {
                fd.append('_method', 'PUT');
                await api.post(config.fetchUrl, fd);
                toastr.success('Saved');
            } else if (isNew) {
                await api.post(config.createUrl, fd);
                toastr.success(`${config.label} created`);
            } else {
                fd.append('_method', 'PUT');
                await api.post(`${config.createUrl}/${paramId}`, fd);
                toastr.success(`${config.label} updated`);
            }
            navigate(config.listPath);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save');
            setBusy(false);
        }
    };

    if (!loaded) return <div className="empty">Loading…</div>;

    const heading = config.singular ? 'Edit Maintenance' : isNew ? `Add ${config.label}` : `Edit ${config.label}`;

    const renderField = (f) => {
        switch (f.type) {
            case 'slug':
                return <input className="form-control" value={slugify(form.title || '') || form.slug || ''} readOnly style={{ background: 'var(--bg-muted, #f1f5f9)', color: 'var(--text-muted)' }} />;
            case 'textarea':
                return <textarea rows={f.rows || 3} className="form-control" value={form[f.name]} onChange={(e) => set(f.name, e.target.value)} required={required(f)} placeholder={f.placeholder} />;
            case 'html':
                return <HtmlEditor value={form[f.name]} onChange={(v) => set(f.name, v)} placeholder={f.placeholder} minHeight={f.minHeight || 110} help={f.help} />;
            case 'select':
                return (
                    <select className="form-control" value={form[f.name]} onChange={(e) => set(f.name, e.target.value)} required={required(f)}>
                        {(f.placeholder !== undefined) && <option value="">{f.placeholder}</option>}
                        {f.options.map((o) => {
                            const val = Array.isArray(o) ? o[0] : o;
                            const label = Array.isArray(o) ? o[1] : String(o).replace(/_/g, ' ');
                            return <option key={val} value={val}>{label}</option>;
                        })}
                    </select>
                );
            case 'number':
                return <input type="number" className="form-control" value={form[f.name]} onChange={(e) => set(f.name, e.target.value)} required={required(f)} />;
            case 'datetime':
                return <input type="datetime-local" className="form-control" value={form[f.name]} onChange={(e) => set(f.name, e.target.value)} />;
            case 'image':
                return (
                    <>
                        <input type="file" accept="image/*" className="form-control" onChange={(e) => setFiles({ ...files, [f.name]: e.target.files[0] })} />
                        {current?.[f.preview] && !files[f.name] && <img src={current[f.preview]} alt="" className="website-img-preview" />}
                    </>
                );
            default:
                return <input type={f.type === 'text' ? 'text' : f.inputType || 'text'} className="form-control" value={form[f.name]} onChange={(e) => set(f.name, e.target.value)} required={required(f)} placeholder={f.placeholder} />;
        }
    };

    return (
        <div className="website-form-page">
            <div className="page-heading website-form-heading">
                <div><h1>{heading}</h1><p>{config.heading}</p></div>
                <Link to={config.listPath} className="btn btn-sm"><ArrowLeft size={14} /> Back to list</Link>
            </div>

            <div className="admin-card website-form-card">
                <form onSubmit={submit} style={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
                    <div className="website-form-body">
                        <div className="website-form-grid">
                            {config.fields.map((f) => (
                                f.type === 'toggle' ? (
                                    <div key={f.name} className="website-field-full">
                                        <label className="website-toggle">
                                            <input type="checkbox" checked={!!form[f.name]} onChange={(e) => set(f.name, e.target.checked)} />
                                            {f.label}
                                        </label>
                                    </div>
                                ) : (
                                    <div key={f.name} className={`website-field ${f.full ? 'website-field-full' : ''}`}>
                                        <div className="form-group">
                                            <label>{f.label}{required(f) && <span className="website-req">*</span>}</label>
                                            {renderField(f)}
                                            {f.note && <span className="website-field-help">{f.note}</span>}
                                        </div>
                                    </div>
                                )
                            ))}
                        </div>
                    </div>
                    <div className="website-form-actions">
                        <Link to={config.listPath} className="btn btn-sm">Cancel</Link>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={busy}><Save size={13} /> {busy ? 'Saving…' : 'Save'}</button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default WebsiteFormPage;