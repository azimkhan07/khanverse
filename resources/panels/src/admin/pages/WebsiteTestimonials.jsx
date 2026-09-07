import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function Stars({ n }) {
    return <span style={{ color: '#f59e0b' }}>{'★'.repeat(n)}{'☆'.repeat(5 - n)}</span>;
}

function WebsiteTestimonials() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/testimonials', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (t) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete testimonial?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/testimonials/${t.id}`);
            toastr.success('Testimonial deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete testimonial');
        }
        load(page);
    };

    const toggle = async (t) => {
        const target = !t.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set testimonial to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/testimonials/${t.id}/status`);
            if (target) toastr.success('Testimonial activated');
            else toastr.warning('Testimonial deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Testimonials</h1><p>Manage website testimonials</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/testimonials/new')}><Plus size={14} /> Add Testimonial</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Company</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} className="empty">No testimonials found</td></tr>
                            ) : (
                                data.map((t) => (
                                    <tr key={t.id}>
                                        <td>#{t.id}</td>
                                        <td style={{ fontWeight: 500 }}>
                                            {t.image_url && <img src={t.image_url} alt="" style={{ width: 28, height: 28, borderRadius: '50%', objectFit: 'cover', marginRight: 6, verticalAlign: 'middle' }} />}
                                            {t.name}
                                        </td>
                                        <td>{t.company || '-'}</td>
                                        <td><Stars n={t.rating} /></td>
                                        <td>
                                            <button className={`status-toggle ${t.status ? 'on' : 'off'}`} onClick={() => toggle(t)}>
                                                <Power size={12} /> {t.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/testimonials/${t.id}`)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(t)}><Trash2 size={13} /></button>
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

export default WebsiteTestimonials;