import { useState, useEffect } from 'react';
import api from '../../shared/api';
import Pagination from '../components/Pagination';

function Roles() {
    const [roles, setRoles] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});

    useEffect(() => {
        api.get('/admin/roles', { params: { page, per_page: 10 } }).then((res) => {
            setRoles(Array.isArray(res.data.data) ? res.data.data : (Array.isArray(res.data) ? res.data : []));
            setMeta(res.data);
        }).catch(() => {});
    }, [page]);

    return (
        <div>
            <div className="page-heading"><div><h1>Roles</h1><p>Platform roles & user counts</p></div></div>

            <div className="admin-card">
                <div className="card-body">
                    {roles.length === 0 ? <div className="empty">No roles found</div> : (
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 12 }}>
                            {roles.map((r) => (
                                <div key={r.id} style={{ border: '1px solid var(--border-color, #E5E7EB)', borderRadius: 10, padding: 14 }}>
                                    <div style={{ fontWeight: 600, fontSize: 14 }}>{r.name}</div>
                                    <div style={{ fontSize: 12, color: 'var(--text-muted)' }}>{r.slug}</div>
                                    <div style={{ marginTop: 8, fontSize: 22, fontWeight: 600 }}>{r.users_count ?? 0}</div>
                                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>users</div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <Pagination page={meta.current_page} lastPage={meta.last_page} total={meta.total} from={meta.from} to={meta.to} perPage={meta.per_page} onPage={setPage} />
        </div>
    );
}

export default Roles;
