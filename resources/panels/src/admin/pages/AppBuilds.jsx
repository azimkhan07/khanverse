import { useEffect, useState } from "react";
import { Upload, Trash2, Download, Star, ExternalLink, Smartphone, RefreshCw } from "lucide-react";
import api from "../../shared/api";
import { toast } from "react-hot-toast";

function AppBuilds() {
    const [builds, setBuilds] = useState([]);
    const [enabled, setEnabled] = useState(true);
    const [busy, setBusy] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [version, setVersion] = useState("");
    const [file, setFile] = useState(null);

    const load = () => {
        api.get("/admin/app-builds")
            .then((res) => {
                setBuilds(res.data?.builds || []);
                setEnabled(res.data?.enabled ?? true);
            })
            .catch((err) => toast.error(err.response?.data?.message || "Failed to load app builds"));
    };

    useEffect(() => {
        load();
    }, []);

    const handleUpload = async (e) => {
        e.preventDefault();
        if (!file) {
            toast.error("Please choose an .apk file");
            return;
        }
        setUploading(true);
        try {
            const fd = new FormData();
            fd.append("apk_file", file);
            fd.append("version", version.trim() || "1.0");
            await api.post("/admin/app-builds/upload", fd);
            toast.success("APK uploaded successfully");
            setFile(null);
            setVersion("");
            e.target.reset();
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || "Upload failed");
        } finally {
            setUploading(false);
        }
    };

    const setActive = async (id) => {
        try {
            await api.post(`/admin/app-builds/${id}/active`);
            toast.success("Active build updated");
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || "Failed to update active build");
        }
    };

    const remove = async (id) => {
        if (!window.confirm("Delete this APK build?")) return;
        setBusy(true);
        try {
            await api.delete(`/admin/app-builds/${id}`);
            toast.success("Build deleted");
            load();
        } catch (err) {
            toast.error(err.response?.data?.message || "Delete failed");
        } finally {
            setBusy(false);
        }
    };

    const togglePrompt = async () => {
        try {
            await api.post("/admin/app-settings/install-prompt", { enabled: !enabled });
            setEnabled(!enabled);
            toast.success(!enabled ? "Install popup enabled" : "Install popup disabled");
        } catch (err) {
            toast.error(err.response?.data?.message || "Failed to update setting");
        }
    };

    return (
        <div>
            <div className="page-heading">
                <div><h1>App Builds</h1><p>Upload .apk builds and manage the install popup</p></div>
            </div>

            <div className="admin-card">
                <div className="card-body" style={{ padding: 0 }}>
                    <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: 12, padding: 18, borderBottom: "1px solid var(--border-light, #eef2f7)" }}>
                        <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                            <Smartphone size={18} style={{ color: "var(--accent, #4f46e5)" }} />
                            <div>
                                <strong style={{ fontSize: 14 }}>Install Popup (Mobile)</strong>
                                <p style={{ fontSize: 12, color: "var(--text-muted, #64748b)", marginTop: 2 }}>
                                    Shows a bottom install prompt after seller/buyer login on phones &amp; tablets.
                                </p>
                            </div>
                        </div>
                        <button className={`status-toggle ${enabled ? "on" : "off"}`} onClick={togglePrompt}>
                            {enabled ? "Enabled" : "Disabled"}
                        </button>
                    </div>
                </div>
            </div>

            <div className="admin-card" style={{ marginTop: 18 }}>
                <div className="card-header">
                    <h3 style={{ fontSize: 15, fontWeight: 600 }}>Upload New APK</h3>
                </div>
                <div className="card-body">
                    <form onSubmit={handleUpload} style={{ display: "flex", gap: 12, flexWrap: "wrap", alignItems: "flex-end" }}>
                        <div style={{ flex: 1, minWidth: 220 }}>
                            <label style={{ fontSize: 12, color: "var(--text-muted, #64748b)" }}>APK File (.apk)</label>
                            <input
                                type="file"
                                accept=".apk,application/vnd.android.package-archive"
                                className="form-control"
                                style={{ padding: 8 }}
                                onChange={(e) => setFile(e.target.files?.[0] || null)}
                            />
                        </div>
                        <div style={{ flex: 0.4, minWidth: 140 }}>
                            <label style={{ fontSize: 12, color: "var(--text-muted, #64748b)" }}>Version (optional)</label>
                            <input className="form-control" value={version} onChange={(e) => setVersion(e.target.value)} placeholder="1.0" />
                        </div>
                        <button type="submit" className="btn btn-primary btn-sm" disabled={uploading}>
                            {uploading ? <RefreshCw size={13} style={{ animation: "spin 1s linear infinite" }} /> : <Upload size={13} />}
                            {uploading ? " Uploading…" : " Upload APK"}
                        </button>
                    </form>
                </div>
            </div>

            <div className="admin-card" style={{ marginTop: 18 }}>
                <div className="card-body" style={{ padding: 0 }}>
                    <div style={{ padding: "14px 18px", borderBottom: "1px solid var(--border-light, #eef2f7)" }}>
                        <strong style={{ fontSize: 14 }}>Uploaded Builds ({builds.length})</strong>
                    </div>
                    {builds.length === 0 ? (
                        <div className="empty" style={{ padding: "40px 20px", textAlign: "center", color: "var(--text-muted, #64748b)" }}>
                            No APK uploaded yet. Upload one above to let users download &amp; install the app.
                        </div>
                    ) : (
                        <table>
                            <thead>
                                <tr><th>File</th><th>Version</th><th>Size</th><th>Uploaded</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                {builds.map((b) => (
                                    <tr key={b.id}>
                                        <td style={{ fontWeight: 500 }}>
                                            <div style={{ display: "flex", alignItems: "center", gap: 8, maxWidth: 320 }}>
                                                <Star size={13} style={{ color: b.active ? "#f59e0b" : "var(--text-muted, #94a3b8)" }} />
                                                {b.filename}
                                            </div>
                                        </td>
                                        <td>{b.version || "-"}</td>
                                        <td>{b.size || "-"}</td>
                                        <td>{b.uploaded_at ? new Date(b.uploaded_at).toLocaleDateString() : "-"}</td>
                                        <td>
                                            {b.active ? (
                                                <span className="badge badge-success">Active</span>
                                            ) : (
                                                <button className="btn btn-sm" onClick={() => setActive(b.id)}>Make Active</button>
                                            )}
                                        </td>
                                        <td>
                                            <div style={{ display: "flex", gap: 5 }}>
                                                {b.url && (
                                                    <a className="btn-icon" href={b.url} target="_blank" rel="noopener noreferrer" title="Download">
                                                        <Download size={13} />
                                                    </a>
                                                )}
                                                {b.url && (
                                                    <button className="btn-icon" title="Copy link" onClick={() => { navigator.clipboard?.writeText(b.url); toast.success("Link copied"); }}>
                                                        <ExternalLink size={13} />
                                                    </button>
                                                )}
                                                <button className="btn-icon btn-icon-danger" disabled={busy} onClick={() => remove(b.id)} title="Delete">
                                                    <Trash2 size={13} />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </div>
    );
}

export default AppBuilds;