const KEY = 'skillnest-compare';

export const COMPARE_MAX = 4;

export const getCompareItems = () => {
    try {
        const raw = localStorage.getItem(KEY);
        const list = raw ? JSON.parse(raw) : [];
        return Array.isArray(list) ? list : [];
    } catch {
        return [];
    }
};

const save = (items) => {
    try {
        localStorage.setItem(KEY, JSON.stringify(items));
    } catch { /* ignore storage errors */ }
    window.dispatchEvent(new Event('skillnest-compare'));
};

export const isInCompare = (id) => {
    return getCompareItems().some((i) => Number(i.id) === Number(id));
};

export const toggleCompare = (item) => {
    const items = getCompareItems();
    const num = Number(item.id);
    const idx = items.findIndex((i) => Number(i.id) === num);

    if (idx >= 0) {
        save(items.filter((_, i) => i !== idx));
        return { added: false, full: false, items: items.filter((_, i) => i !== idx) };
    }

    let next = [...items, {
        id: num,
        title: item.title,
        image: item.image,
        price: item.price,
    }];
    if (next.length > COMPARE_MAX) {
        next = next.slice(next.length - COMPARE_MAX);
        save(next);
        return { added: true, full: true, replaced: true, items: next };
    }
    save(next);
    return { added: true, full: false, items: next };
};

export const removeCompare = (id) => {
    const items = getCompareItems().filter((i) => Number(i.id) !== Number(id));
    save(items);
    return items;
};

export const clearCompare = () => {
    save([]);
};