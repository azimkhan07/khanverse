import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Edit2, Trash2, Power } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function WebsiteFaqs() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState({});
    const navigate = useNavigate();

    const load = (p) => {
        api.get('/admin/website/faqs', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []); setMeta(res.data);
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const remove = async (f) => {
        const ok = await showConfirm({ title: 'Please confirm', message: `Delete FAQ?`, type: 'danger', confirmText: 'Delete' }); if (!ok) return;
        try {
            await api.delete(`/admin/website/faqs/${f.id}`);
            toastr.success('FAQ deleted');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to delete FAQ');
        }
        load(page);
    };

    const toggle = async (f) => {
        const target = !f.status;
        const ok = await showConfirm({ title: 'Please confirm', message: `Set this FAQ to ${target ? 'active' : 'inactive'}?`, type: 'warning', confirmText: target ? 'Activate' : 'Deactivate' });
        if (!ok) return;
        try {
            await api.post(`/admin/website/faqs/${f.id}/status`);
            if (target) toastr.success('FAQ activated');
            else toastr.warning('FAQ deactivated');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to update status');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>FAQs</h1><p>Manage frequently asked questions</p></div>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/website/faqs/new')}><Plus size={14} /> Add FAQ</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty">No FAQs found</div> : (
                        <table>
                            <thead><tr><th>ID</th><th>Question</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                {data.map((f) => (
                                    <tr key={f.id}>
                                        <td>#{f.id}</td>
                                        <td style={{ fontWeight: 500 }}>{f.question}</td>
                                        <td>{f.sort_order}</td>
                                        <td>
                                            <button className={`status-toggle ${f.status ? 'on' : 'off'}`} onClick={() => toggle(f)}>
                                                <Power size={12} /> {f.status ? 'Active' : 'Inactive'}
                                            </button>
                                        </td>
                                        <td>
                                            <div style={{ display: 'flex', gap: 5 }}>
                                                <button className="btn-icon" style={{ color: '#2563eb' }} onClick={() => navigate(`/website/faqs/${f.id}`)}><Edit2 size={13} /></button>
                                                <button className="btn-icon btn-icon-danger" onClick={() => remove(f)}><Trash2 size={13} /></button>
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

export default WebsiteFaqs;