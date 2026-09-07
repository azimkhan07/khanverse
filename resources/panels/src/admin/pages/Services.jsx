import { useState, useEffect } from 'react';
import { Eye } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import api from '../../shared/api';
import { serviceStatusBadge, fmtMoney } from '../helpers';
import Pagination from '../components/Pagination';

function Services() {
    const navigate = useNavigate();
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});

    useEffect(() => {
        api.get('/admin/services', { params: { status, page, per_page: 10 } })
            .then((res) => {
                setData(res.data.data || res.data || []);
                setMeta(res.data);
            })
            .catch(() => {});
    }, [status, page]);

    return (
        <div>
            <div className="page-heading"><div><h1>Services</h1><p>All seller services</p></div></div>

            <div className="toolbar">
                <div className="filters">
                    <select className="form-control" value={status} onChange={(e) => { setStatus(e.target.value); setPage(1); }}>
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Title</th><th>Seller</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={7} className="empty">No services found</td></tr>
                            ) : (
                                data.map((s) => (
                                    <tr key={s.id}>
                                        <td>#{s.id}</td>
                                        <td>{s.title}</td>
                                        <td>{s.seller?.full_name || s.seller?.name || '-'}</td>
                                        <td>{s.category?.name || '-'}</td>
                                        <td>{fmtMoney(s.price)}</td>
                                        <td><span className={`badge ${serviceStatusBadge[s.status] || 'badge-info'}`}>{s.status}</span></td>
                                        <td><button className="btn btn-sm" onClick={() => navigate(`/services/${s.id}`)}><Eye size={13} /> View</button></td>
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

export default Services;
