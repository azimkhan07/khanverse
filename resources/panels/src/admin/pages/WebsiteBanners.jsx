import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function WebsiteBanners() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/banners', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (b) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete banner "${b.title}"?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/banners/${b.id}`);
            toastr.success('Banner deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete banner');
        }
        load(page);
    };

    const toggle = async (b) => {
        const target = !b.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set banner "${b.title}" to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/banners/${b.id}/status`);
            if (target) toastr.success('Banner activated');
            else toastr.warning('Banner deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Banners</h1><p>Manage website banners</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/banners/new')}><Plus size={14} /> Add Banner</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No banners found</div> : (
                        <table>
                            <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Position</th><th>Link</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                {data.map((b) => (
                                    <tr key={b.id}>
                                        <td>#{b.id}</td>
                                        <td>{b.image_url ? <img src={b.image_url} alt={b.title} style={{ width: 60, height: 40, objectFit: 'cover', borderRadius: 6 }} /> : '-'}</td>
                                        <td style={{ fontWeight: 500 }}>{b.title}</td>
                                        <td><span className="badge badge-info">{b.position}</span></td>
                                        <td style={{ maxWidth: 180, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{b.link || '-'}</td>
                                        <td>
                                            <button className={`status-toggle ${b.status ? 'on' : 'off'}`} onClick={() => toggle(b)}>
                                                <Power size={12} /> {b.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/banners/${b.id}`)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(b)}><Trash2 size={13} /></button>
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

export default WebsiteBanners;