import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Camera, Save, User } from 'lucide-react';
import api from '../../shared/api';

const fallbackMe = { full_name: 'Buyer', email: '', avatar: null, company_name: '', location: '' };

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.07 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0, transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] } },
};

function Profile({ user }) {
    const [form, setForm] = useState(fallbackMe);
    const [message, setMessage] = useState(null);
    const [saving, setSaving] = useState(false);
    const [avatar, setAvatar] = useState(null);

    useEffect(() => {
        api.get('/buyer/profile').then(({ data }) => setForm({ ...fallbackMe, ...data }))
            .catch(() => setForm({ ...fallbackMe, full_name: user.name || 'Buyer', email: user.email || '' }));
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
            await api.post('/buyer/profile', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
            setMessage({ type: 'success', text: 'Profile updated.' });
        } catch (err) {
            setMessage({ type: 'error', text: err.response?.data?.message || 'Update failed.' });
        } finally {
            setSaving(false);
        }
    };

    return (
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.3 }}>
            <motion.div className="page-heading" variants={fadeUp} initial="hidden" animate="show">
                <h1><User size={22} style={{ marginRight: 8, verticalAlign: 'middle' }} />My Profile</h1>
                <p>Update your public buyer profile.</p>
            </motion.div>

            {message && <motion.div className={`alert ${message.type === 'success' ? 'alert-success' : 'alert-error'}`} initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>{message.text}</motion.div>}

            <div className="settings-wrap profile-page">
                <motion.div
                    className="profile-cover"
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.1 }}
                    style={{ background: 'linear-gradient(135deg, rgba(109,40,217,0.15), rgba(139,92,246,0.08))' }}
                >
                    <motion.img
                        className="profile-avatar"
                        src={form.avatar || (avatar ? URL.createObjectURL(avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(form.full_name || 'U')}&background=6d28d9&color=fff`)}
                        alt=""
                        initial={{ opacity: 0, scale: 0.7 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.5, delay: 0.2, type: 'spring', stiffness: 200 }}
                    />
                    <label className="btn btn-secondary btn-sm" style={{ cursor: 'pointer', alignSelf: 'flex-end' }}>
                        <Camera size={14} /> Change Photo
                        <input type="file" accept="image/*" style={{ display: 'none' }} onChange={(e) => setAvatar(e.target.files[0] || null)} />
                    </label>
                </motion.div>

                <motion.form className="profile-form" onSubmit={submit} style={{ marginTop: 0 }} variants={container} initial="hidden" animate="show">
                    <motion.div className="form-row" variants={fadeUp}>
                        <div className="form-group">
                            <label>Full Name</label>
                            <input className="form-input" name="full_name" value={form.full_name} onChange={handleChange} required />
                        </div>
                        <div className="form-group">
                            <label>Email</label>
                            <input className="form-input" type="email" name="email" value={form.email} onChange={handleChange} />
                        </div>
                    </motion.div>
                    <motion.div className="form-row" variants={fadeUp}>
                        <div className="form-group">
                            <label>Company Name</label>
                            <input className="form-input" name="company_name" value={form.company_name} onChange={handleChange} />
                        </div>
                        <div className="form-group">
                            <label>Location</label>
                            <input className="form-input" name="location" value={form.location} onChange={handleChange} />
                        </div>
                    </motion.div>
                    <motion.div variants={fadeUp}>
                        <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                            <button type="submit" className="btn btn-primary" disabled={saving}><Save size={16} /> {saving ? 'Saving...' : 'Save Profile'}</button>
                        </motion.div>
                    </motion.div>
                </motion.form>
            </div>
        </motion.div>
    );
}

export default Profile;
