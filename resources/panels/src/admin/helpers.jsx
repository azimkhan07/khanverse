export const orderStatusBadge = {
    pending: 'badge-warning',
    active: 'badge-primary',
    delivered: 'badge-info',
    completed: 'badge-success',
    cancelled: 'badge-danger',
    disputed: 'badge-danger',
};

export const projectStatusBadge = {
    open: 'badge-primary',
    in_progress: 'badge-warning',
    completed: 'badge-success',
    cancelled: 'badge-danger',
};

export const serviceStatusBadge = {
    active: 'badge-success',
    paused: 'badge-warning',
    draft: 'badge-info',
    inactive: 'badge-danger',
};

export function avatar(name, image) {
    if (image) return <img className="avatar-sm" src={image} alt="" />;
    return <span className="avatar-sm">{((name || 'U').charAt(0) || 'U').toUpperCase()}</span>;
}

export function fmtMoney(n) {
    let num = Number(n || 0);
    return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(num);
}

export function fmtDate(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
}

export function fmtTime(d) {
    if (!d) return '-';
    return new Date(d).toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: 'numeric', minute: '2-digit' });
}
