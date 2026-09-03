import { useState, useEffect } from 'react';
import api from '../../shared/api';
import Pagination from '../components/Pagination';

function Devices() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const [search, setSearch] = useState('');

    const load = (p, s) => {
        api.get('/admin/devices', { params: { page: p, per_page: 10, search: s || undefined } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page, search); }, [page]); // eslint-disable-line react-hooks/exhaustive-deps

    const searchSubmit = (e) => { e.preventDefault(); setPage(1); load(1, search); };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Login Devices</h1><p>User login devices by IP</p></div>
                <form onSubmit={searchSubmit} style={{ display: 'flex', gap: 6 }}>
                    <input className="search-input" placeholder="Search user..." value={search} onChange={(e) => setSearch(e.target.value)} style={{ width: 200 }} />
                    <button className="btn btn-sm">Search</button>
                </form>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No devices found</div> : (
                        <table>
                            <thead><tr><th>User</th><th>Email</th><th>Device</th><th>IP Address</th><th>User Agent</th><th>Last Activity</th></tr></thead>
                            <tbody>
                                {data.map((d) => (
                                    <tr key={d.id}>
                                        <td style={{ fontWeight: 500 }}>{d.user?.name || d.user?.username || '-'}</td>
                                        <td>{d.user?.email || '-'}</td>
                                        <td>{d.device_name || d.device_type || '-'}</td>
                                        <td>{d.ip_address || '-'}</td>
                                        <td style={{ maxWidth: 260, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{d.user_agent || '-'}</td>
                                        <td>{d.last_activity ? new Date(d.last_activity).toLocaleString() : '-'}</td>
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

export default Devices;
