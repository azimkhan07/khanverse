export default function Pagination({ page = 1, lastPage = 1, onPage, total, from = 0, to = 0, perPage = 10 }) {
    if (!lastPage || lastPage <= 1) return null;

    const winStart = Math.max(1, Math.min(page - 4, lastPage - 9));
    const winEnd = Math.min(lastPage, winStart + 9);
    const window = [];
    for (let i = winStart; i <= winEnd; i++) window.push(i);

    return (
        <div className="pagination">
            {total != null && (
                <span className="pagination-info">
                    Showing {from || 0}–{to || 0} of {total} ({perPage} / page)
                </span>
            )}
            <div style={{ display: 'flex', gap: 5, flexWrap: 'wrap', alignContent: 'center' }}>
                <button disabled={page <= 1} onClick={() => onPage(page - 1)}>‹</button>
                {winStart > 1 && <button onClick={() => onPage(1)}>1</button>}
                {winStart > 2 && <span className="pagination-ellipsis">…</span>}
                {window.map((p) => (
                    <button key={p} className={p === page ? 'active' : ''} onClick={() => onPage(p)}>{p}</button>
                ))}
                {winEnd < lastPage - 1 && <span className="pagination-ellipsis">…</span>}
                {winEnd < lastPage && <button onClick={() => onPage(lastPage)}>{lastPage}</button>}
                <button disabled={page >= lastPage} onClick={() => onPage(page + 1)}>›</button>
            </div>
        </div>
    );
}