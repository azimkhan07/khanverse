import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import api from '../../shared/api';
import { projectStatusBadge, fmtMoney, fmtDate } from '../helpers';

function ProjectDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [project, setProject] = useState(null);

    useEffect(() => {
        api.get(`/admin/projects/${id}`).then((res) => setProject(res.data)).catch(() => {});
    }, [id]);

    if (!project) return <div className="empty">Loading…</div>;

    return (
        <div>
            <div className="page-heading">
                <div>
                    <button className="btn btn-sm" onClick={() => navigate('/projects')} style={{ marginBottom: 10 }}>
                        <ArrowLeft size={13} /> Back
                    </button>
                    <h1>{project.title}</h1>
                    <p>Project #{project.id}</p>
                </div>
                <span className={`badge ${projectStatusBadge[project.status] || 'badge-info'}`}>{project.status?.replace('_', ' ')}</span>
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Client</h3></div>
                    <div className="card-body">
                        <Row label="Buyer" value={project.buyer?.full_name || project.buyer?.name || '-'} />
                        <Row label="Seller" value={project.seller?.full_name || project.seller?.name || '-'} />
                    </div>
                </div>
                <div className="admin-card">
                    <div className="card-head"><h3>Details</h3></div>
                    <div className="card-body">
                        <Row label="Budget" value={fmtMoney(project.budget)} />
                        <Row label="Deadline" value={fmtDate(project.deadline)} />
                        <Row label="Created" value={fmtDate(project.created_at)} />
                    </div>
                </div>
            </div>

            <div className="admin-card">
                <div className="card-head"><h3>Description</h3></div>
                <div className="card-body">
                    <p style={{ fontSize: 12.5, lineHeight: 1.7, color: 'var(--text-primary)', margin: 0 }}>
                        {project.description || 'No description.'}
                    </p>
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

export default ProjectDetail;
