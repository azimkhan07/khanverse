import { useState, useEffect } from 'react';
import { Plus, Save, Trash2 } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Modal from '../components/Modal';

const GROUPS = ['admin', 'seller', 'buyer', 'frontend', 'auth'];
const TYPES = ['text', 'textarea', 'number', 'boolean', 'image'];

function SettingsPage() {
    const [groups, setGroups] = useState([]);
    const [settings, setSettings] = useState({});
    const [modalOpen, setModalOpen] = useState(false);
    const [saving, setSaving] = useState(false);
    const [form, setForm] = useState({ group: 'auth', key: '', value: '', type: 'text' });

    const load = () => {
        api.get('/admin/settings').then((res) => {
            const list = Array.isArray(res.data) ? res.data : [];
            const byGroup = {};
            const values = {};
            list.forEach((s) => {
                (byGroup[s.group] = byGroup[s.group] || []).push(s);
                values[s.id] = s.value;
            });
            setGroups(Object.entries(byGroup));
            setSettings(values);
        }).catch(() => {});
    };

    useEffect(() => { load(); }, []);

    const save = async () => {
        try {
            await api.put('/admin/settings', { settings: Object.entries(settings).map(([id, value]) => ({ id: Number(id), value })) });
            toastr.success('Settings saved');
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Failed to save settings');
        }
    };

    const addSetting = async () => {
        if (!form.key.trim()) { toastr.danger('Key is required'); return; }
        setSaving(true);
        try {
            await api.post('/admin/settings', { ...form, key: form.key.trim() });
            toastr.success('Setting added');
            setModalOpen(false);
            setForm({ group: 'auth', key: '', value: '', type: 'text' });
            load();
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Failed to add setting');
        } finally {
            setSaving(false);
        }
    };

    const removeSetting = (row) => {
        showConfirm(`Delete setting "${row.key}"?`, () => {
            api.delete(`/admin/settings/${row.id}`).then(() => {
                toastr.success('Setting deleted');
                load();
            }).catch((e) => toastr.danger(e.response?.data?.message || 'Failed to delete setting'));
        });
    };

    const renderControl = (s) => {
        if (s.type === 'boolean') {
            return (
                <select className="form-control" value={settings[s.id] || '0'} onChange={(e) => setSettings({ ...settings, [s.id]: e.target.value })}>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            );
        }
        if (s.type === 'textarea') {
            return (
                <textarea
                    className="form-control"
                    rows={2}
                    style={{ width: '100%', minHeight: 46, resize: 'vertical' }}
                    value={settings[s.id] ?? ''}
                    onChange={(e) => setSettings({ ...settings, [s.id]: e.target.value })}
                />
            );
        }
        return (
            <input
                className="form-control"
                type={s.type === 'number' ? 'number' : 'text'}
                style={{ width: 240 }}
                value={settings[s.id] ?? ''}
                onChange={(e) => setSettings({ ...settings, [s.id]: e.target.value })}
            />
        );
    };

    return (
        <div>
            <div className="page-heading">
                <div>
                    <h1>Settings</h1>
                    <p>Platform &amp; auth page configuration</p>
                </div>
                <button className="btn btn-primary btn-sm" onClick={() => setModalOpen(true)}>
                    <Plus size={14} /> Add Setting
                </button>
            </div>

            <div className="admin-grid" style={{ gridTemplateColumns: '1fr', gap: 12 }}>
                {groups.length === 0 ? (
                    <div className="admin-card"><div className="empty">No settings found</div></div>
                ) : groups.map(([group, items]) => (
                    <div className="admin-card" key={group}>
                        <div className="card-head">
                            <h3 style={{ textTransform: 'capitalize' }}>
                                {group === 'auth' ? 'Auth Pages' : group}
                            </h3>
                            <span style={{ fontSize: 11, color: 'var(--text-muted)' }}>{items.length} keys</span>
                        </div>
                        <div className="card-body">
                            {items.map((s) => (
                                <div key={s.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: 12, padding: '7px 0', borderBottom: '1px solid var(--border-color, #E5E7EB)' }}>
                                    <div style={{ minWidth: 140, flex: '0 0 200px' }}>
                                        <span style={{ fontSize: 12.5, wordBreak: 'break-all', display: 'block' }}>{s.key}</span>
                                        <span style={{ fontSize: 10.5, color: 'var(--text-muted)', textTransform: 'uppercase' }}>{s.type}</span>
                                    </div>
                                    <div style={{ flex: 1 }}>{renderControl(s)}</div>
                                    <button
                                        className="btn-icon"
                                        title="Delete setting"
                                        style={{ color: '#ef4444', flex: '0 0 auto' }}
                                        onClick={() => removeSetting(s)}
                                    >
                                        <Trash2 size={14} />
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                ))}
            </div>

            {groups.length > 0 && (
                <div style={{ marginTop: 14 }}>
                    <button className="btn btn-primary btn-sm" onClick={save}><Save size={14} /> Save Settings</button>
                </div>
            )}

            <Modal open={modalOpen} title="Add Setting" onClose={() => setModalOpen(false)} width="460px">
                <div className="form-group">
                    <label>Group</label>
                    <select className="form-control" value={form.group} onChange={(e) => setForm({ ...form, group: e.target.value })}>
                        {GROUPS.map((g) => <option key={g} value={g}>{g}</option>)}
                    </select>
                </div>
                <div className="form-group">
                    <label>Key</label>
                    <input className="form-control" value={form.key} onChange={(e) => setForm({ ...form, key: e.target.value })} placeholder="e.g. login.heading" />
                </div>
                <div className="form-group">
                    <label>Type</label>
                    <select className="form-control" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
                        {TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
                    </select>
                </div>
                <div className="form-group">
                    <label>Value</label>
                    {form.type === 'textarea' ? (
                        <textarea className="form-control" rows={3} style={{ resize: 'vertical' }} value={form.value} onChange={(e) => setForm({ ...form, value: e.target.value })} />
                    ) : (
                        <input type={form.type === 'number' ? 'number' : 'text'} value={form.value} onChange={(e) => setForm({ ...form, value: e.target.value })} />
                    )}
                </div>
                <div className="modal-actions">
                    <button type="button" className="btn" onClick={() => setModalOpen(false)}>Cancel</button>
                    <button type="button" className="btn btn-primary" disabled={saving} onClick={addSetting}>
                        <Plus size={14} /> {saving ? 'Adding...' : 'Add Setting'}
                    </button>
                </div>
            </Modal>
        </div>
    );
}

export default SettingsPage;