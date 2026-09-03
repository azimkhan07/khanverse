import { useState, useRef, useEffect } from 'react';
import { Filter, ChevronDown, Check } from 'lucide-react';

function FilterSelect({ value, onChange, options, className = '' }) {
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    const selected = options.find((o) => o.value === value) || options[0];

    useEffect(() => {
        const onDown = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false); };
        document.addEventListener('mousedown', onDown);
        return () => document.removeEventListener('mousedown', onDown);
    }, []);

    const pick = (val) => {
        onChange({ target: { value: val } });
        setOpen(false);
    };

    return (
        <div className={`kv-filter ${className}`} ref={ref}>
            <button type="button" className="kv-filter-trigger" onClick={() => setOpen((o) => !o)}>
                <Filter size={14} className="kv-filter-icon" />
                <span className="kv-filter-label">{selected?.label || 'Filter'}</span>
                <ChevronDown size={14} className={`kv-filter-chevron ${open ? 'open' : ''}`} />
            </button>
            {open && (
                <div className="kv-filter-dropdown">
                    {options.map((opt) => (
                        <button
                            key={opt.value}
                            type="button"
                            className={`kv-filter-option ${opt.value === value ? 'active' : ''}`}
                            onClick={() => pick(opt.value)}
                        >
                            <span>{opt.label}</span>
                            {opt.value === value && <Check size={14} />}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

export default FilterSelect;
