import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function WebsiteHomepage() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/homepage-sections', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (s) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete section "${s.title}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/homepage-sections/${s.id}`);
            toastr.success('Section deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete section');
        }
        load(page);
    };

    const toggle = async (s) => {
        const target = !s.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set section "${s.title || s.section_key}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/homepage-sections/${s.id}/status`);
            if (target) toastr.success('Section activated');
            else toastr.warning('Section deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Homepage Sections</h1><p>Manage homepage content sections</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/homepage/new')}><Plus size={14} /> Add Section</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Key</th><th>Title</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No sections found</td></tr>
                            ) : (
                                data.map((s) => (
                                    <tr key={s.id}>
                                        <td>#{s.id}</td>
                                        <td><span className="badge badge-info">{s.section_key}</span></td>
                                        <td style={{ fontWeight: 500 }}>{s.title}</td>
                                        <td>{s.sort_order}</td>
                                        <td>
                                            <button className={`status-toggle ${s.status ? 'on' : 'off'}`} onClick={() => toggle(s)}>
                                                <Power size={12} /> {s.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/homepage/${s.id}`)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(s)}><Trash2 size={13} /></button>
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
        </div>
    );
}

export default WebsiteHomepage;