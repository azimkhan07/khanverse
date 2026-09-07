export default function Loading({ text = 'Loading...' }) {
    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', minHeight: '30vh', gap: 12 }}>
            <div className="spinner" style={{
                width: 32, height: 32, border: '3px solid var(--border-color, #e5e7eb)',
                borderTopColor: 'var(--accent, #6366f1)', borderRadius: '50%',
                animation: 'spin .6s linear infinite'
            }} />
            <span style={{ fontSize: 13, color: 'var(--text-muted, #64748b)' }}>{text}</span>
            <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
        </div>
    );
}
