import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import api from '../../shared/api';
import { avatar, fmtMoney, fmtTime } from '../helpers';

export default function SellerDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [u, setU] = useState(null);

    useEffect(() => {
        api.get(`/admin/users/${id}`).then((res) => setU(res.data)).catch(() => {});
    }, [id]);

    if (!u) return <div className="empty">Loading…</div>;

    return (
        <div>
            <div className="page-heading">
                <div>
                    <button className="btn btn-sm" onClick={() => navigate('/sellers')} style={{ marginBottom: 10 }}>
                        <ArrowLeft size={13} /> Back
                    </button>
                    <h1>{u.name}</h1>
                    <p>Seller #{u.id}</p>
                </div>
                {avatar(u.name, null)}
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Account</h3></div>
                    <div className="card-body">
                        <Row label="Name" value={u.name} />
                        <Row label="Username" value={u.username || '-'} />
                        <Row label="Email" value={u.email || '-'} />
                        <Row label="Phone" value={u.phone || '-'} />
                        <Row label="Role" value={u.role || '-'} />
                        <Row label="Seller Bio" value={u.seller?.bio || '-'} />
                        <Row label="Skills" value={u.seller?.skills || '-'} />
                    </div>
                </div>
                <div className="admin-card">
                    <div className="card-head"><h3>Wallet & Status</h3></div>
                    <div className="card-body">
                        <Row label="Balance" value={fmtMoney(u.wallet?.balance)} />
                        <Row label="Pending" value={fmtMoney(u.wallet?.pending_balance)} />
                        <Row label="Joined" value={fmtTime(u.created_at)} />
                        <Row label="Status" value={u.status ? 'Active' : 'Inactive'} />
                        <Row label="Banned" value={u.is_banned ? 'Yes' : 'No'} />
                    </div>
                </div>
            </div>
        </div>
    );
}

function Row({ label, value }) {
    return (
        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', fontSize: 12.5 }}>
            <span style={{ color: 'var(--text-muted)' }}>{label}</span>
            <span style={{ fontWeight: 500 }}>{value}</span>
        </div>
    );
}
