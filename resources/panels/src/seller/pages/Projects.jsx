import { useState, useEffect } from 'react';
import { Eye, FolderKanban, Search } from 'lucide-react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import DataTable from '../../shared/components/DataTable';
import FilterSelect from '../../shared/components/FilterSelect';
import api from '../../shared/api';

const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'delivered', label: 'Delivered' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const fallbackData = [
    { id: 201, title: 'E-commerce Redesign', buyer: { full_name: 'Alice Johnson' }, budget: 2500, status: 'in_progress', deadline: '2026-10-01' },
    { id: 202, title: 'Mobile App Prototype', buyer: { full_name: 'Bob Smith' }, budget: 4000, status: 'open', deadline: '2026-09-20' },
    { id: 203, title: 'Brand Identity Pack', buyer: { full_name: 'Carol White' }, budget: 1200, status: 'completed', deadline: '2026-08-15' },
];

const statusBadgeMap = { open: 'badge-primary', in_progress: 'badge-warning', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };

const projectStatusLabels = { open: 'Open', in_progress: 'In Progress', delivered: 'Delivered', completed: 'Completed', cancelled: 'Cancelled' };

const container = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const item = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

function Projects() {
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');

    useEffect(() => {
        api.get('/seller/projects', { params: status ? { status } : {} })
            .then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, [status]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading page-toolbar"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <div>
                    <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <FolderKanban size={26} style={{ color: 'var(--accent)' }} />
                        Projects
                    </h1>
                    <p>Manage your ongoing and completed projects.</p>
                </div>
                <div className="toolbar-filters">
                    <FilterSelect
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        options={statusOptions}
                    />
                </div>
            </motion.div>

            {data.length === 0 ? (
                <motion.div
                    className="empty-state"
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    style={{ padding: '60px 20px', textAlign: 'center' }}
                >
                    <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--accent-light, #eef2ff)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' }}>
                        <Search size={28} style={{ color: 'var(--accent)' }} />
                    </div>
                    <h3 style={{ marginBottom: 6 }}>No projects found</h3>
                    <p style={{ color: 'var(--text-muted)' }}>Projects will appear here once you have active work.</p>
                </motion.div>
            ) : (
                <motion.div variants={container} initial="hidden" animate="show">
                    <motion.div variants={item}>
                        <DataTable
                            columns={[
                                { key: 'id', label: 'ID' },
                                { key: 'title', label: 'Project' },
                                { key: 'budget', label: 'Budget' },
                                { key: 'status', label: 'Status' },
                                { key: 'deadline', label: 'Deadline' },
                                { key: 'action', label: 'Action' },
                            ]}
                            data={data}
                            renderRow={(row, i) => (
                                <motion.tr
                                    key={row.id ?? i}
                                    initial={{ opacity: 0, x: -12 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    transition={{ duration: 0.35, delay: i * 0.05, ease: [0.22, 1, 0.36, 1] }}
                                >
                                    <td>#{row.id}</td>
                                    <td><Link to={`/projects/${row.id}`} style={{ color: 'var(--accent)', fontWeight: 600 }}>{row.title}</Link></td>
                                    <td>₹{Number(row.budget).toLocaleString('en-IN')}</td>
                                    <td><span className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}>{projectStatusLabels[row.status] || row.status?.replace('_', ' ')}</span></td>
                                    <td>{row.deadline ? new Date(row.deadline).toLocaleDateString() : '-'}</td>
                                    <td>
                                        <Link to={`/projects/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link>
                                    </td>
                                </motion.tr>
                            )}
                        />
                    </motion.div>
                </motion.div>
            )}
        </motion.div>
    );
}

export default Projects;
