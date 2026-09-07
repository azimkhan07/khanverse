import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { AlertTriangle } from 'lucide-react';
import api from '../../shared/api';
import Pagination from '../components/Pagination';

function Suspicious() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});

    const load = (p) => {
        api.get('/admin/suspicious', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    return (
        <div>
            <div className="page-heading">
                <div><h1>Suspicious Users</h1><p>Users with multiple accounts on same IP</p></div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Same IP Accounts</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No suspicious users found</td></tr>
                            ) : (
                                data.map((u) => (
                                    <tr key={u.id}>
                                        <td style={{ fontWeight: 500 }}>{u.name || u.username}</td>
                                        <td>{u.email}</td>
                                        <td><span className="badge badge-warning">{u.role}</span></td>
                                        <td>
                                            <span className="badge badge-danger">
                                                <AlertTriangle size={12} /> {u.same_ip_accounts || 0} accounts
                                            </span>
                                        </td>
                                        <td><span className={`badge ${u.status === 'active' ? 'badge-success' : 'badge-danger'}`}>{u.status}</span></td>
                                        <td>
                                            <Link className="btn btn-sm" to={u.role === 'buyer' ? `/buyers/${u.id}` : `/sellers/${u.id}`}>View</Link>
                                        </td>
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

export default Suspicious;
