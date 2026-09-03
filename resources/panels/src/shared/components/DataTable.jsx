import { Fragment } from 'react';
import { motion } from 'framer-motion';

function DataTable({ columns, data, renderRow }) {
    return (
        <motion.div
            className="data-table"
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.4, ease: [0.22, 1, 0.36, 1] }}
        >
            <div className="data-table-scroll">
                <table style={{ width: '100%' }}>
                    <thead>
                        <tr>
                            {columns.map((col) => (
                                <th key={col.key}>{col.label}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {data.length === 0 ? (
                            <tr>
                                <td colSpan={columns.length} style={{ textAlign: 'center', padding: '48px', color: 'var(--text-muted)' }}>
                                    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 8 }}>
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" style={{ color: '#c4b5fd' }}>
                                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span style={{ fontWeight: 600, fontSize: 15 }}>No data available</span>
                                        <span style={{ fontSize: 13, color: 'var(--text-light)' }}>Records will appear here once available</span>
                                    </div>
                                </td>
                            </tr>
                        ) : (
                            data.map((row, i) => (
                                <Fragment key={row.id ?? i}>
                                    {renderRow(row, i)}
                                </Fragment>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </motion.div>
    );
}

export default DataTable;