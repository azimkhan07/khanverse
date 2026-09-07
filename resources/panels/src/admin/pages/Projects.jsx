import { useState, useEffect } from 'react';
import { Eye } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import api from '../../shared/api';
import { projectStatusBadge, fmtMoney } from '../helpers';
import Pagination from '../components/Pagination';

function Projects() {
    const navigate = useNavigate();
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});

    useEffect(() => {
        api.get('/admin/projects', { params: { status, page, per_page: 10 } })
            .then((res) => {
                setData(res.data.data || res.data || []);
                setMeta(res.data);
            })
            .catch(() => {});
    }, [status, page]);

    return (
        <div>
            <div className="page-heading"><div><h1>Projects</h1><p>Manage platform projects</p></div></div>

            <div className="toolbar">
                <div className="filters">
                    <select className="form-control" value={status} onChange={(e) => { setStatus(e.target.value); setPage(1); }}>
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Title</th><th>Buyer</th><th>Seller</th><th>Budget</th><th>Status</th><th>Deadline</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={8} className="empty">No projects found</td></tr>
                            ) : (
                                data.map((p) => (
                                    <tr key={p.id}>
                                        <td>#{p.id}</td>
                                        <td>{p.title}</td>
                                        <td>{p.buyer?.full_name || p.buyer?.name || '-'}</td>
                                        <td>{p.seller?.full_name || p.seller?.name || '-'}</td>
                                        <td>{fmtMoney(p.budget)}</td>
                                        <td><span className={`badge ${projectStatusBadge[p.status] || 'badge-info'}`}>{p.status?.replace('_', ' ')}</span></td>
                                        <td>{p.deadline ? new Date(p.deadline).toLocaleDateString() : '-'}</td>
                                        <td><button className="btn btn-sm" onClick={() => navigate(`/projects/${p.id}`)}><Eye size={13} /> View</button></td>
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

export default Projects;
