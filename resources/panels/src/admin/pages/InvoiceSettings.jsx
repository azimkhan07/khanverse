import { useState, useEffect, Children, isValidElement, cloneElement } from 'react';
import { Save } from 'lucide-react';
import toastr from '../../shared/toastr';
import api from '../../shared/api';

const PLACEHOLDERS = [
    ['{prefix}', 'Configured prefix'],
    ['{suffix}', 'Configured suffix'],
    ['{year}', 'Full year e.g. 2026'],
    ['{yy}', 'Two-digit year e.g. 26'],
    ['{month}', 'Two-digit month e.g. 08'],
    ['{seq}', 'Zero-padded sequence e.g. 0001'],
];

function exampleNumber(pattern, prefix, suffix, padding) {
    const vars = {
        prefix, suffix,
        year: new Date().getFullYear(),
        yy: String(new Date().getFullYear()).slice(-2),
        month: String(new Date().getMonth() + 1).padStart(2, '0'),
        seq: String(1).padStart(Number(padding) || 4, '0'),
    };
    let out = pattern || '';
    Object.entries(vars).forEach(([k, v]) => { out = out.split(`{${k}}`).join(String(v)); });
    return out;
}

function composePattern({ prefix, suffix }) {
    let base = prefix ? '{prefix}-{year}-{seq}' : '{year}-{seq}';
    return suffix ? `${base}-{suffix}` : base;
}

function fullWidth(el) {
    if (!isValidElement(el)) return el;
    return cloneElement(el, {
        style: { ...(el.props.style || {}), width: '100%', boxSizing: 'border-box' },
    });
}

function ReadonlyPattern({ value }) {
    return (
        <input
            className="form-control"
            value={value}
            readOnly
            title="Auto-built from the fields below"
            style={{ width: '100%', boxSizing: 'border-box', background: 'var(--bg-muted, #f1f5f9)', color: 'var(--text-muted)' }}
        />
    );
}

function Field({ label, children, flex = 1, minWidth = 150 }) {
    return (
        <div style={{ flex, minWidth, display: 'flex', flexDirection: 'column', gap: 6 }}>
            <label style={{ fontSize: 12, fontWeight: 600, color: 'var(--text-secondary)', textAlign: 'left', marginBottom: 0 }}>{label}</label>
            {Children.map(children, fullWidth)}
        </div>
    );
}

function FieldRow({ children, minWidth = 150 }) {
    return (
        <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', marginTop: 12 }}>
            {Children.map(children, (child) => {
                if (!isValidElement(child)) return child;
                return cloneElement(child, { minWidth });
            })}
        </div>
    );
}

