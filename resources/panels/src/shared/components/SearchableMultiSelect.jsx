import { useEffect, useMemo, useRef, useState } from 'react';
import { Search, X, Check, Sparkles } from 'lucide-react';

/**
 * Linkedin-style searchable tag picker.
 *
 * Renders selected items as removable chips. Typing filters the option list;
 * clicking an option adds it as a chip. Keyboard friendly (Escape closes,
 * Enter/click selects).
 *
 * Props:
 *  options    [{ id, name, category? }]
 *  selected   array of ids
 *  onChange(ids)
 *  placeholder
 *  loading
 *  disabled
 *  emptyText
 */
function SearchableMultiSelect({
    options = [],
    selected = [],
    onChange,
    placeholder = 'Search and select…',
    loading = false,
    disabled = false,
    emptyText = 'No matching services found.',
}) {
    const [query, setQuery] = useState('');
    const [open, setOpen] = useState(false);
    const [focused, setFocused] = useState(false);
    const boxRef = useRef(null);

    const selectedSet = useMemo(() => new Set(selected), [selected]);
    const selectedItems = useMemo(
        () => options.filter((o) => selectedSet.has(o.id)),
        [options, selectedSet]
    );

    const matches = useMemo(() => {
        const q = query.trim().toLowerCase();
        return options.filter(
            (o) => !selectedSet.has(o.id) && (!q || o.name.toLowerCase().includes(q) || (o.category && o.category.toLowerCase().includes(q)))
        );
    }, [options, selectedSet, query]);

    useEffect(() => {
        const onDocClick = (e) => {
            if (boxRef.current && !boxRef.current.contains(e.target)) setOpen(false);
        };
        document.addEventListener('mousedown', onDocClick);
        return () => document.removeEventListener('mousedown', onDocClick);
    }, []);

    const addItem = (item) => {
        if (disabled || selectedSet.has(item.id)) return;
        onChange([...selected, item.id]);
        setQuery('');
    };

    const removeItem = (id) => {
        if (disabled) return;
        onChange(selected.filter((s) => s !== id));
    };

    const selectAllVisible = () => {
        if (disabled) return;
        const ids = matches.map((m) => m.id);
        onChange([...new Set([...selected, ...ids])]);
        setQuery('');
    };

    return (
        <div ref={boxRef} style={{ position: 'relative', width: '100%' }}>
            {/* chips + search field */}
            <div
                className="kvsms-field"
                style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    gap: 6,
                    padding: '8px 10px',
                    border: `1.5px solid ${focused ? 'var(--accent,#4F46E5)' : 'var(--border-color,#E2E8F0)'}`,
                    borderRadius: 12,
                    background: 'var(--bg-input,#fff)',
                    cursor: disabled ? 'not-allowed' : 'text',
                    opacity: disabled ? 0.6 : 1,
                    minHeight: 44,
                }}
                onClick={() => !disabled && boxRef.current?.querySelector('input.kvsms-input')?.focus()}
            >
                {selectedItems.map((item) => (
                    <span
                        key={item.id}
                        title={item.category ? `${item.name} · ${item.category}` : item.name}
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: 5,
                            padding: '3px 6px 3px 10px',
                            borderRadius: 999,
                            background: 'var(--accent-light,rgba(79,70,229,.1))',
                            color: 'var(--accent,#4F46E5)',
                            fontSize: 12.5,
                            fontWeight: 600,
                            whiteSpace: 'nowrap',
                        }}
                    >
                        {item.name}
                        <button
                            type="button"
                            aria-label={`Remove ${item.name}`}
                            onClick={(e) => { e.stopPropagation(); removeItem(item.id); }}
                            style={{
                                border: 'none',
                                background: 'transparent',
                                color: 'inherit',
                                cursor: 'pointer',
                                display: 'inline-flex',
                                padding: 2,
                                borderRadius: 999,
                            }}
                        >
                            <X size={12} />
                        </button>
                    </span>
                ))}
                <input
                    className="kvsms-input"
                    value={query}
                    disabled={disabled}
                    onChange={(e) => { setQuery(e.target.value); setOpen(true); }}
                    onFocus={() => setOpen(true)}
                    onBlur={() => setTimeout(() => setFocused(false), 150)}
                    onKeyDown={(e) => {
                        if (e.key === 'Escape') setOpen(false);
                        if (e.key === 'Enter' && matches[0]) { e.preventDefault(); addItem(matches[0]); }
                        if (e.key === 'Backspace' && !query && selected.length) removeItem(selected[selected.length - 1]);
                    }}
                    placeholder={selectedItems.length ? '' : placeholder}
                    style={{
                        flex: 1,
                        minWidth: 140,
                        border: 'none',
                        outline: 'none',
                        background: 'transparent',
                        fontSize: 14,
                        fontFamily: 'inherit',
                        color: 'var(--text-primary,#0F172A)',
                    }}
                />
                {loading && (
                    <span className="spin" style={{ display: 'inline-flex', alignSelf: 'center', width: 14, height: 14, border: '2px solid var(--accent,#4F46E5)', borderTopColor: 'transparent', borderRadius: '50%', animation: 'spin 0.7s linear infinite' }} />
                )}
            </div>

            {/* dropdown */}
            {open && !disabled && (
                <div
                    style={{
                        position: 'absolute',
                        top: 'calc(100% + 6px)',
                        left: 0,
                        right: 0,
                        zIndex: 50,
                        background: 'var(--bg-card,#fff)',
                        border: '1px solid var(--border-color,#E2E8F0)',
                        borderRadius: 14,
                        boxShadow: '0 18px 40px rgba(2,6,23,.12)',
                        maxHeight: 260,
                        overflowY: 'auto',
                        padding: 6,
                    }}
                >
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 8px', fontSize: 12, color: 'var(--text-muted,#64748B)' }}>
                        <span style={{ display: 'inline-flex', alignItems: 'center', gap: 5 }}>
                            <Search size={12} /> {query ? `Matches for "${query}"` : 'All services'}
                        </span>
                        {matches.length > 1 && (
                            <button
                                type="button"
                                className="kvsms-add-all"
                                onClick={selectAllVisible}
                                style={{
                                    border: 'none',
                                    background: 'transparent',
                                    color: 'var(--accent,#4F46E5)',
                                    fontWeight: 700,
                                    cursor: 'pointer',
                                    fontSize: 12,
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    gap: 4,
                                }}
                            >
                                <Check size={12} /> Add all {matches.length}
                            </button>
                        )}
                    </div>
                    {matches.length === 0 ? (
                        <div style={{ padding: '14px 10px', fontSize: 13, color: 'var(--text-muted,#64748B)', textAlign: 'center' }}>
                            {emptyText}
                        </div>
                    ) : (
                        matches.slice(0, 40).map((item) => (
                            <button
                                key={item.id}
                                type="button"
                                onClick={() => addItem(item)}
                                style={{
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: 8,
                                    width: '100%',
                                    padding: '9px 10px',
                                    border: 'none',
                                    borderRadius: 10,
                                    background: 'transparent',
                                    cursor: 'pointer',
                                    textAlign: 'left',
                                    fontFamily: 'inherit',
                                }}
                                onMouseEnter={(e) => { e.currentTarget.style.background = 'var(--bg-badge,#F1F5F9)'; }}
                                onMouseLeave={(e) => { e.currentTarget.style.background = 'transparent'; }}
                            >
                                <Sparkles size={13} style={{ color: 'var(--accent,#4F46E5)', flexShrink: 0 }} />
                                <span style={{ fontSize: 13.5, fontWeight: 600, color: 'var(--text-primary,#0F172A)' }}>{item.name}</span>
                                {item.category && (
                                    <span style={{ marginLeft: 'auto', fontSize: 11.5, color: 'var(--text-muted,#64748B)' }}>{item.category}</span>
                                )}
                            </button>
                        ))
                    )}
                </div>
            )}
        </div>
    );
}

export default SearchableMultiSelect;