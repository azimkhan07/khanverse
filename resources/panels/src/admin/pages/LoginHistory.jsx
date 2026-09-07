import { useState, useEffect } from 'react';
import api from '../../shared/api';
import Pagination from '../components/Pagination';

function LoginHistory() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('');

    const load = (p, s, f) => {
        api.get('/admin/login-history', { params: { page: p, per_page: 15, search: s || undefined, is_successful: f || undefined } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page, search, filter); }, [page, filter]); // eslint-disable-line react-hooks/exhaustive-deps

    const searchSubmit = (e) => { e.preventDefault(); setPage(1); load(1, search, filter); };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Login History</h1><p>Track user login and logout activity</p></div>
                <form onSubmit={searchSubmit} style={{ display: 'flex', gap: 6 }}>
                    <select className="search-input" value={filter} onChange={(e) => { setFilter(e.target.value); setPage(1); }} style={{ width: 130 }}>
                        <option value="">All</option>
                        <option value="1">Success</option>
                        <option value="0">Failed</option>
                    </select>
                    <input className="search-input" placeholder="Search email..." value={search} onChange={(e) => setSearch(e.target.value)} style={{ width: 200 }} />
                    <button className="btn btn-sm">Search</button>
                </form>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>User</th><th>Email</th><th>Browser</th><th>Device</th><th>Platform</th><th>IP Address</th><th>Status</th><th>Login Time</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={8} className="empty">No login history found</td></tr>
                            ) : (
                                data.map((d) => (
                                    <tr key={d.id}>
                                        <td style={{ fontWeight: 500 }}>{d.user?.name || d.user?.username || '-'}</td>
                                        <td>{d.user?.email || '-'}</td>
                                        <td>{d.browser || '-'}</td>
                                        <td>{d.device || '-'}</td>
                                        <td>{d.platform || '-'}</td>
                                        <td>{d.ip_address || '-'}</td>
                                        <td><span className={d.is_successful ? 'badge badge-success' : 'badge badge-danger'}>{d.is_successful ? 'Success' : 'Failed'}</span></td>
                                        <td>{d.login_at ? new Date(d.login_at).toLocaleString() : '-'}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />
        </div>
    );
}

export default LoginHistory;
