import { useState, useEffect, useCallback } from 'react';
import {
    Plus, Edit2, Trash2, Eye, EyeOff, Power, Save, Send, Mail,
} from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';
import Pagination from '../components/Pagination';
import HtmlEditor from '../../shared/components/HtmlEditor';

const TABS = [
    { id: 'templates', label: 'Email Templates' },
    { id: 'smtp', label: 'SMTP Server' },
];

const TPL_TOKENS = [
    '{app_name}', '{user_name}', '{email}',
    '{customer_name}', '{buyer_name}', '{seller_name}',
    '{order_number}', '{service_title}', '{project_title}',
    '{total}', '{subject}', '{message}',
];

const emptyForm = {
    name: '',
    key: '',
    subject: '',
    description: '',
    content: '',
    logo_url: '',
    is_active: true,
};

const emptySmtp = {
    host: '',
    port: 587,
    encryption: 'tls',
    username: '',
    password: '',
    from_address: '',
    from_name: '',
    enabled: false,
    test_email: '',
};

const TAB_KEY = 'skillnest-email-tab';

function EmailSettings() {
    const [tab, setTab] = useState(() => {
        try { return sessionStorage.getItem(TAB_KEY) || 'templates'; } catch { return 'templates'; }
    });

    const switchTab = (id) => {
        setTab(id);
        try { sessionStorage.setItem(TAB_KEY, id); } catch { /* ignore storage errors */ }
    };

    const [rows, setRows] = useState([]);
    const [meta, setMeta] = useState({});
    const [page, setPage] = useState(1);

    const [modalOpen, setModalOpen] = useState(false);
    const [editing, setEditing] = useState(null);
    const [form, setForm] = useState(emptyForm);
    const [keyTouched, setKeyTouched] = useState(false);
    const [preview, setPreview] = useState(null);
    const [saving, setSaving] = useState(false);

    const [smtp, setSmtp] = useState(emptySmtp);
    const [smtpBusy, setSmtpBusy] = useState(false);
    const [testing, setTesting] = useState(false);
    const [showPass, setShowPass] = useState(false);

    const load = useCallback(() => {
        api.get('/admin/email-templates', { params: { page, per_page: 10 } })
            .then((res) => {
                const d = res.data;
                setRows(d.data || d || []);
                setMeta(d);
            })
            .catch(() => {});
    }, [page]);

    useEffect(() => { load(); }, [load]);

    useEffect(() => {
        api.get('/admin/email-smtp/settings').then(({ data }) => {
            setSmtp((s) => ({ ...data, test_email: s.test_email }));
        }).catch(() => {});
    }, []);

    const openCreate = () => {
        setEditing(null);
        setForm({ ...emptyForm });
        setKeyTouched(false);
        setModalOpen(true);
    };

    const openEdit = (d) => {
        setEditing(d);
        setForm({ ...d });
        setKeyTouched(true);
        setModalOpen(true);
    };

    const slug = (name) => String(name || '')
        .toLowerCase()
        .replace(/[^a-z0-9_]+/g, '_')
        .replace(/^_+|_+$/g, '');

    const onName = (name) => {
        setForm((f) => ({
            ...f,
            name,
            key: (!editing && !keyTouched) ? slug(name) : f.key,
        }));
    };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing) {
                await api.put(`/admin/email-templates/${editing.id}`, form);
                toastr.success('Email template updated');
            } else {
                await api.post('/admin/email-templates', form);
                toastr.success('Email template created');
            }
            setModalOpen(false);
            load();
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save template');
        } finally {
            setSaving(false);
        }
    };

    const toggle = async (d) => {
        const target = !d.is_active;
        const ok = await showConfirm({
            title: 'Please confirm',
            message: `Set template "${d.name}" to ${target ? 'active' : 'inactive'}?`,
            type: 'warning',
            confirmText: target ? 'Activate' : 'Deactivate',
        });
        if (!ok) return;
        try {
            await api.post(`/admin/email-templates/${d.id}/status`);
            if (target) toastr.success('Template activated');
            else toastr.warning('Template deactivated');
            load();
        } catch {
            toastr.danger('Failed to update status');
        }
    };

    const remove = async (d) => {
        const ok = await showConfirm({
            title: 'Please confirm',
            message: `Delete template "${d.name}"?`,
            type: 'danger',
            confirmText: 'Delete',
        });
        if (!ok) return;
        try {
            await api.delete(`/admin/email-templates/${d.id}`);
            toastr.success('Template deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete template');
        }
        load();
    };

    const showPreview = async (d) => {
        try {
            const { data } = await api.get(`/admin/email-templates/${d.id}/preview`);
            setPreview({ html: data.html });
        } catch {
            toastr.danger('Failed to generate preview');
        }
    };

    const saveSmtp = async () => {
        setSmtpBusy(true);
        try {
            await api.put('/admin/email-smtp/settings', smtp);
            toastr.success('SMTP settings saved');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save SMTP settings');
        } finally {
            setSmtpBusy(false);
        }
    };

    const testSmtp = async () => {
        if (!smtp.test_email) {
            toastr.warning('Enter an email address to send the test to');
            return;
        }
        setTesting(true);
        try {
            const { data } = await api.post('/admin/email-smtp/settings/test', smtp);
            toastr.success(data.message || 'Test email sent');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'SMTP connection failed');
        } finally {
            setTesting(false);
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1>Email Settings</h1>
                    <p>Email templates &amp; SMTP server used for sending site emails</p>
                </div>
            </div>

            <div className="seg-tabs">
                {TABS.map((t) => (
                    <button
                        key={t.id}
                        className={`seg-tab ${tab === t.id ? 'active' : ''}`}
                        onClick={() => switchTab(t.id)}
                    >
                        {t.id === 'templates' ? <Mail size={13} /> : <Send size={13} />}
                        {t.label}
                    </button>
                ))}
            </div>

            {tab === 'templates' && (
                <>
                    <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap', alignItems: 'center', margin: '12px 0 14px' }}>
                        <span style={{ fontSize: 11, color: 'var(--text-muted)' }}>Available placeholders:</span>
                        {TPL_TOKENS.map((t) => (
                            <code key={t} style={{ fontSize: 11, background: 'var(--bg-muted)', padding: '3px 7px', borderRadius: 6, color: 'var(--text-secondary)' }}>{t}</code>
                        ))}
                    </div>

                    <div className="admin-card toolbar-card">
                        <div className="toolbar">
                            <div className="filters">
                                <span style={{ fontSize: 12, color: 'var(--text-muted)' }}>
                                    {rows.length} template{rows.length === 1 ? '' : 's'} · subject + message with official logo wrapper
                                </span>
                            </div>
                            <button className="btn btn-primary btn-sm" onClick={openCreate}><Plus size={14} /> New Template</button>
                        </div>
                        <div className="card-body" style={{ padding: 0 }}>
                            <table>
                                <thead>
                                    <tr><th>Name</th><th>Key</th><th>Subject</th><th>Purpose</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    {rows.length === 0 ? (
                                        <tr><td colSpan={6} className="empty">No email templates found</td></tr>
                                    ) : (
                                        rows.map((d) => (
                                            <tr key={d.id}>
                                                <td style={{ fontWeight: 500 }}>{d.name}</td>
                                                <td><code style={{ fontSize: 11, background: 'var(--bg-muted)', padding: '2px 7px', borderRadius: 6 }}>{d.key}</code></td>
                                                <td>{d.subject}</td>
                                                <td style={{ fontSize: 12, color: 'var(--text-muted)' }}>{d.description || '-'}</td>
                                                <td>
                                                    <span className={`badge ${d.is_active ? 'badge-success' : 'badge-danger'}`}>{d.is_active ? 'Active' : 'Inactive'}</span>
                                                </td>
                                                <td>
                                                    <div style={{ display: 'flex', gap: 5 }}>
                                                        <button className="btn-icon" style={{ color: '#2563eb' }} title="Preview (official layout)" onClick={() => showPreview(d)}><Eye size={13} /></button>
                                                        <button className={`btn-icon ${d.is_active ? 'btn-icon-success' : 'btn-icon-danger'}`} title={d.is_active ? 'Deactivate' : 'Activate'} onClick={() => toggle(d)}><Power size={13} /></button>
                                                        <button className="btn-icon" style={{ color: '#7c3aed' }} title="Edit" onClick={() => openEdit(d)}><Edit2 size={13} /></button>
                                                        <button className="btn-icon btn-icon-danger" title="Delete" onClick={() => remove(d)}><Trash2 size={13} /></button>
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

                    <Modal open={modalOpen} title={editing ? 'Edit Email Template' : 'New Email Template'} onClose={() => setModalOpen(false)} width="860px">
                        <form className="modal-content" onSubmit={submit}>
                            <div style={{ display: 'flex', gap: 12 }}>
                                <div className="form-group" style={{ flex: 1.4 }}>
                                    <label>Template Name</label>
                                    <input
                                        className="form-control"
                                        value={form.name}
                                        onChange={(e) => onName(e.target.value)}
                                        placeholder="e.g. Order Created"
                                        required
                                    />
                                </div>
                                <div className="form-group" style={{ flex: 1 }}>
                                    <label>Key (unique, snake_case)</label>
                                    <input
                                        className="form-control"
                                        value={form.key}
                                        onChange={(e) => { setKeyTouched(true); setForm({ ...form, key: e.target.value }); }}
                                        placeholder={slug(form.name) || 'order_created'}
                                        required
                                    />
                                </div>
                            </div>
                            <div style={{ display: 'flex', gap: 12 }}>
                                <div className="form-group" style={{ flex: 1.4 }}>
                                    <label>Subject Line</label>
                                    <input
                                        className="form-control"
                                        value={form.subject}
                                        onChange={(e) => setForm({ ...form, subject: e.target.value })}
                                        placeholder="Your order {order_number} has been confirmed"
                                        required
                                    />
                                </div>
                                <div className="form-group" style={{ flex: 1 }}>
                                    <label>Purpose / When it is sent</label>
                                    <input
                                        className="form-control"
                                        value={form.description || ''}
                                        onChange={(e) => setForm({ ...form, description: e.target.value })}
                                        placeholder="e.g. Sent to the buyer when a new order is created"
                                    />
                                </div>
                            </div>
                            <div className="form-group">
                                <label>Logo URL (shown on the official layout header; leave empty for template name)</label>
                                <input
                                    className="form-control"
                                    value={form.logo_url || ''}
                                    onChange={(e) => setForm({ ...form, logo_url: e.target.value })}
                                    placeholder="https://yourdomain.com/uploads/logo.png"
                                />
                            </div>
                            <div className="form-group">
                                <label>Message Content (HTML)</label>
                                <HtmlEditor
                                    value={form.content || ''}
                                    onChange={(html) => setForm({ ...form, content: html })}
                                    placeholder="Write the email body here. Use {placeholders} for dynamic values."
                                    minHeight={210}
                                    help="The content is wrapped automatically in the official layout (dark header + footer)."
                                />
                            </div>
                            <div className="form-group">
                                <label><input type="checkbox" checked={!!form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active (emails are sent with this template)</label>
                            </div>
                            <div className="modal-actions">
                                <button type="button" className="btn btn-sm" onClick={() => setModalOpen(false)}>Cancel</button>
                                <button type="submit" className="btn btn-primary btn-sm" disabled={saving}>{saving ? 'Saving…' : editing ? 'Update' : 'Save'}</button>
                            </div>
                        </form>
                    </Modal>

                    <Modal open={!!preview} title="Template Preview" onClose={() => setPreview(null)} width="860px">
                        {preview && (
                            <div>
                                <div style={{ marginBottom: 10, fontSize: 12, color: 'var(--text-muted)' }}>
                                    Preview shown with sample values, wrapped in the official email layout.
                                </div>
                                <div style={{ border: '1px solid var(--border)', borderRadius: 8, overflow: 'hidden', background: '#fff' }}>
                                    <iframe title="email-preview" srcDoc={preview.html} style={{ width: '100%', height: 520, border: 0 }} />
                                </div>
                            </div>
                        )}
                    </Modal>
                </>
            )}

            {tab === 'smtp' && (
                <div className="detail-cards">
                    <div className="admin-card stack-form" style={{ maxWidth: 780 }}>
                        <div className="card-head">
                            <h3>SMTP Server</h3>
                            <button
                                className={`status-toggle ${smtp.enabled ? 'on' : 'off'}`}
                                onClick={() => setSmtp({ ...smtp, enabled: !smtp.enabled })}
                                title="Enable / disable SMTP"
                            >
                                {smtp.enabled ? 'ENABLED' : 'DISABLED'}
                            </button>
                        </div>
                        <div className="card-body">
                            <p className="email-hint">
                                {smtp.enabled
                                    ? 'All site emails are sent through this SMTP server.'
                                    : 'SMTP is off — emails fall back to the server default mailer.'}
                            </p>

                            <div className="email-grid g-3">
                                <div className="form-group">
                                    <label>Host</label>
                                    <input className="form-control" value={smtp.host} onChange={(e) => setSmtp({ ...smtp, host: e.target.value })} placeholder="smtp.hostinger.com" required />
                                </div>
                                <div className="form-group">
                                    <label>Port</label>
                                    <input type="number" className="form-control" value={smtp.port} onChange={(e) => setSmtp({ ...smtp, port: Number(e.target.value) || 587 })} required />
                                </div>
                                <div className="form-group">
                                    <label>Encryption</label>
                                    <select className="form-control" value={smtp.encryption || ''} onChange={(e) => setSmtp({ ...smtp, encryption: e.target.value })}>
                                        <option value="tls">TLS (587)</option>
                                        <option value="ssl">SSL (465)</option>
                                        <option value="">None</option>
                                    </select>
                                </div>
                            </div>

                            <div className="email-grid g-2">
                                <div className="form-group">
                                    <label>Username</label>
                                    <input className="form-control" value={smtp.username} onChange={(e) => setSmtp({ ...smtp, username: e.target.value })} placeholder="SMTP login / email" />
                                </div>
                                <div className="form-group">
                                    <label>Password</label>
                                    <div className="pwd-wrap">
                                        <input type={showPass ? 'text' : 'password'} className="form-control" value={smtp.password} onChange={(e) => setSmtp({ ...smtp, password: e.target.value })} placeholder="App / SMTP password" style={{ paddingRight: 34 }} />
                                        <button type="button" className="btn-icon" onClick={() => setShowPass(!showPass)} title={showPass ? 'Hide password' : 'Show password'}>{showPass ? <Eye size={13} /> : <EyeOff size={13} />}</button>
                                    </div>
                                </div>
                            </div>

                            <div className="email-grid g-2">
                                <div className="form-group">
                                    <label>From Email</label>
                                    <input type="email" className="form-control" value={smtp.from_address} onChange={(e) => setSmtp({ ...smtp, from_address: e.target.value })} placeholder="no-reply@yourdomain.com" required />
                                </div>
                                <div className="form-group">
                                    <label>From Name</label>
                                    <input className="form-control" value={smtp.from_name} onChange={(e) => setSmtp({ ...smtp, from_name: e.target.value })} placeholder="SkillNest" />
                                </div>
                            </div>

                            <div className="email-divider" />

                            <div className="email-grid g-2" style={{ alignItems: 'flex-end' }}>
                                <div className="form-group">
                                    <label>Send Test Email To</label>
                                    <input type="email" className="form-control" value={smtp.test_email} onChange={(e) => setSmtp({ ...smtp, test_email: e.target.value })} placeholder="you@example.com" />
                                </div>
                                <div className="email-actions">
                                    <button className="btn btn-sm" onClick={testSmtp} disabled={testing}>
                                        <Send size={13} /> {testing ? 'Testing…' : 'Send Test'}
                                    </button>
                                    <button className="btn btn-primary btn-sm" onClick={saveSmtp} disabled={smtpBusy}>
                                        <Save size={13} /> {smtpBusy ? 'Saving…' : 'Save SMTP'}
                                    </button>
                                </div>
                            </div>

                            <p className="email-help">
                                <strong>Tip:</strong> free Gmail/Workspace accounts work via an <strong>App Password</strong> (host
                                smtp.gmail.com, port 465 SSL or 587 TLS) — good for testing/low volume. For official branded emails use
                                your hosting/cPanel SMTP, Brevo, Mailgun, Zoho (free tier) or AWS SES — enter any provider here.
                            </p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default EmailSettings;