import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function WebsiteSeo() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/seo', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (s) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete SEO setting for "${s.page_key}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/seo/${s.id}`);
            toastr.success('SEO setting deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete SEO setting');
        }
        load(page);
    };

    const toggle = async (s) => {
        const target = !s.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set SEO setting to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/seo/${s.id}/status`);
            if (target) toastr.success('SEO setting activated');
            else toastr.warning('SEO setting deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>SEO Settings</h1><p>Manage per-page SEO meta data</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/seo/new')}><Plus size={14} /> Add SEO Setting</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Page</th><th>Meta Title</th><th>Robots</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No SEO settings found</td></tr>
                            ) : (
                                data.map((s) => (
                                    <tr key={s.id}>
                                        <td>#{s.id}</td>
                                        <td style={{ fontWeight: 500 }}>{s.page_key}</td>
                                        <td style={{ maxWidth: 220, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{s.meta_title || '-'}</td>
                                        <td><span className="badge badge-info">{s.robots || '-'}</span></td>
                                        <td>
                                            <button className={`status-toggle ${s.status ? 'on' : 'off'}`} onClick={() => toggle(s)}>
                                                <Power size={12} /> {s.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/seo/${s.id}`)}><Edit2 size={13} /></button>
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

export default WebsiteSeo;