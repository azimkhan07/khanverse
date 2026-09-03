import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Eye, FolderKanban } from 'lucide-react';
import { Link } from 'react-router-dom';
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
    { id: 201, title: 'E-commerce Redesign', seller: { full_name: 'Sara Ali' }, budget: 2500, status: 'in_progress', deadline: '2026-10-01' },
    { id: 202, title: 'Mobile App UI', seller: { full_name: 'Usman Tariq' }, budget: 4000, status: 'open', deadline: '2026-09-20' },
];

const statusBadgeMap = { open: 'badge-primary', in_progress: 'badge-warning', delivered: 'badge-info', completed: 'badge-success', cancelled: 'badge-danger' };

const projectStatusLabels = { open: 'Open', in_progress: 'In Progress', delivered: 'Delivered', completed: 'Completed', cancelled: 'Cancelled' };

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

const rowVariant = {
    hidden: { opacity: 0, y: 12 },
    show: (i) => ({ opacity: 1, y: 0, transition: { duration: 0.35, delay: i * 0.04, ease: [0.22, 1, 0.36, 1] } }),
};

function Projects() {
    const [data, setData] = useState([]);
    const [status, setStatus] = useState('');

    useEffect(() => {
        api.get('/buyer/projects', { params: status ? { status } : {} })
            .then(({ data: res }) => setData(Array.isArray(res.data) ? res.data : []))
            .catch(() => setData(fallbackData));
    }, [status]);

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading page-toolbar" variants={fadeUp} initial="hidden" animate="show">
                <div>
                    <h1><FolderKanban size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />My Projects</h1>
                    <p>Track your projects.</p>
                </div>
                <div className="toolbar-filters">
                    <FilterSelect
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        options={statusOptions}
                    />
                </div>
            </motion.div>

            <DataTable
                columns={[
                    { key: 'id', label: 'ID' },
                    { key: 'title', label: 'Project' },
                    { key: 'seller', label: 'Seller' },
                    { key: 'budget', label: 'Budget' },
                    { key: 'status', label: 'Status' },
                    { key: 'action', label: 'Action' },
                ]}
                data={data}
                renderRow={(row, i) => (
                    <motion.tr key={row.id ?? i} custom={i} variants={rowVariant} initial="hidden" animate="show">
                        <td>#{row.id}</td>
                        <td><Link to={`/projects/${row.id}`} style={{ color: 'var(--accent)', fontWeight: 600 }}>{row.title}</Link></td>
                        <td>{row.seller?.full_name || '-'}</td>
                        <td>â‚¹{Number(row.budget).toLocaleString('en-IN')}</td>
                        <td><span className={`badge ${statusBadgeMap[row.status] || 'badge-info'}`}>{projectStatusLabels[row.status] || row.status?.replace('_', ' ')}</span></td>
                        <td><Link to={`/projects/${row.id}`} className="btn btn-secondary btn-sm"><Eye size={14} /> View</Link></td>
                    </motion.tr>
                )}
            />
        </motion.div>
    );
}

export default Projects;
