import { useState, useEffect } from 'react';
import { Camera, Save, User } from 'lucide-react';
import { motion } from 'framer-motion';
import api from '../../shared/api';

const fallbackMe = { full_name: 'Seller', email: '', bio: '', avatar: null, skills: '', location: '' };

const fadeUp = {
    hidden: { opacity: 0, y: 24 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

const stagger = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.06 } },
};

function Profile({ user }) {
    const [form, setForm] = useState(fallbackMe);
    const [message, setMessage] = useState(null);
    const [saving, setSaving] = useState(false);
    const [avatar, setAvatar] = useState(null);

    useEffect(() => {
        api.get('/seller/profile').then(({ data }) => setForm({ ...fallbackMe, ...data }))
            .catch(() => setForm({ ...fallbackMe, full_name: user.name || 'Seller', email: user.email || '' }));
    }, []);

    const handleChange = (e) => { const { name, value } = e.target; setForm((p) => ({ ...p, [name]: value })); };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setMessage(null);
        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => fd.append(k, String(v || '')));
        if (avatar) fd.append('avatar', avatar);
        try {
            await api.post('/seller/profile', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            setMessage({ type: 'success', text: 'Profile updated.' });
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
                <p>Update your public seller profile.</p>
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

            <motion.div className="settings-wrap profile-page" variants={stagger} initial="hidden" animate="show">
                <motion.div className="profile-cover" variants={fadeUp}>
                    <motion.img
                        className="profile-avatar"
                        src={form.avatar || (avatar ? URL.createObjectURL(avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(form.full_name || 'U')}&background=6d28d9&color=fff`)}
                        alt=""
                        whileHover={{ scale: 1.05 }}
                        transition={{ duration: 0.3 }}
                    />
                    <label className="btn btn-secondary btn-sm" style={{ cursor: 'pointer', alignSelf: 'flex-end' }}>
                        <Camera size={14} /> Change Photo
                        <input type="file" accept="image/*" style={{ display: 'none' }} onChange={(e) => setAvatar(e.target.files[0] || null)} />
                    </label>
                </motion.div>

                <motion.form className="profile-form" onSubmit={submit} style={{ marginTop: 0 }} variants={fadeUp}>
                    <div className="form-row">
                        <div className="form-group">
                            <label>Full Name</label>
                            <input className="form-input" name="full_name" value={form.full_name} onChange={handleChange} required />
                        </div>
                        <div className="form-group">
                            <label>Email</label>
                            <input className="form-input" type="email" name="email" value={form.email} onChange={handleChange} />
                        </div>
                    </div>
                    <div className="form-row">
                        <div className="form-group">
                            <label>Location</label>
                            <input className="form-input" name="location" value={form.location} onChange={handleChange} />
                        </div>
                        <div className="form-group">
                            <label>Skills</label>
                            <input className="form-input" name="skills" value={form.skills} onChange={handleChange} placeholder="e.g. Design, Development" />
                        </div>
                    </div>
                    <div className="form-group">
                        <label>Bio</label>
                        <textarea className="form-input" name="bio" rows="4" value={form.bio} onChange={handleChange} />
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
