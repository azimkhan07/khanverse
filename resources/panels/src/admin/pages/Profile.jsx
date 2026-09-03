import { useState, useEffect } from 'react';
import { Camera, Save, User } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

function Profile({ user }) {
    const [form, setForm] = useState({ full_name: user?.name || 'Admin', email: user?.email || '', avatar: user?.avatar || null });
    const [message, setMessage] = useState(null);
    const [saving, setSaving] = useState(false);
    const [avatarFile, setAvatarFile] = useState(null);

    useEffect(() => {
        api.get('/admin/profile').then(({ data }) => setForm((p) => ({ ...p, ...data })))
            .catch(() => {});
    }, []);

    const handleChange = (e) => { const { name, value } = e.target; setForm((p) => ({ ...p, [name]: value })); };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setMessage(null);
        const fd = new FormData();
        fd.append('full_name', form.full_name || '');
        fd.append('email', form.email || '');
        if (avatarFile) fd.append('avatar', avatarFile);
        try {
            const res = await api.post('/admin/profile/update', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            setForm((p) => ({ ...p, ...res.data.profile }));
            setMessage({ type: 'success', text: res.data.message || 'Profile updated.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Update failed.' });
        } finally {
            setSaving(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}>
            <motion.div
                className="page-heading"
                initial={{ opacity: 0, y: -16 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5, ease: [0.22, 1, 0.36, 1] }}
            >
                <h1 style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <User size={24} style={{ color: 'var(--accent)' }} />
                    My Profile
                </h1>
                <p>Manage your admin account details.</p>
            </motion.div>

            {message && (
                <motion.div
                    className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`}
                    initial={{ opacity: 0, y: -10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.35 }}
                >
                    {message.text}
                </motion.div>
            )}

            <motion.div className="settings-wrap" variants={stagger} initial="hidden" animate="show">
                <motion.div className="profile-cover" variants={fadeUp}>
                    <motion.img
                        className="profile-avatar"
                        src={form.avatar || (avatarFile ? URL.createObjectURL(avatarFile) : `https://ui-avatars.com/api/?name=${encodeURIComponent(form.full_name || 'A')}&background=6d28d9&color=fff`)}
                        alt=""
                        whileHover={{ scale: 1.05 }}
                        transition={{ duration: 0.3 }}
                    />
                    <label className="btn btn-secondary btn-sm" style={{ cursor: 'pointer', alignSelf: 'flex-end' }}>
                        <Camera size={14} /> Change Photo
                        <input type="file" accept="image/*" style={{ display: 'none' }} onChange={(e) => setAvatarFile(e.target.files[0] || null)} />
                    </label>
                </motion.div>

                <motion.form onSubmit={submit} style={{ marginTop: 16 }} variants={fadeUp}>
                    <div className="form-row">
                        <div className="form-group">
                            <label>Full Name</label>
                            <input className="form-input" name="full_name" value={form.full_name} onChange={handleChange} required />
                        </div>
                        <div className="form-group">
                            <label>Email</label>
                            <input className="form-input" type="email" name="email" value={form.email} onChange={handleChange} required />
                        </div>
                    </div>
                    <motion.button
                        type="submit"
                        className="btn btn-primary"
                        disabled={saving}
                        whileHover={{ scale: 1.02 }}
                        whileTap={{ scale: 0.98 }}
                    >
                        <Save size={16} /> {saving ? 'Saving...' : 'Save Profile'}
                    </motion.button>
                </motion.form>
            </motion.div>
        </motion.div>
    );
}

export default Profile;