function InvoiceSettings() {
    const [orders, setOrders] = useState({ pattern: 'ORD-{year}-{seq}', prefix: 'ORD', suffix: '', seq_padding: 4 });
    const [invoices, setInvoices] = useState({ pattern: '{prefix}{year}{seq}', prefix: 'INV', suffix: '', seq_padding: 4 });
    const [branding, setBranding] = useState({ platform_fee_pct: 12, company_name: '', logo_text: '', logo_url: '', footer_line: '', about_line: '' });
    const [busy, setBusy] = useState(false);

    useEffect(() => {
        api.get('/admin/invoice-settings/orders').then(({ data }) => setOrders(data)).catch(() => {});
        api.get('/admin/invoice-settings/invoices').then(({ data }) => {
            setInvoices({ pattern: data.pattern, prefix: data.prefix, suffix: data.suffix, seq_padding: data.seq_padding });
            setBranding({
                platform_fee_pct: data.platform_fee_pct,
                company_name: data.company_name,
                logo_text: data.logo_text,
                logo_url: data.logo_url,
                footer_line: data.footer_line,
                about_line: data.about_line,
            });
        }).catch(() => {});
    }, []);

    const saveOrders = async () => {
        setBusy(true);
        try {
            await api.put('/admin/invoice-settings/orders', { ...orders, pattern: composePattern(orders) });
            toastr.success('Order numbering settings saved');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save order settings');
        } finally {
            setBusy(false);
        }
    };

    const saveInvoiceNumbering = async () => {
        setBusy(true);
        try {
            await api.put('/admin/invoice-settings/invoices', { ...invoices, pattern: composePattern(invoices) });
            toastr.success('Invoice numbering saved');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save invoice numbering');
        } finally {
            setBusy(false);
        }
    };

    const saveInvoiceSettings = async () => {
        setBusy(true);
        try {
            await api.put('/admin/invoice-settings/invoices', { ...invoices, pattern: composePattern(invoices), ...branding });
            toastr.success('Invoice settings saved');
        } catch (err) {
            toastr.danger(err.response?.data?.message || 'Failed to save invoice settings');
        } finally {
            setBusy(false);
        }
    };

    const oPlaces = orders.prefix || 'PREFIX';
    const iPlaces = invoices.prefix || 'INV';

    return (
        <div>
            <div className="page-heading">
                <div><h1>Invoice Settings</h1><p>Order &amp; invoice numbering patterns and base invoice branding</p></div>
            </div>

            <div style={{ display: 'flex', gap: 16, flexWrap: 'wrap', marginBottom: 16 }}>
                {PLACEHOLDERS.map(([tok, desc]) => (
                    <span key={tok} style={{ fontSize: 11, color: 'var(--text-muted)', background: 'var(--bg-muted)', padding: '4px 8px', borderRadius: 6 }}><code>{tok}</code> — {desc}</span>
                ))}
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Order Number Format</h3></div>
                    <div className="card-body">
                        <div><label style={{ fontSize: 12, fontWeight: 600, color: 'var(--text-secondary)', textAlign: 'left', marginBottom: 6 }}>Number Pattern</label><ReadonlyPattern value={composePattern(orders)} />
                            <p style={{ fontSize: 11, color: 'var(--text-muted)', margin: '4px 0 0' }}>Auto-built from the fields below. Every new order gets a sequential number in this format.</p>
                        </div>
                        <FieldRow>
                            <Field label="Prefix" flex={1}><input className="form-control" value={orders.prefix} onChange={(e) => setOrders({ ...orders, prefix: e.target.value })} /></Field>
                            <Field label="Suffix" flex={1}><input className="form-control" value={orders.suffix} onChange={(e) => setOrders({ ...orders, suffix: e.target.value })} /></Field>
                            <Field label="Seq Padding" flex={0.6} minWidth={110}><input type="number" min="1" max="12" className="form-control" value={orders.seq_padding} onChange={(e) => setOrders({ ...orders, seq_padding: Number(e.target.value) || 4 })} /></Field>
                        </FieldRow>
                        <div style={{ marginTop: 12, fontSize: 12, color: 'var(--text-muted)' }}>
                            <strong>Example:</strong> <code>{exampleNumber(composePattern(orders), oPlaces, orders.suffix, orders.seq_padding)}</code> — new orders get sequential numbers in this format.
                        </div>
                        <div style={{ marginTop: 12, display: 'flex', justifyContent: 'flex-end' }}>
                            <button className="btn btn-primary btn-sm" onClick={saveOrders} disabled={busy}><Save size={13} /> Save Order Format</button>
                        </div>
                    </div>
                </div>

                <div className="admin-card">
                    <div className="card-head"><h3>Invoice Number Format</h3></div>
                    <div className="card-body">
                        <div><label style={{ fontSize: 12, fontWeight: 600, color: 'var(--text-secondary)', textAlign: 'left', marginBottom: 6 }}>Number Pattern</label><ReadonlyPattern value={composePattern(invoices)} />
                            <p style={{ fontSize: 11, color: 'var(--text-muted)', margin: '4px 0 0' }}>Used as the serial printed below the invoice barcode.</p>
                        </div>
                        <FieldRow>
                            <Field label="Prefix" flex={1}><input className="form-control" value={invoices.prefix} onChange={(e) => setInvoices({ ...invoices, prefix: e.target.value })} /></Field>
                            <Field label="Suffix" flex={1}><input className="form-control" value={invoices.suffix} onChange={(e) => setInvoices({ ...invoices, suffix: e.target.value })} /></Field>
                            <Field label="Seq Padding" flex={0.6} minWidth={110}><input type="number" min="1" max="12" className="form-control" value={invoices.seq_padding} onChange={(e) => setInvoices({ ...invoices, seq_padding: Number(e.target.value) || 4 })} /></Field>
                        </FieldRow>
                        <div style={{ marginTop: 12, fontSize: 12, color: 'var(--text-muted)' }}>
                            <strong>Example:</strong> <code>{exampleNumber(composePattern(invoices), iPlaces, invoices.suffix, invoices.seq_padding)}</code> — used as the serial printed below the invoice barcode.
                        </div>
                        <div style={{ marginTop: 12, display: 'flex', justifyContent: 'flex-end' }}>
                            <button className="btn btn-primary btn-sm" onClick={saveInvoiceNumbering} disabled={busy}><Save size={13} /> Save Invoice Format</button>
                        </div>
                    </div>
                </div>
            </div>

            <div className="detail-cards">
                <div className="admin-card">
                    <div className="card-head"><h3>Base Branding &amp; Fee</h3></div>
                    <div className="card-body">
                        <FieldRow minWidth={180}>
                            <Field label="Platform Fee (%)" flex={0.7}><input type="number" min="0" max="100" step="0.1" className="form-control" value={branding.platform_fee_pct} onChange={(e) => setBranding({ ...branding, platform_fee_pct: e.target.value })} /></Field>
                            <Field label="Company Name" flex={1.3}><input className="form-control" value={branding.company_name} onChange={(e) => setBranding({ ...branding, company_name: e.target.value })} /></Field>
                            <Field label="Logo Text" flex={1}><input className="form-control" value={branding.logo_text} onChange={(e) => setBranding({ ...branding, logo_text: e.target.value })} /></Field>
                            <Field label="Logo URL" flex={1}><input className="form-control" value={branding.logo_url} onChange={(e) => setBranding({ ...branding, logo_url: e.target.value })} /></Field>
                            <Field label="Footer Line" flex={1}><input className="form-control" value={branding.footer_line} onChange={(e) => setBranding({ ...branding, footer_line: e.target.value })} /></Field>
                            <Field label="About Line" flex={1}><input className="form-control" value={branding.about_line} onChange={(e) => setBranding({ ...branding, about_line: e.target.value })} /></Field>
                        </FieldRow>
                        <div style={{ marginTop: 12, display: 'flex', justifyContent: 'flex-end' }}>
                            <button className="btn btn-primary btn-sm" onClick={saveInvoiceSettings} disabled={busy}><Save size={13} /> Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default InvoiceSettings;