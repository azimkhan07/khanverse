import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Power, Play, Eye } from 'lucide-react';
import { Link } from 'react-router-dom';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

const ROLES = ['admin', 'seller', 'buyer'];

function Tutorials() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [search, setSearch] = useState('');
    const [watch, setWatch] = useState(null);

    const load = (p, s) => {
        api.get('/admin/tutorials', { params: { page: p, per_page: 10, search: s || undefined } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page, search); }, [page]); // eslint-disable-line react-hooks/exhaustive-deps

    const searchSubmit = (e) => { e.preventDefault(); setPage(1); load(1, search); };

    const remove = async (t) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete tutorial "${t.title}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/tutorials/${t.id}`);
            toastr.success('Tutorial deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete tutorial');
        }
        load(page);
    };

    const toggle = async (t) => {
        const target = !t.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set tutorial "${t.title}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/tutorials/${t.id}/status`);
            toastr.success(target ? 'Tutorial activated' : 'Tutorial deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    const embedUrl = (u) => {
        if (!u) return '';
        const yt = u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{6,})/);
        if (yt) return `https://www.youtube.com/embed/${yt[1]}`;
        return u;
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Tutorial Videos</h1><p>Guide users through your marketplace</p></div>
                <div style={{ display: 'flex', gap: 8 }}>
                    <form onSubmit={searchSubmit} style={{ display: 'flex', gap: 6 }}>
                        <input className="search-input" placeholder="Search tutorial..." value={search} onChange={(e) => setSearch(e.target.value)} style={{ width: 200 }} />
                        <button className="btn btn-sm">Search</button>
                    </form>
                    <Link to="/tutorials/new" className="btn btn-primary btn-sm"><Plus size={14} /> Add Tutorial</Link>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No tutorials found</div> : (
                        <table>
                            <thead><tr><th>ID</th><th>Title</th><th>Duration</th><th>Roles</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                {data.map((t) => (
                                    <tr key={t.id}>
                                        <td>#{t.id}</td>
                                        <td style={{ fontWeight: 500 }}>
                                            <button className="link-btn" onClick={() => setWatch(t)} style={{ background: 'none', border: 'none', color: 'var(--accent)', cursor: 'pointer', fontWeight: 500, padding: 0 }}>
                                                <Play size={12} style={{ marginRight: 4, verticalAlign: 'middle' }} />{t.title}
                                            </button>
                                        </td>
                                        <td>{t.duration || '-'}</td>
                                        <td>{(t.roles && t.roles.length ? t.roles : ['all']).map((r) => <span key={r} className="badge badge-info" style={{ marginRight: 4 }}>{r}</span>)}</td>
                                        <td>
                                            <button className={`status-toggle ${t.status ? 'on' : 'off'}`} onClick={() => toggle(t)}>
                                                <Power size={12} /> {t.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => setWatch(t)}><Eye size={13} /></button>
                                                <Link to={`/tutorials/${t.id}`} className="btn-icon" style={{ color: '#10b981' }}><Edit2 size={13} /></Link>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(t)}><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />

            {watch && (
                <div className="modal-overlay" onClick={() => setWatch(null)}>
                    <div className="modal-box" style={{ maxWidth: 760 }} onClick={(e) => e.stopPropagation()}>
                        <div className="modal-header">
                            <h3>{watch.title}</h3>
                            <button className="modal-close" onClick={() => setWatch(null)}>×</button>
                        </div>
                        <iframe
                            src={embedUrl(watch.video_url)}
                            title={watch.title}
                            style={{ width: '100%', aspectRatio: '16/9', border: 0, borderRadius: 8 }}
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen
                        />
                        {watch.description && <p style={{ marginTop: 12, color: 'var(--text-muted)', lineHeight: 1.6 }}>{watch.description}</p>}
                    </div>
                </div>
            )}
        </div>
    );
}

export default Tutorials;
