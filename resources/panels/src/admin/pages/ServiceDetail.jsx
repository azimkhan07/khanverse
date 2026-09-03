import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import api from '../../shared/api';
import { serviceStatusBadge, fmtMoney } from '../helpers';

function ServiceDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [service, setService] = useState(null);

    useEffect(() => {
        api.get(`/admin/services/${id}`).then((res) => {
            const s = res.data;
            setService({
                ...s,
                thumbnail: s.thumbnail ? (s.thumbnail.startsWith('http') ? s.thumbnail : `/storage/${s.thumbnail}`) : null,
                images: (s.images || []).map((img) => ({
                    ...img,
                    url: img.image && !img.image.startsWith('http') ? `/storage/${img.image}` : img.image,
                })),
            });
        }).catch(() => {});
    }, [id]);

    if (!service) return <div className="empty">Loading…</div>;

    return (
        <div>
            <div className="page-heading">
                <div>
                    <button className="btn btn-sm" onClick={() => navigate('/services')} style={{ marginBottom: 10 }}>
                        <ArrowLeft size={13} /> Back
                    </button>
                    <h1>{service.title}</h1>
                    <p>Service #{service.id}</p>
                </div>
                <span className={`badge ${serviceStatusBadge[service.status] || 'badge-info'}`}>{service.status}</span>
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Details</h3></div>
                    <div className="card-body">
                        <Row label="Seller" value={service.seller?.full_name || service.seller?.name || '-'} />
                        <Row label="Category" value={service.category?.name || '-'} />
                        <Row label="Price" value={fmtMoney(service.price)} />
                        <Row label="Delivery" value={`${service.delivery_days} Days`} />
                        <Row label="Revisions" value={service.revisions || 0} />
                    </div>
                </div>
                <div className="admin-card">
                    <div className="card-head"><h3>Thumbnail</h3></div>
                    <div className="card-body">
                        {service.thumbnail
                            ? <img src={service.thumbnail} alt="" style={{ width: '100%', height: 160, objectFit: 'cover', borderRadius: 8 }} />
                            : <div className="empty">No thumbnail</div>}
                    </div>
                </div>
            </div>

            <div className="admin-card" style={{ marginBottom: 14 }}>
                <div className="card-head"><h3>Description</h3></div>
                <div className="card-body">
                    <p style={{ fontSize: 12.5, lineHeight: 1.7, color: 'var(--text-primary)', margin: 0 }}>{service.description || 'No description.'}</p>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-head"><h3>Gallery ({service.images?.length || 0})</h3></div>
                <div className="card-body">
                    {(!service.images || service.images.length === 0) ? (
                        <div className="empty">No gallery images</div>
                    ) : (
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(110px, 1fr))', gap: 10 }}>
                            {service.images.map((img) => (
                                <img key={img.id} src={img.url} alt="" style={{ width: '100%', height: 90, objectFit: 'cover', borderRadius: 8 }} />
                            ))}
                        </div>
                    )}
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

export default ServiceDetail;
