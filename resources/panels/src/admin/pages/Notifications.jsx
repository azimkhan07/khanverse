import { useState, useEffect } from 'react';
import { Bell, CheckCheck, Trash2 } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';
import { showConfirm } from '../../shared/components/ConfirmDialog';
import Pagination from '../components/Pagination';

function Notifications() {
    const [data, setData] = useState([]);
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({});

    const load = (p) => {
        api.get('/admin/notifications', { params: { page: p, per_page: 10 } }).then((res) => {
            setData(res.data.data || []);
            setPagination(res.data.pagination || {});
        }).catch(() => {});
    };

    useEffect(() => { load(page); }, [page]);

    const markRead = async (n) => {
        try {
            await api.post(`/admin/notifications/${n.id}/read`);
            toastr.success('Notification marked as read');
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Failed to mark as read');
        }
        load(page);
    };

    const markAll = async () => {
        try {
            await api.post('/admin/notifications/read-all');
            toastr.success('All notifications marked as read');
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Failed to update notifications');
        }
        load(page);
    };

    const remove = async (n) => {
        const ok = await showConfirm({ title: 'Please confirm', message: 'Delete this notification?', type: 'danger', confirmText: 'Delete' });
        if (!ok) return;
        try {
            await api.delete(`/admin/notifications/${n.id}`);
            toastr.success('Notification deleted');
        } catch (e) {
            toastr.danger(e.response?.data?.message || 'Failed to delete notification');
        }
        load(page);
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>Notifications</h1><p>{pagination.total ?? 0} total</p></div>
                <button className="btn btn-sm" onClick={markAll}><CheckCheck size={14} /> Mark all read</button>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    {data.length === 0 ? <div className="empty"><Bell size={22} /> No notifications</div> : (
                        <div style={{ display: 'flex', flexDirection: 'column' }}>
                            {data.map((n) => (
                                <div key={n.id} className={`notif-row ${n.read ? '' : 'unread'}`} style={{ display: 'flex', alignItems: 'flex-start', gap: 10, padding: '12px 16px' }}>
                                    <div style={{ flex: 1 }}>
                                        <div style={{ fontWeight: 600, fontSize: 13 }}>{n.title}</div>
                                        <div className="notif-time" style={{ margin: '2px 0' }}>{n.message}</div>
                                        <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{n.time}</div>
                                    </div>
                                    <div style={{ display: 'flex', gap: 6 }}>
                                        {!n.read && <button className="btn-icon" title="Mark read" onClick={() => markRead(n)}><CheckCheck size={14} /></button>}
                                        <button className="btn-icon" title="Delete" style={{ color: '#ef4444' }} onClick={() => remove(n)}><Trash2 size={14} /></button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <Pagination page={pagination.current_page} lastPage={pagination.last_page} total={pagination.total} from={pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0} to={Math.min(pagination.current_page * pagination.per_page, pagination.total || 0)} perPage={pagination.per_page} onPage={setPage} />
        </div>
    );
}

export default Notifications;
