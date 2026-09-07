import { useState, useEffect } from 'react';
import { Search, Eye, Power, Ban } from 'lucide-react';
import toastr from '../../shared/toastr';
import { useNavigate } from 'react-router-dom';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import { avatar, fmtTime } from '../helpers';
import Pagination from '../components/Pagination';
import Loading from './Loading';

export default function UsersPage({ title, subtitle, endpoint, detailPath }) {
    const navigate = useNavigate();
    const [data, setData] = useState([]);
    const [query, setQuery] = useState('');
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [loading, setLoading] = useState(true);

    const load = (p, q) => {
        setLoading(true);
        api.get(endpoint, { params: { page: p, per_page: 10, search: q || undefined } })
            .then((res) => { setData(res.data.data || res.data || []); setMeta(res.data); })
            .catch(() => {})
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        const t = setTimeout(() => load(page, query), 300);
        return () => clearTimeout(t);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [page, query]);

    const toggleStatus = async (u) => {
        const target = !u.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set ${u.name} to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/users/${u.id}/status`);
            if (target) toastr.success(`${u.name} activated`);
            else toastr.warning(`${u.name} deactivated`);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page, query);
    };

    const toggleBan = async (u) => {
        const target = !u.is_banned;
        const ok = await showConfirm({ title: 'Please confirm', message: `${target ? 'Ban' : 'Unban'} ${u.name}?`, type: 'danger', confirmText: target ? 'Ban' : 'Unban' });
        if (!ok) return;
        try {
            await api.post(`/admin/users/${u.id}/ban`);
            if (target) toastr.success(`${u.name} banned`);
            else toastr.warning(`${u.name} unbanned`);
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update ban status');
        }
        load(page, query);
    };

    if (loading) return <Loading />;

    return (
        <div>
            <div className="page-heading"><div><h1>{title}</h1><p>{subtitle}</p></div></div>

            <div className="toolbar">
                <div />
                <div className="search-box">
                    <Search size={14} style={{ color: 'var(--text-muted)' }} />
                    <input className='form-control' placeholder="Search name or email…" value={query} onChange={(e) => { setQuery(e.target.value); setPage(1); }} />
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No users found</div> : (
                        <table>
                            <thead><tr><th>User</th><th>Email</th><th>Joined</th><th>Status</th><th>Banned</th><th>Actions</th></tr></thead>
                            <tbody>
                                {data.map((u) => (
                                    <tr key={u.id}>
                                        <td>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                                {avatar(u.name, u.profile_photo_path)}
                                                <span style={{ fontWeight: 500 }}>{u.name}</span>
                                            </div>
                                        </td>
                                        <td>{u.email}</td>
                                        <td>{fmtTime(u.created_at)}</td>
                                        <td>
                                            <button className={`status-toggle ${u.status ? 'on' : 'off'}`} onClick={() => toggleStatus(u)}>
                                                <Power size={12} /> {u.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td><span className={`badge ${u.is_banned ? 'badge-danger' : 'badge-success'}`}>{u.is_banned ? 'Banned' : 'OK'}</span></td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`${detailPath}/${u.id}`)}><Eye size={13} /></button>
                                                <button className={`btn-icon ${u.is_banned ? 'btn-icon-success' : 'btn-icon-danger'}`} onClick={() => toggleBan(u)}><Ban size={13} /></button>
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
        </div>
    );
}
