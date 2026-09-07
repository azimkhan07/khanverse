import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function WebsitePages() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/pages', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (p) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete page "${p.title}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/pages/${p.id}`);
            toastr.success('Page deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete page');
        }
        load(page);
    };

    const toggle = async (p) => {
        const target = !p.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set page "${p.title}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/pages/${p.id}/status`);
            if (target) toastr.success('Page activated');
            else toastr.warning('Page deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Pages</h1><p>Manage content pages</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/pages/new')}><Plus size={14} /> Add Page</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={5} className="empty">No pages found</td></tr>
                            ) : (
                                data.map((p) => (
                                    <tr key={p.id}>
                                        <td>#{p.id}</td>
                                        <td style={{ fontWeight: 500 }}>{p.title}</td>
                                        <td>{p.slug}</td>
                                        <td>
                                            <button className={`status-toggle ${p.status ? 'on' : 'off'}`} onClick={() => toggle(p)}>
                                                <Power size={12} /> {p.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/pages/${p.id}`)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(p)}><Trash2 size={13} /></button>
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

export default WebsitePages;